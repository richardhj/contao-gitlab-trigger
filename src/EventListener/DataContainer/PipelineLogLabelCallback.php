<?php

declare(strict_types=1);

/*
 * Contao GitLab Trigger Bundle for Contao Open Source CMS.
 *
 * @copyright  Copyright (c) 2020, Richard Henkenjohann
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/richardhj/contao-gitlab-trigger
 */

namespace Richardhj\ContaoGitlabTriggerBundle\EventListener\DataContainer;

use Contao\CoreBundle\Exception\NoContentResponseException;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\Date;
use Contao\Input;
use Gitlab\Client;
use Richardhj\ContaoGitlabTriggerBundle\Model\GitlabPipeline;
use Richardhj\ContaoGitlabTriggerBundle\Model\GitlabPipelineLog;
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
            '%s<span class="ci-id">#%s</span><span class="ci-title">%s</span><p class="ci-started"><img src="bundles/richardhjcontaogitlabtrigger/img/calendar.svg">%4$s</p><p class="ci-duration"><img src="bundles/richardhjcontaogitlabtrigger/img/duration.svg">%5$s</p>',
            $this->getBadge($row['status'], $row['web_url']),
            $row['pipeline_id'],
            $pipelineConfig->getName(),
            (new Date($row['created_at']))->date,
            $row['finished_at'] ? date('H:m:s', $row['finished_at'] - $row['started_at']) : '-'
        );

        if (null !== $this->gitlabClient) {
            $GLOBALS['TL_JAVASCRIPT']['ci-refresh'] = 'bundles/richardhjcontaogitlabtrigger/js/ci-refresh.js';
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
            '<a href="%2$s" class="ci-status ci-%1$s" target="_blank"><img src="bundles/richardhjcontaogitlabtrigger/img/ci-%1$s.svg"> %3$s</a>',
            $status,
            $href,
            $GLOBALS['TL_LANG']['MSC']['gitlab_ci']['status'][$status] ?? $status
        );
    }
}
