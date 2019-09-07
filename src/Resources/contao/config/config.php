<?php

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
