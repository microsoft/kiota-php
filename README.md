# Kiota Libraries for PHP

The Kiota libraries provide the essential building blocks for Kiota-generated SDKs based on OpenAPI definitions, offering default implementations for serialization, authentication, and HTTP transport. These libraries are necessary for compiling and running any Kiota-generated project.

To learn more about Kiota, visit the [Kiota repository](https://github.com/microsoft/kiota).

## Build Status

![Build Status](https://github.com/microsoft/kiota-php/actions/workflows/build.yml/badge.svg)

## Libraries

| Library                                                                   | Packagist Release                                                                                                                                        | Changelog                                                      |
|---------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------|
| [Abstractions](./packages/abstractions/README.md)                         | [![Latest Stable Version](https://poser.pugx.org/microsoft/kiota-abstractions/version)](https://packagist.org/packages/microsoft/kiota-abstractions)                 | [Changelog](./packages/abstractions/CHANGELOG.md)              |
| [Authentication - PhpLeague](./packages/authentication/phpleague/README.md)       | [![Latest Stable Version](https://poser.pugx.org/microsoft/kiota-authentication-phpleague/version)](https://packagist.org/packages/microsoft/kiota-authentication-phpleague) | [Changelog](./packages/authentication/phpleague/CHANGELOG.md)      |
| [Http - Guzzle](./packages/http/guzzle/README.md)               | [![Latest Stable Version](https://poser.pugx.org/microsoft/kiota-http-guzzle/version)](https://packagist.org/packages/microsoft/kiota-http-guzzle)                                 | [Changelog](./packages/http/guzzle/CHANGELOG.md)                |
| [Serialization - Json](./packages/serialization/json/README.md)           | [![Latest Stable Version](https://poser.pugx.org/microsoft/kiota-serialization-json/version)](https://packagist.org/packages/microsoft/kiota-serialization-json)     | [Changelog](./packages/serialization/json/CHANGELOG.md)        |
| [Serialization - Form](./packages/serialization/form/README.md)           | [![Latest Stable Version](https://poser.pugx.org/microsoft/kiota-serialization-form/version)](https://packagist.org/packages/microsoft/kiota-serialization-form)      | [Changelog](./packages/serialization/form/CHANGELOG.md)        |
| [Serialization - Text](./packages/serialization/text/README.md)           | [![Latest Stable Version](https://poser.pugx.org/microsoft/kiota-serialization-text/version)](https://packagist.org/packages/microsoft/kiota-serialization-text)     | [Changelog](./packages/serialization/text/CHANGELOG.md)        |
| [Serialization - Multipart](./packages/serialization/multipart/README.md) | [![Latest Stable Version](https://poser.pugx.org/microsoft/kiota-serialization-multipart/version)](https://packagist.org/packages/microsoft/kiota-serialization-multipart)         | [Changelog](./packages/serialization/multipart/CHANGELOG.md)   |
| [Bundle](./packages/bundle/README.md)                                     | [![Latest Stable Version](https://poser.pugx.org/microsoft/kiota-bundle/version)](https://packagist.org/packages/microsoft/kiota-bundle)                             | [Changelog](./packages/bundle/CHANGELOG.md)   |


## Contributing
This project welcomes contributions and suggestions.  Most contributions require you to agree to a
Contributor License Agreement (CLA) declaring that you have the right to, and actually do, grant us
the rights to use your contribution. For details, visit https://cla.opensource.microsoft.com.

When you submit a pull request, a CLA bot will automatically determine whether you need to provide
a CLA and decorate the PR appropriately (e.g., status check, comment). Simply follow the instructions
provided by the bot. You will only need to do this once across all repos using our CLA.

This project has adopted the [Microsoft Open Source Code of Conduct](https://opensource.microsoft.com/codeofconduct/).
For more information see the [Code of Conduct FAQ](https://opensource.microsoft.com/codeofconduct/faq/) or
contact [opencode@microsoft.com](mailto:opencode@microsoft.com) with any additional questions or comments.

Please read our [Contributing](./CONTRIBUTING.md) guidelines to begin contributing to this repository.

## Trademarks

This project may contain trademarks or logos for projects, products, or services. Authorized use of Microsoft
trademarks or logos is subject to and must follow
[Microsoft's Trademark & Brand Guidelines](https://www.microsoft.com/en-us/legal/intellectualproperty/trademarks/usage/general).
Use of Microsoft trademarks or logos in modified versions of this project must not cause confusion or imply Microsoft sponsorship.
Any use of third-party trademarks or logos are subject to those third-party's policies.
