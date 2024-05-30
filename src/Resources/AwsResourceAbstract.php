<?php

namespace App\Resources;

use CaseConverter\CaseString;
use App\Interfaces\AwsResourceInterface;


abstract class AwsResourceAbstract implements AwsResourceInterface
{
    protected array $model = [];

    protected string $restApiId = '';

    protected AwsResourceInterface|null $parent;

    public function setRestApiId(string $restApiId): self
    {
        $this->restApiId = $restApiId;

        return $this;
    }

    public function setModel(array $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): array
    {
        return $this->model;
    }

    public function getModelId(): string
    {
        return $this->model['id'];
    }

    public function getModelName(): string
    {
        $name = preg_replace_callback('/([[:upper:]])([[:upper:]]+)/', function ($match) {
            return $match[1] . strtolower($match[2]);
        }, $this->model['name']);

        return str_replace(' ', '', CaseString::camel($name)->snake());
    }

    public function getExtraApiRequestParams(): array
    {
        return [];
    }

    public function getChildPropertiesPaths(): array
    {
        return [];
    }

    public function setParent(AwsResourceInterface $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent(): AwsResourceInterface|null
    {
        return $this->parent;
    }

    public static function isChildResource(): bool
    {
        return false;
    }

    abstract public function getImportsFilename(): string;

    abstract public function getAwsResourceName(): string;

    abstract public function getApiListMethodName(): string;

    abstract public static function getResourceName(): string;

}
