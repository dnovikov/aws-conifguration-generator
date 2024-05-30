# AWS Configuration Generator

## Contents

1. Problem and motivation
2. Environment
2. Usage
3. Adding new API Gateway resources
4. Configuration structure
5. Possible issues
5. TODO

### Problem and motivation

Infrastructure-as-code (IaC) approach implies configuration is being stored in a repository.
This allows to utilize usual version control functions, like diff checks, versioning etc.
This repository contains a micro PHP framework to generate AWS API Gateway
configuration automatically. Generated JSON files will be placed in the `modules`
directory of this repository.

More configuration types can be added. 

### Environment

[OpenTofu](https://opentofu.org/) is being used as Terraform changed it's licensing and became a paid one.
PHP CLI >= 8.0 is required.

### Usage

1. [Install AWS CLI](https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html)
2. [Store your AWS credentials](https://docs.aws.amazon.com/cli/v1/userguide/cli-configure-files.html)
3. Change the AWS region in either `main.tf` and `src/Services/GenerateImport.php` (this is hard-coded for the moment)
4. Initialize OpenTofu
```
tofu init
```
5. Install dependencies
```
    composer install
```
6. Run the script
```
    php run.php
```
7. Review changes and apply
```
    tofu plan
    tofu apply
```


### Adding new API Gateway resources

Add a new class to `src/Resources`.
The class must extend `AwsResourceAbstract`.

### Configuration structure

OpenTofu assumes there should be `resource` as well as `import` blocks to maintain externally imported resources ([https://opentofu.org/docs/language/import/](https://opentofu.org/docs/language/import/)).
The `import` blocks *must* reside in the root of the project (that's why we're keeping all those `*.tf` files in the root).
The `resource` blocks, however, may be separated into modules. Currently, there is a single `api_gateway` module, described in `main.tf`.

### Possible issues

* The `Attribute endpoint_configuration.0.vpc_endpoint_ids requires 1 item minimum,
   but config has only 0 declared.` error on the *rest_apis* import. You can safely ignore this.
  The `vpc_endpoint_ids` item is removed from the configuration by the script.

### TODO
1. Add [stages](https://registry.terraform.io/providers/hashicorp/aws/latest/docs/resources/api_gateway_stage)
2. Add [documentation parts](https://registry.terraform.io/providers/hashicorp/aws/latest/docs/resources/api_gateway_documentation_part)
3. Add [gateway responses](https://registry.terraform.io/providers/hashicorp/aws/latest/docs/resources/api_gateway_gateway_response)
4. Add [API keys](https://registry.terraform.io/providers/hashicorp/aws/latest/docs/resources/api_gateway_api_key)???
5. Add colored output
6. Add other configuration resources?
