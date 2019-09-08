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

use ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\DataContainer\PipelineLogLabelCallback;
use ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\DataContainer\TriggerGitlabPipelineCommand;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipeline;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipelineLog;

$GLOBALS['BE_MOD']['system']['gitlab_pipelines'] = [
    'tables' => ['tl_gitlab_pipeline'],
    'run'    => [TriggerGitlabPipelineCommand::class, 'onRun'],
];

$GLOBALS['BE_MOD']['system']['gitlab_pipeline_log'] = [
    'tables' => ['tl_gitlab_pipeline_log'],
];

$GLOBALS['TL_CSS'][] = 'bundles/erdmannfreundecontaogitlabtrigger/scss/ci.scss|static';

$GLOBALS['TL_MODELS']['tl_gitlab_pipeline']     = GitlabPipeline::class;
$GLOBALS['TL_MODELS']['tl_gitlab_pipeline_log'] = GitlabPipelineLog::class;

$GLOBALS['TL_HOOKS']['executePreActions'][] = [PipelineLogLabelCallback::class, 'onExecutePreActions'];
