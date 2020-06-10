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

namespace Richardhj\ContaoGitlabTriggerBundle\Model;

use Contao\Model;

class GitlabPipelineLog extends Model
{
    protected static $strTable = 'tl_gitlab_pipeline_log';

    public function getPid(): int
    {
        return (int) $this->arrData['pid'];
    }

    public function getPipelineId(): int
    {
        return (int) $this->arrData['pipeline_id'];
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

    public function updateByApiResponse(array $response): void
    {
        $this->setResponse(json_encode($response));
        $this->setStatus($response['status']);
        $this->setWebUrl($response['web_url']);

        foreach (['created_at', 'updated_at', 'started_at', 'finished_at'] as $k) {
            $this->$k = $response[$k] ? strtotime($response[$k]) : null;
        }

        $this->save();
    }
}
