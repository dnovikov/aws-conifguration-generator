<?php

namespace App\Resources;


class AwsModel extends AwsResourceAbstract
{
    public function getModelId(): string
    {
        return $this->restApiId . '/' . $this->model['name'];
    }

    public static function getResourceName(): string
    {
        return 'aws_model';
    }

    public function getAwsResourceName(): string
    {
        return 'aws_api_gateway_model';
    }

    public function getApiListMethodName(): string
    {
        return 'getModels';
    }

    public function getImportsFilename(): string
    {
        return 'models';
    }
}
