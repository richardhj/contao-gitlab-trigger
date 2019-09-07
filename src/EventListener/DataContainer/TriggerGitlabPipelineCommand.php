<?php


namespace ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\DataContainer;

use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\DataContainer;
use ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\UpdatePipelineLogTrait;
use ErdmannFreunde\ContaoGitlabTriggerBundle\GitlabPipelineTrigger;
use Symfony\Component\Routing\RouterInterface;


class TriggerGitlabPipelineCommand
{

    private $router;

    private $pipeline;

    public function __construct(GitlabPipelineTrigger $pipelineTrigger, RouterInterface $router)
    {
        $this->pipeline = $pipelineTrigger;
        $this->router   = $router;
    }

    public function onRun(DataContainer $dc): string
    {
        if (!$dc->id) {
            return '';
        }

        $this->pipeline->trigger($dc->id);

        throw new RedirectResponseException($this->router->generate('contao_backend', ['do' => 'gitlab_pipeline_log']));
    }
}
