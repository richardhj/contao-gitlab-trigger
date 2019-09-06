# contao-gitlab-trigger

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Dependency Status][ico-dependencies]][link-dependencies]

Trigger Gitlab Pipelines within Contao.

## Install

Via Composer

``` bash
$ composer require erdmannfreunde/contao-gitlab-trigger
```

## Usage

Run `php vendor/bin/contao-console backup-manager:backup contao local -c gzip --filename test/backup.sql` to create a backup and `php vendor/bin/contao-console backup-manager:restore contao local test/backup.sql.gz -c gzip` to restore from a backup.

The dumps will saved under `var/sql/`.

[ico-version]: https://img.shields.io/packagist/v/erdmannfreunde/contao-gitlab-trigger.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-LGPL-brightgreen.svg?style=flat-square
[ico-dependencies]: https://www.versioneye.com/php/richardhj:contao-backup-manager/badge.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/erdmannfreunde/contao-gitlab-trigger
[link-dependencies]: https://www.versioneye.com/php/richardhj:contao-backup-manager
