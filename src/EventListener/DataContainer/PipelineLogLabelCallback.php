<?php


namespace ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\DataContainer;


use Contao\CoreBundle\Exception\NoContentResponseException;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\Input;
use ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\UpdatePipelineLogTrait;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipeline;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipelineLog;
use Gitlab\Client;
use Symfony\Component\HttpFoundation\Response;

class PipelineLogLabelCallback
{
    use UpdatePipelineLogTrait;

    private $gitlabClient;

    public function __construct(Client $client)
    {
        $this->gitlabClient = $client;
    }

    public function onLabelCallback(array $row, string $label, $dc, array $args): array
    {
        $pipelineConfig = GitlabPipeline::findByPk($row['pid']);
        $args[0]        = sprintf(
            '%s<span class="ci-id">#%s</span><span class="ci-title">%s</span>',
            $this->getBadge($row['status'], $row['web_url']),
            $row['pipeline_id'],
            $pipelineConfig->getName()
        );

        $this->javascript();

        return $args;
    }

    public function onExecutePreActions(string $action): void
    {
        if ('update-ci-label' !== $action) {
            return;
        }

        $log = GitlabPipelineLog::findOneBy('pipeline_id', Input::post('pipeline'));
        if (null === $log) {
            throw new NoContentResponseException();
        }

        $pipelineConfig = GitlabPipeline::findByPk($log->getPid());

        $updated = $this->gitlabClient->api('projects')->pipeline($pipelineConfig->getProject(), $log->getPipelineId());

        $this->updateLog($log, $updated);

        throw new ResponseException(new Response($this->onLabelCallback($log->row(), '', null, [])));
    }

    private function javascript()
    {
        $GLOBALS['TL_JAVASCRIPT']['ci-update'] = 'bundles/erdmannfreundecontaogitlabtrigger/js/ci-update.js';
    }

    private function getBadge(string $status, string $href): string
    {
        return sprintf(
            '<a href="%2$s" class="ci-status ci-%1$s" target="_blank"><img src="bundles/erdmannfreundecontaogitlabtrigger/img/ci-%1$s.svg"> %1$s</a>',
            $status,
            $href
        );
    }
}
