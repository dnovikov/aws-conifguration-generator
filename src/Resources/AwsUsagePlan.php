<?php

namespace App\Resources;


class AwsUsagePlan extends AwsResourceAbstract
{
    public static function getResourceName(): string
    {
        return 'aws_usage_plan';
    }

    public function getAwsResourceName(): string
    {
        return 'aws_api_gateway_usage_plan';
    }

    public function getApiListMethodName(): string
    {
        return 'getUsagePlans';
    }

    public function getImportsFilename(): string
    {
        return 'usage_plans';
    }
}
