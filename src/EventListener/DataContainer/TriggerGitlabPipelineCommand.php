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

namespace Richardhj\ContaoGitlabTriggerBundle\EventListener\DataContainer;

use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\DataContainer;
use Richardhj\ContaoGitlabTriggerBundle\GitlabPipelineTrigger;
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

        $this->pipeline->trigger((int) $dc->id);

        throw new RedirectResponseException($this->router->generate('contao_backend', ['do' => 'gitlab_pipeline_log']));
    }
}
