<?php

namespace App\Resources;


class AwsApiResourceIntegration extends AwsResourceAbstract
{
    public function getModelId(): string
    {
        return $this->getParent()->getModelId();
    }

    public static function getResourceName(): string
    {
        return 'aws_resource_integration';
    }

    public function getAwsResourceName(): string
    {
        return 'aws_api_gateway_integration';
    }

    public function getApiListMethodName(): string
    {
        return 'getIntegration';
    }

    public function getImportsFilename(): string
    {
        return $this->getParent()->getImportsFilename();
    }

    public function getModelName(): string
    {
        return $this->getParent()->getModelName();
    }

    public function getExtraApiRequestParams(): array
    {
        return [
            'httpMethod' => $this->model['httpMethod'],
            'resourceId' => $this->getParent()->getParent()->getModel()['id'],
        ];
    }

    public function getChildPropertiesPaths(): array
    {
        return [
            'aws_resource_integration_response' => '$.integrationResponses.*',
        ];
    }

    public static function isChildResource(): bool
    {
        return true;
    }
}
