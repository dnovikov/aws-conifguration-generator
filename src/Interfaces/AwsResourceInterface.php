<?php

namespace App\Interfaces;

interface AwsResourceInterface
{
    public function setRestApiId(string $restApiId): self;

    public function setModel(array $model): self;

    public function getModel(): array;

    public function getModelId(): string;

    public function getModelName(): string;

    public function getImportsFilename(): string;

    public function getAwsResourceName(): string;

    public function getApiListMethodName(): string;

    public function getExtraApiRequestParams(): array;

    public function getChildPropertiesPaths(): array;

    public function setParent(AwsResourceInterface $parent): self;

    public function getParent(): AwsResourceInterface|null;

    public static function getResourceName(): string;

    public static function isChildResource(): bool;
}
