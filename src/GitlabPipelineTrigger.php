<?php


namespace ErdmannFreunde\ContaoGitlabTriggerBundle;


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
                        'variables' => $pipelineConfig->getVariables()
                    ]
                ]
            );
        } catch (GuzzleException $e) {
            // Ignored. Execution will be logged.
        } finally {
            $log = new GitlabPipelineLog();
            $log->setPid($pipelineConfig->id);
            $log->setPipelineId($response['id']);

            $json = json_decode($response->getBody(), true);

            $log->updateLogByApiResponse($json);
        }
    }
}
