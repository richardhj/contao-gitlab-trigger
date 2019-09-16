<?php

declare(strict_types=1);

/*
 * Contao GitLab Trigger Bundle for Contao Open Source CMS.
 *
 * @copyright  Copyright (c) 2019, Erdmann & Freunde
 * @author     Erdmann & Freunde <https://erdmann-freunde.de/>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/erdmannfreunde/contao-gitlab-trigger
 */

namespace ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\DataContainer;

use Contao\CoreBundle\Exception\NoContentResponseException;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\Date;
use Contao\Input;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipeline;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipelineLog;
use Gitlab\Client;
use Symfony\Component\HttpFoundation\Response;

class PipelineLogLabelCallback
{
    private $gitlabClient;

    public function __construct(?Client $client)
    {
        $this->gitlabClient = $client;
    }

    public function onLabelCallback(array $row, string $label, $dc, array $args): array
    {
        $pipelineConfig = GitlabPipeline::findByPk($row['pid']);

        $args[0] = sprintf(
            '%s<span class="ci-id">#%s</span><span class="ci-title">%s</span><p class="ci-duration"><img src="bundles/erdmannfreundecontaogitlabtrigger/img/duration.svg">%5$s</p><p class="ci-started"><img src="bundles/erdmannfreundecontaogitlabtrigger/img/calendar.svg">%4$s</p>',
            $this->getBadge($row['status'], $row['web_url']),
            $row['pipeline_id'],
            $pipelineConfig->getName(),
            (new Date($row['created_at']))->date,
            $row['finished_at'] ? date('H:m:s', $row['finished_at'] - $row['started_at']) : '-'
        );

        if (null !== $this->gitlabClient) {
            $GLOBALS['TL_JAVASCRIPT']['ci-refresh'] = 'bundles/erdmannfreundecontaogitlabtrigger/js/ci-refresh.js';
        }

        return $args;
    }

    public function onExecutePreActions(string $action): void
    {
        if (null === $this->gitlabClient || 'update-ci-label' !== $action) {
            return;
        }

        $log = GitlabPipelineLog::findOneBy('pipeline_id', ltrim(Input::postRaw('pipeline'), '#'));
        if (null === $log) {
            throw new NoContentResponseException();
        }

        $pipelineConfig = GitlabPipeline::findByPk($log->getPid());

        $updated = $this->gitlabClient->api('projects')->pipeline($pipelineConfig->getProject(), $log->getPipelineId());

        $log->updateByApiResponse($updated);

        $args = $this->onLabelCallback($log->row(), '', null, []);

        throw new ResponseException(new Response(reset($args)));
    }

    private function getBadge(string $status, string $href): string
    {
        return sprintf(
            '<a href="%2$s" class="ci-status ci-%1$s" target="_blank"><img src="bundles/erdmannfreundecontaogitlabtrigger/img/ci-%1$s.svg"> %3$s</a>',
            $status,
            $href,
            $GLOBALS['TL_LANG']['MSC']['gitlab_ci']['status'][$status] ?? $status
        );
    }
}
