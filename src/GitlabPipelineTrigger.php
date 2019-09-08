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

namespace ErdmannFreunde\ContaoGitlabTriggerBundle;

use Contao\CoreBundle\Exception\InternalServerErrorException;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipeline;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipelineLog;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GitlabPipelineTrigger
{
    public function trigger(int $pipelineId): void
    {
        $pipelineConfig = GitlabPipeline::findByPk($pipelineId);
        if (null === $pipelineConfig) {
            throw new \LogicException("Pipeline config with ID $pipelineId not found!");
        }

        $client = new Client(
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

            $json = json_decode((string)$response->getBody(), true);

            $log = new GitlabPipelineLog();
            $log->setPid((int)$pipelineConfig->id);
            $log->setPipelineId((int)$json['id']);
            $log->updateByApiResponse($json);
        } catch (GuzzleException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }
}
