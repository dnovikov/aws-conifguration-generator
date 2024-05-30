<?php

namespace App\Resources;


class AwsRestApi extends AwsResourceAbstract
{
    public static function getResourceName(): string
    {
        return 'aws_rest_api';
    }

    public function getAwsResourceName(): string
    {
        return 'aws_api_gateway_rest_api';
    }

    public function getApiListMethodName(): string
    {
        return 'getRestApis';
    }

    public function getImportsFilename(): string
    {
        return 'rest_apis';
    }
}
