# Release Automation Workflow

This document summarizes the process of releasing changes made within this repository to Packagist.

## How publishing PHP packages works

Packagist is PHP's official package repository. We currently publish Microsoft Kiota's packages on Packagist.

Packagist leverages Composer, a CLI tool used to configure PHP projects:

- installs dependencies from Packagist
- configuring PHP repositories as packages
among others.

According to Composer, any folder with a `composer.json` is considered a package.

Packagist has an integration with GitHub repositories that we leverage. By installing the Packagist GitHub
app on our repositories, Packagist is able to read tags and new branches on the repositories when they are created.

Although any folder with a `composer.json` is considered a package, Packagist only reads the `composer.json` in the root
of a repository. The impact of this is that with this mono-repo setup, Packagist can only read tags & composer.json from the individual package repositories from each project e.g. [Kiota Abstractions PHP](https://github.com/microsoft/kiota-abstractions-php), [Kiota HTTP Guzzle PHP](https://github.com/microsoft/kiota-http-guzzle-php) etc.

This means that any changes to this mono-repo need to be cascaded to the individual repositories and tagged in the individual repositories for updates to reach Packagist.

Packagist also reads the `version` property in `composer.json` files to determine the package version.

This mono-repo currently uses the `version` property: For the sub-projects to take a dependency on each other locally,
we configure a local `path` repository in each `composer.json` to point each package to the local dependencies within this project. This tells Composer to try to resolve each dependency locally first before hitting Packagist. For the local setup,
Composer relies on the `version` tag in the sub-projects' `composer.json` to determine what version the local package is.

To read more on Packagist's integration with GitHub and versioning:

- https://packagist.org/about
- https://packagist.org/about

## Versioning the mono-repo

This mono-repo uses [Release Please](https://github.com/googleapis/release-please) to manage the versioning, tagging and updates to CHANGELOG files.

Release please determines the next version of the packages by scanning for [conventional commit messages](https://www.conventionalcommits.org/en/v1.0.0/) in the git history on the default branch (`main` in our case).

The Release Please configuration in this mono-repo ensures that:

- for each change, each CHANGELOG in the sub-projects is updated
- the new version to bump each sub-project to is set via updating the `release-as` property in each sub-project's `release-please-config.json`
- the `version` property in each `composer.json` is bumped accordingly as well as the local dependency versions in the `require` properties

Effectively, by updating the `release-as` property, when the changes to each sub-project are merged to their individual repositories default branches, Release Please on the individual repositories will create a Release PR using the new version.

`release-as` is used since at times, a code change may only be made to one package e.g. abstractions but all the other packages need to be bumped. When splitting changes to the individual repositories, an unchanged package e.g. HTTP would only contain a new commit updating its CHANGELOG but not a conventional commit message that would bump its version accordingly. `release-as` allows to control the new release version regardless of the conventional commit messages available at the time.

The `release-as` property is bumped regularly via this mono-repo's config


## Splitting changes from this Mono-repo to individual repositories

This mono-repo uses a workflow to split the commits affecting sub-projects using `git subtree split`
This ensures only commits that affected each sub-project are pushed to the individual repositories.

This Mono-repo's workflows get access to push to a new branch in the individual repositories by using a
GitHub app to generate an app installation token that it uses to authorize the request to push.
