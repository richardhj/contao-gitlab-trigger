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

namespace Richardhj\ContaoGitlabTriggerBundle;

use Contao\CoreBundle\Exception\InternalServerErrorException;
use Defuse\Crypto\Exception\CryptoException;
use Gitlab\Client as GitlabClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Richardhj\ContaoGitlabTriggerBundle\Model\GitlabPipeline;
use Richardhj\ContaoGitlabTriggerBundle\Model\GitlabPipelineLog;

class GitlabPipelineTrigger
{
    private $gitlabClient;

    public function __construct(?GitlabClient $client)
    {
        $this->gitlabClient = $client;
    }

    public function trigger(int $pipelineId): void
    {
        $pipelineConfig = GitlabPipeline::findByPk($pipelineId);
        if (null === $pipelineConfig) {
            throw new \LogicException("Pipeline config with ID $pipelineId not found!");
        }

        $client = new GuzzleClient(
            [
                'base_uri' => $pipelineConfig->getHost() ?: 'https://gitlab.com',
            ]
        );

        try {
            $response = $client->request(
                'POST',
                sprintf('api/v4/projects/%s/trigger/pipeline', $pipelineConfig->getProject()),
                [
                    'form_params' => [
                        'ref'       => $pipelineConfig->getRef(),
                        'token'     => $pipelineConfig->getToken(),
                        'variables' => $pipelineConfig->getVariables(),
                    ],
                ]
            );

            $json = json_decode((string) $response->getBody(), true);

            $log = new GitlabPipelineLog();
            $log->setPid((int) $pipelineConfig->id);
            $log->setPipelineId((int) $json['id']);
            $log->updateByApiResponse($json);

            // If we do not have a GitLab client, we cannot update the pipeline status. As a result, we use an alternative status here
            if (null === $this->gitlabClient) {
                $log->setStatus('triggered');
                $log->save();
            }
        } catch (GuzzleException | CryptoException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }
}
