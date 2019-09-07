<?php


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
        $palette = 'act_gitlab_pipeline';

        $fields = [
            'act_gitlab_pipeline_id' => [
                'inputType'  => 'select',
                'foreignKey' => 'tl_gitlab_pipeline.name',
                'sql'        => "int(10) unsigned NOT NULL default '0'"
            ]
        ];

        return new Definition($fields, $palette);
    }
}
