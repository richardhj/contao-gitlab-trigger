<?php


namespace ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener;


use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipelineLog;

trait UpdatePipelineLogTrait
{
    private function updateLog(GitlabPipelineLog $log, array $response): void
    {
        $log->setResponse(json_encode($response));
        $log->setStatus($response['status']);
        $log->setWebUrl($response['web_url']);

        foreach (['created_at', 'updated_at', 'started_at', 'finished_at'] as $k) {
            $log->$k = strtotime($response[$k]) ?: null;
        }

        $log->save();
    }
}
