<?php


namespace ErdmannFreunde\ContaoGitlabTriggerBundle\Model;

use Contao\Model;


class GitlabPipelineLog extends Model
{
    protected static $strTable = 'tl_gitlab_pipeline_log';

    public function getPid(): int
    {
        return $this->arrData['pid'];
    }

    public function getPipelineId(): int
    {
        return $this->arrData['pipeline_id'];

    }

    public function setPid(int $pid): void
    {
        $this->pid = $pid;
    }

    public function setResponse(string $data): void
    {
        $this->response = $data;
    }

    public function setPipelineId(int $id): void
    {
        $this->pipeline_id = $id;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setWebUrl(string $webUrl): void
    {
        $this->web_url = $webUrl;
    }
}
