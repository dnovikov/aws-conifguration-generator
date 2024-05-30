<?php

namespace App\Resources;

use CaseConverter\CaseString;


class AwsApiResource extends AwsResourceAbstract
{
    public function getModelId(): string
    {
        return $this->restApiId . '/' . $this->model['id'];
    }

    public static function getResourceName(): string
    {
        return 'aws_resource';
    }

    public function getAwsResourceName(): string
    {
        return 'aws_api_gateway_resource';
    }

    public function getApiListMethodName(): string
    {
        return 'getResources';
    }

    public function getImportsFilename(): string
    {
        return 'resources';
    }

    public function getModelName(): string
    {
        if ($this->model['path'] === '/') {
            return 'root';
        }

        $name = trim($this->model['path'], '/');
        $name = str_replace(['/', '-'], '_', $name);
        $name = str_replace(['{', '}'], '', $name);

        return CaseString::camel($name)->snake();
    }

    public function getExtraApiRequestParams(): array
    {
        return [
            'embed' => ['methods'],
        ];
    }

    public function getChildPropertiesPaths(): array
    {
        return [
            'aws_resource_method' => '$.resourceMethods.*',
        ];
    }
}
