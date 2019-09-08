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

namespace ErdmannFreunde\ContaoGitlabTriggerBundle\ContaoTrigger\Action;

use EBlick\ContaoTrigger\Component\Action\ActionInterface;
use EBlick\ContaoTrigger\DataContainer\DataContainerComponentInterface;
use EBlick\ContaoTrigger\DataContainer\Definition;
use EBlick\ContaoTrigger\Execution\ExecutionContext;
use ErdmannFreunde\ContaoGitlabTriggerBundle\GitlabPipelineTrigger;

class TriggerGitlabPipelineAction implements ActionInterface, DataContainerComponentInterface
{
    private $pipeline;

    public function __construct(GitlabPipelineTrigger $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    public function fire(ExecutionContext $context, array $rawData): bool
    {
        $trigger = $context->getParameters();

        $this->pipeline->trigger($trigger->act_gitlab_pipeline_id);

        return true;
    }

    public function getDataContainerDefinition(): Definition
    {
        $palette = 'act_gitlab_pipeline_id';

        $fields = [
            'act_gitlab_pipeline_id' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_eblick_trigger']['action_gitlab_pipeline_id'],
                'inputType'  => 'select',
                'exclude'    => true,
                'foreignKey' => 'tl_gitlab_pipeline.name',
                'eval'       => [
                    'includeBlankOption' => true,
                    'mandatory'          => true,
                ],
                'sql'        => "int(10) unsigned NOT NULL default '0'",
            ],
        ];

        return new Definition($fields, $palette);
    }
}
