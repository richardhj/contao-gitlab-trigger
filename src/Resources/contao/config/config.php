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

use Richardhj\ContaoGitlabTriggerBundle\EventListener\DataContainer\PipelineLogLabelCallback;
use Richardhj\ContaoGitlabTriggerBundle\EventListener\DataContainer\TriggerGitlabPipelineCommand;
use Richardhj\ContaoGitlabTriggerBundle\Model\GitlabPipeline;
use Richardhj\ContaoGitlabTriggerBundle\Model\GitlabPipelineLog;

$GLOBALS['BE_MOD']['system']['gitlab_pipelines'] = [
    'tables' => ['tl_gitlab_pipeline'],
    'run'    => [TriggerGitlabPipelineCommand::class, 'onRun'],
];

$GLOBALS['BE_MOD']['system']['gitlab_pipeline_log'] = [
    'tables' => ['tl_gitlab_pipeline_log'],
];

$GLOBALS['TL_CSS'][] = 'bundles/richardhjcontaogitlabtrigger/scss/ci.scss|static';

$GLOBALS['TL_MODELS']['tl_gitlab_pipeline']     = GitlabPipeline::class;
$GLOBALS['TL_MODELS']['tl_gitlab_pipeline_log'] = GitlabPipelineLog::class;

$GLOBALS['TL_HOOKS']['executePreActions'][] = [PipelineLogLabelCallback::class, 'onExecutePreActions'];
