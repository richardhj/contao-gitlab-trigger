<?php


namespace ErdmannFreunde\ContaoGitlabTriggerBundle\Model;


use Contao\Model;
use Contao\StringUtil;
use Contao\System;
use Defuse\Crypto\Crypto;

class GitlabPipelineLog extends Model
{
    protected static $strTable = 'tl_gitlab_pipeline_log';

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
        $this->arrData['stattus'] = $status;
    }
}
