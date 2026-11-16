# Triploide Toolbox

A Laravel toolkit designed to streamline API development through convention-based architecture and automated code generation.

Triploide Toolbox provides an interactive Artisan command to generate common application components, helping maintain a consistent structure across resources, environments, and application contexts.

## Features

- **Interactive code generation** — Create application components through an interactive Artisan command.
- **Convention-based architecture** — Organize generated classes by environment, context, and resource.
- **Multiple component types** — Generate controllers, dataproviders, filters, policies, repositories, actions, form requests, and API resources.
- **Configurable environments and contexts** — Define the environments and application contexts available to the generator.
- **Existing-file protection** — Generated files are not overwritten if they already exist.
- **Laravel integration** — Automatically registers its service provider through Laravel package discovery.

## Requirements

- PHP 8.3 or higher
- Laravel 13.x

## Installation

Install the package using Composer:

```bash
composer require triploide/toolbox
```

Laravel automatically discovers and registers the package service provider.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=toolbox-config
```

This creates `config/toolbox.php` in your application.

Configure the environments and contexts available to the generator:

```php
<?php

return [
    'enviroments' => [
        'Api',
        'Web',
    ],

    'contexts' => [
        'Admin',
        'Public',
    ],
];
```

Both options support multiple values. The generator will create components for every selected environment and context combination.

## Usage

Run the interactive generator:

```bash
php artisan make:toolbox
```

The command guides you through the following steps:

1. **Resource name** — Enter the resource name (e.g. `User`, `Post`, or `Order`).
2. **Environments** — Select one or more configured environments.
3. **Contexts** — Select one or more configured contexts.
4. **Components** — Choose the components to generate.
5. **Confirmation** — Review your selections and confirm the generation.

The generator creates the corresponding directory structure and PHP classes under your application's `app/` directory.

### Available components

| Component | Generated classes |
|---|---|
| Action | Store, Update, Destroy |
| Controller | Resource Controller |
| Dataprovider | Resource Dataprovider |
| Filter | Resource Filter |
| Policy | Resource Policy |
| Repository | Resource Repository |
| Request | Store, Update |
| Resource | Index, Show |

### Generated structure

For example, selecting the `User` resource, the `Api` environment, the `Admin` context, and all available components produces a structure similar to:

```text
app/
├── Actions/
│   └── Api/
│       └── Admin/
│           └── User/
│               ├── StoreUser.php
│               ├── UpdateUser.php
│               └── DestroyUser.php
├── Dataproviders/
│   └── Api/
│       └── Admin/
│           └── UserDataprovider.php
├── Filters/
│   └── Api/
│       └── Admin/
│           └── UserFilter.php
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── Admin/
│   │           └── UserController.php
│   ├── Requests/
│   │   └── Api/
│   │       └── Admin/
│   │           ├── StoreUserRequest.php
│   │           └── UpdateUserRequest.php
│   └── Resources/
│       └── Api/
│           └── Admin/
│               ├── IndexUserResource.php
│               └── ShowUserResource.php
├── Policies/
│   └── Api/
│       └── Admin/
│           └── UserPolicy.php
└── Repositories/
    └── Api/
        └── Admin/
            └── UserRepository.php
```

**Note:** This is an illustrative structure. The actual output depends on the components selected during generation.

## Code Generation

Triploide Toolbox uses predefined stubs to generate classes.

Each generated class receives the appropriate namespace and class name based on the selected resource, environment, context, and component type.

The generated classes provide a starting point for implementing your application logic. They can be freely customized after generation.

Existing files are preserved: the generator only creates files that do not already exist.

## License

Triploide Toolbox is open-sourced software licensed under the [MIT license](LICENSE.md).

## Author

**Maximiliano Yañez Borghi**

- GitHub: [@triploide](https://github.com/triploide)
- Repository: [triploide/toolbox](https://github.com/triploide/toolbox)