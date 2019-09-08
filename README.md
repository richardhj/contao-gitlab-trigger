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

We will describe the functionality by example.

In this case, we have a website consisting of two instances (production, stage). The production website is read-only and all changes are made on the stage. When changes are approved on the stage, the production should be overridden.

To override the production, we need to do the following three steps: 1) download database dump from stage and 2) restore database dump on production. Also, 3) sync the `files/` directory between instances.

### `.gitlab-ci.yml`

First we define the abovementioned three steps as jobs.

```yml
# OVERRIDE PRODUCTION
download_database_dump:
  only:
    refs:
      - triggers
    variables:
      - $CTO_CMD == "override_prod"
  variables:
    GIT_STRATEGY: none
  script:
    - export DUMP_FILENAME=$(date '+%F-%H-%M-%S').sql
    - ssh $SSH_USER@$SSH_HOST "cd $SSH_DOCUMENT_ROOT && $PHP vendor/bin/contao-console backup-manager:backup contao local -c gzip --filename $DUMP_FILENAME"
    - mkdir -p sql
    - "scp $SSH_USER@$SSH_HOST:$SSH_DOCUMENT_ROOT/var/sql/$DUMP_FILENAME.gz sql"
    - test -f sql/$DUMP_FILENAME.gz
  artifacts:
    name: db-dump
    paths:
      - "sql/"
  stage: build

restore_database_dump:
  only:
    refs:
      - triggers
    variables:
      - $CTO_CMD == "override_prod"
  stage: deploy
  dependencies:
    - download_database_dump
  variables:
    GIT_STRATEGY: none
  script:
    - export DUMP_FILENAME=$(ls -t sql | head -1)
    - "scp sql/$DUMP_FILENAME $SSH_USER@$SSH_HOST:$SSH_DOCUMENT_ROOT/var/sql/$DUMP_FILENAME"
    - ssh $SSH_USER@$SSH_HOST "cd $SSH_DOCUMENT_ROOT && $PHP vendor/bin/contao-console backup-manager:restore contao local $DUMP_FILENAME -c gzip && $PHP vendor/bin/contao-console contao:database:update"

sync_files:
  only:
    refs:
      - triggers
    variables:
      - $CTO_CMD == "override_prod"
  variables:
    GIT_STRATEGY: none
  stage: deploy
  script:
    - ssh $SSH_USER@$SSH_HOST "rsync -av $SSH_DOCUMENT_ROOT.stage/files/upload/ $SSH_DOCUMENT_ROOT/files/upload"
``` 

The `only` section helps us to only trigger these jobs when we explicitly pass the variable.

If you have any other jobs in your `.gitlab-ci.yml`, you should make sure that they do not run on triggered pipelines:
```yml
deploy_production:
  except:
    - schedules
    - triggers
```

### GitLab configuration

We switch to our GitLab repository and visit the "Pipeline triggers" section under > Settings > CI/CD. We create a new token for our purpose. Give it a recognizable name and save the token.

In order to be able to show the current status of the pipeline being triggered, we need to utilize an API user.

1. Create a new GitLab user that only has access to this particular repository.
2. Switch to User > Settings > Access Tokens and create an access token with API scope. Use the api token within the next step:
3. Add the following config to your `config.yml`:
```yml 
zeichen32_git_lab_api:
  clients:
    firstclient:
      token: secret_token
      url: https://gitlab.com/api/v4/
      auth_method: url_token
```

### Contao configuration

We create a new pipeline config in the Contao back end giving it the name "Override Production".

**Host:** In case you have self-hosted GitLab, enter the uri of the instance. Otherwise it defaults to `https://gitlab.com`.

**Project:** Enter the project ID of the repository. You can find it within the "Pipeline triggers" section or within the general settings section of the repository.

**Token:** The token that we obtained before.

**ref:** The ref can be either branch or tag. The GitLab runner will check out this reference to build the application. As we use `GIT_STRATEGY: none` in all jobs of our example config, the GitLab runner will not check out the repository, making this parameter obsolete. Though this parameter is mandatory, this option defaults to the `master` branch.

**Variables:** These variables will be passed to the pipelines and will help to distinguish between triggered pipelines and normal pipelines. In our example it is important to add the variable `CTO_CMD` = `override_prod`.

### Appendix: Environmental aware database parameters

Sometimes, when overriding the database, some parameters break the website. For instance, when you have set the DNS of the page root to stage.example.com, the production site will become unreachable since the dns setting is wrong.

To fix this, you can create an event listener that makes sure to update the dns accordingly.

```php
<?php


namespace App\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;


class OverrideHostSqlCommandListener
{

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function onSqlCompileCommands(array $sql): array
    {
        if ('usr_p512345_xxx' === $this->connection->getDatabase()) {
            try {
                $sql = $this->addQuery(12, 'www.example.org', $sql);
                $sql = $this->addQuery(123, 'www.example.com', $sql);
            } catch (DBALException $e) {
                // Ignored.
            }
        }

        return $sql;
    }

    /**
     * @param int    $rootId
     * @param string $dns
     * @param array  $sql
     *
     * @return array
     * @throws DBALException
     */
    private function addQuery(int $rootId, string $dns, array $sql): array
    {
        $actualDns = $this->connection->executeQuery('SELECT dns FROM tl_page WHERE id=' . $rootId)->fetchColumn();
        if (false !== $dns && $dns !== $actualDns) {
            $command = "UPDATE tl_page SET dns='$dns' WHERE id=$rootId";

            $sql['ALTER_CHANGE'][md5($command)] = $command;
        }

        return $sql;
    }
}
```

Just register the event listener as sqlCompileCommands hook and run `contao:database:update` (utilizing fuzzyma's bundle) after database recovery. Same procedure applies, if you want to disable all users on the production system.

[ico-version]: https://img.shields.io/packagist/v/erdmannfreunde/contao-gitlab-trigger.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-LGPL-brightgreen.svg?style=flat-square
[ico-dependencies]: https://www.versioneye.com/php/erdmannfreunde:contao-gitlab-trigger/badge.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/erdmannfreunde/contao-gitlab-trigger
[link-dependencies]: https://www.versioneye.com/php/erdmannfreunde:contao-gitlab-trigger
