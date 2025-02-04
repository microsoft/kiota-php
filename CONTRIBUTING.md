# Contributing to Kiota PHP

Kiota PHP is a mono-repo containing source code for the following packages:

- ['microsoft/kiota-abstractions'](https://packagist.org/packages/microsoft/kiota-abstractions)
- ['microsoft/kiota-http-guzzle'](https://packagist.org/packages/microsoft/kiota-http-guzzle)
- ['microsoft/kiota-authentication-phpleague'](https://packagist.org/packages/microsoft/kiota-authentication-phpleague)
- ['microsoft/kiota-bundle']()
- ['microsoft/kiota-serialization-multipart'](https://packagist.org/packages/microsoft/kiota-serialization-multipart)
- ['microsoft/kiota-serialization-text'](https://packagist.org/packages/microsoft/kiota-serialization-text)
- ['microsoft/kiota-serialization-json'](https://packagist.org/packages/microsoft/kiota-serialization-json)
- ['microsoft/kiota-serialization-form'](https://packagist.org/packages/microsoft/kiota-serialization-form)

Kiota Java is open to contributions. There are a couple of different recommended paths to get contributions into the released version of this library.

__NOTE__ A signed a contribution license agreement is required for all contributions, and is checked automatically on new pull requests. Please read and sign [the agreement](https://cla.microsoft.com/) before starting any work for this repository.

## File issues

The best way to get started with a contribution is to start a dialog with the owners of this repository. Sometimes features will be under development or out of scope for this SDK and it's best to check before starting work on contribution. Discussions on bugs and potential fixes could point you to the write change to make.

## Submit pull requests for bug fixes and features

Feel free to submit a pull request with a linked issue against the __main__ branch.  The main branch will be updated frequently.

## Commit message format

To support our automated release process, pull requests are required to follow the [Conventional Commit](https://www.conventionalcommits.org/en/v1.0.0/)
format.

Each commit message consists of a **header**, an optional **body** and an optional **footer**. The header is the first line of the commit and
MUST have a **type** (see below for a list of types) and a **description**. An optional **scope** can be added to the header to give extra context.

```
<type>[optional scope]: <short description>
<BLANK LINE>
<optional body>
<BLANK LINE>
<optional footer(s)>
```

The recommended commit types used are:

 - **feat** for feature updates (increments the _minor_ version)
 - **fix** for bug fixes (increments the _patch_ version)
 - **perf** for performance related changes e.g. optimizing an algorithm
 - **refactor** for code refactoring changes
 - **test** for test suite updates e.g. adding a test or fixing a test
 - **style** for changes that don't affect the meaning of code. e.g. formatting changes
 - **docs** for documentation updates e.g. ReadMe update or code documentation updates
 - **build** for build system changes (gradle updates, external dependency updates)
 - **ci** for CI configuration file changes e.g. updating a pipeline
 - **chore** for miscallaneous non-sdk changesin the repo e.g. removing an unused file

Adding a an exclamation mark after the commit type (`feat!`) or footer with the prefix **BREAKING CHANGE:** will cause an increment of the _major_ version.
