<?php

namespace App\Resources;


class AwsApiResourceMethodResponse extends AwsResourceAbstract
{
    public function getModelId(): string
    {
        return $this->getParent()->getModelId() . '/' . $this->model['statusCode'];
    }

    public static function getResourceName(): string
    {
        return 'aws_resource_method_response';
    }

    public function getAwsResourceName(): string
    {
        return 'aws_api_gateway_method_response';
    }

    public function getApiListMethodName(): string
    {
        return 'getMethodResponse';
    }

    public function getImportsFilename(): string
    {
        return $this->getParent()->getImportsFilename();
    }

    public function getModelName(): string
    {
        return $this->getParent()->getModelName() . '_' . strtolower($this->model['statusCode']);
    }

    public function getExtraApiRequestParams(): array
    {
        return [
            'httpMethod' => $this->getParent()->getModel()['httpMethod'],
            'resourceId' => $this->getParent()->getParent()->getModel()['id'],
            'statusCode' => $this->model['statusCode'],
        ];
    }

    public static function isChildResource(): bool
    {
        return true;
    }
}
