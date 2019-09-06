<?php

use ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\DataContainer\TriggerGitlabPipelineCommand;

$GLOBALS['BE_MOD']['system']['gitlab_pipelines'] = [
    'tables' => ['tl_gitlab_pipeline'],
    'run'    => [TriggerGitlabPipelineCommand::class, 'onRun'],
];

$GLOBALS['BE_MOD']['system']['gitlab_pipeline_log'] = [
    'tables' => ['tl_gitlab_pipeline_log'],
];
