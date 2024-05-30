<?php

namespace App\Services;

use Aws\ApiGateway\ApiGatewayClient;
use Remorhaz\JSON\Data\Value\EncodedJson;
use Remorhaz\JSON\Path\Processor\Processor;
use Remorhaz\JSON\Path\Query\QueryFactory;
use Remorhaz\JSON\Path\Query\Exception\QueryExecutionFailedException;
use App\Interfaces\AwsResourceInterface;


class GenerateImport
{
    private string|null $restApiId;

    private ApiGatewayClient|null $apiGatewayClient;

    private array $resources = [];

    const PROFILE = 'default';

    const REGION = 'us-east-2';

    const API_VERSION = '2015-07-09';

    const RESOURCES_PATH = __DIR__ . '/../Resources';

    public function __construct(string $restApiId)
    {
        $this->apiGatewayClient = new ApiGatewayClient([
            'profile' => self::PROFILE,
            'region' => self::REGION,
            'version' => self::API_VERSION,
        ]);

        $this->restApiId = $restApiId;
    }

    public function setResourceId(string $restApiId): self
    {
        $this->restApiId = $restApiId;

        return $this;
    }

    private function generateResources($fh, AwsResourceInterface $resource): void
    {
        $methodName = $resource->getApiListMethodName();
        $position = null;
        $params = [
            'restApiId' => $this->restApiId,
        ];
        do {
            $params['position'] = $position;

            $params += $resource->getExtraApiRequestParams();

            $response = $this->apiGatewayClient->{$methodName}($params);

            $items = $response['items'] ?? [$response->toArray()];
            foreach ($items as $model) {
                $resource->setModel($model);

                $id = $resource->getModelId();
                $awsName = $resource->getAwsResourceName();
                $name = $resource->getModelName();

                $output = <<<EOT
import {
  id = "$id"
  to = $awsName.$name
}

EOT;
                fwrite($fh, $output);

                echo "Generating import section for $awsName.$name\n";

                $childResourcesPaths = $resource->getChildPropertiesPaths();
                if (!empty($childResourcesPaths)) {
                    $processor = Processor::create();
                    $queryFactory = QueryFactory::create();
                    $decodedValueFactory = EncodedJson\NodeValueFactory::create();
                    $jsonDocument = $decodedValueFactory->createValue(json_encode($model));

                    foreach ($childResourcesPaths as $resourceName => $resourcePath) {
                        $jsonQuery = $queryFactory->createQuery($resourcePath);

                        $result = [];
                        try {
                            $result = $processor
                                ->select($jsonQuery, $jsonDocument)
                                ->decode();
                        }
                        catch (QueryExecutionFailedException $e) {
                            // Do nothing.
                        }

                        if (!empty($result)) {
                            foreach ($result as $item) {
                                $childResource = $this->createResourceFromName($resourceName)
                                    ->setRestApiId($this->restApiId)
                                    ->setParent($resource)
                                    ->setModel((array) $item);

                                $this->generateResources($fh, $childResource);
                            }
                        }
                    }
                }
            }

            $position = $response['position'];
        } while ($position);
    }

    public function loadResources(): self
    {
        $resources = [];

        $files = scandir(self::RESOURCES_PATH, SCANDIR_SORT_ASCENDING);
        foreach ($files as $file) {
            // Ignore directories and abstract classes.
            if (is_dir($file) || stripos($file, 'Abstract') !== false) {
                continue;
            }

            // Get the name of the file without the suffix.
            $file = explode('.', $file);
            $class = '\App\Resources\\' . $file[0];

            $name = $class::getResourceName();

            $resources[$name] = $class;
        }

        $this->resources = $resources;

        return $this;
    }

    private function createResourceFromName($resourceName)
    {
        if (isset($this->resources[$resourceName])) {
            return new $this->resources[$resourceName]();
        }

        return null;
    }

    public function generate(): self
    {
        $importFiles = [];
        $resources = [];

        foreach ($this->resources as $resourceName => $className) {
            if ($className::isChildResource()) {
                continue;
            }

            $resource = $this->createResourceFromName($resourceName);

            $fullImportsFilename = realpath($resource->getImportsFilename() . '.tf');
            if (file_exists($fullImportsFilename)) {
                unlink($fullImportsFilename);
            }

            $resources[$resourceName] = $resource;
        }

        foreach ($resources as $resourceName => $resource) {
            $resource->setRestApiId($this->restApiId);

            $importsFilename = $resource->getImportsFilename();
            $fullImportsFilename = realpath('.') . '/' . $importsFilename . '.tf';
            $fh = fopen($fullImportsFilename, 'w');
            $this->generateResources($fh, $resource);
            fclose($fh);

            $generatedFilename = realpath('.') . '/generated.tf';
            if (file_exists($generatedFilename)) {
                unlink($generatedFilename);
            }

            echo  "Generating OpenTofu resources for $importsFilename.\n";

            if (exec('tofu plan -generate-config-out=generated.tf') !== false) {
                // Workaround for the 'Attribute endpoint_configuration.0.vpc_endpoint_ids requires 1 item minimum' error,
                // as we do not use VPC currently.
                if ($resourceName === 'aws_rest_api') {
                    $contents = file_get_contents($generatedFilename);
                    $contents = str_replace("\x20\x20\x20\x20vpc_endpoint_ids = []\n", '', $contents);
                    file_put_contents($generatedFilename, $contents);
                }
                rename($generatedFilename, realpath('.') . '/modules/api-gateway/' . $importsFilename . '.tf');

                $contents = file_get_contents($fullImportsFilename);
                $contents = str_replace('to = ', 'to = module.api_gateway.', $contents);
                file_put_contents($fullImportsFilename, $contents);
                $importsFilenameRenamed = $fullImportsFilename . '.tmp';
                rename($fullImportsFilename, $importsFilenameRenamed);
                $importFiles[] = $importsFilenameRenamed;

                echo  "Resources for $importsFilename generated.\n";
            }
        }

        foreach ($importFiles as $filename) {
            $newFilename = str_replace('.tmp', '', $filename);
            rename($filename, $newFilename);
        }

        return $this;
    }

}
