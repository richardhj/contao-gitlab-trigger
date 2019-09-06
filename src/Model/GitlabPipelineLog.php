<?php


namespace ErdmannFreunde\ContaoGitlabTriggerBundle\Model;

use Contao\Model;


class GitlabPipelineLog extends Model
{
    protected static $strTable = 'tl_gitlab_pipeline_log';

    public function setPid(int $pid): void
    {
        $this->arrData['pid'] = $pid;
    }

    public function setResponse(string $data): void
    {
        $this->arrData['response'] = $data;
    }

    public function setPipelineId(int $id): void
    {
        $this->arrData['pipeline_id'] = $id;
    }

    public function setStatus(string $status): void
    {
        $this->arrData['status'] = $status;
    }

    public function setWebUrl(string $webUrl): void
    {
        $this->arrData['web_url'] = $webUrl;
    }
}
