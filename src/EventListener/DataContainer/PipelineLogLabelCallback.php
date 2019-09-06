<?php


namespace ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\DataContainer;


use Contao\DataContainer;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipeline;

class PipelineLogLabelCallback
{

    public function onLabelCallback(array $row, string $label, DataContainer $dc, array $args): array
    {
        $pipelineConfig = GitlabPipeline::findByPk($row['pid']);
        $args[0]        = sprintf(
            '%s<span class="ci-id">#%s</span><span class="ci-title">%s</span>',
            $this->getBadge($row['status'], $row['web_url']),
            $label,
            $pipelineConfig->getName()
        );

        return $args;
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
