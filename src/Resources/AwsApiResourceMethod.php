<?php

namespace App\Resources;


class AwsApiResourceMethod extends AwsResourceAbstract
{
    public function getModelId(): string
    {
        return $this->getParent()->getModelId() . '/' . $this->model['httpMethod'];
    }

    public static function getResourceName(): string
    {
        return 'aws_resource_method';
    }

    public function getAwsResourceName(): string
    {
        return 'aws_api_gateway_method';
    }

    public function getApiListMethodName(): string
    {
        return 'getMethod';
    }

    public function getImportsFilename(): string
    {
        return $this->getParent()->getImportsFilename();
    }

    public function getModelName(): string
    {
        return $this->getParent()->getModelName() . '_' . strtolower($this->model['httpMethod']);
    }

    public function getExtraApiRequestParams(): array
    {
        return [
            'httpMethod' => $this->model['httpMethod'],
            'resourceId' => $this->getParent()->getModel()['id'],
        ];
    }

    public function getChildPropertiesPaths(): array
    {
        return [
            'aws_resource_method_response' => '$.methodResponses.*',
            'aws_resource_integration' => '$.methodIntegration',
        ];
    }

    public static function isChildResource(): bool
    {
        return true;
    }
}
