<?php


namespace ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\DataContainer;

use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\DataContainer;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipeline;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipelineLog;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Routing\RouterInterface;


class TriggerGitlabPipelineCommand
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onRun(DataContainer $dc): string
    {
        if (!$dc->id) {
            return '';
        }

        $pipelineConfig = GitlabPipeline::findByPk($dc->id);
        if (null === $pipelineConfig) {
            return '';
        }

        $client = new Client(
            [
                'base_uri' => $pipelineConfig->getHost() ?: 'https://gitlab.com',
            ]
        );

        try {
            $response = $client->request(
                'POST',
                sprintf('api/v4/projects/%s/trigger/pipeline', $pipelineConfig->getProject()),
                [
                    'form_params' => [
                        'ref'       => $pipelineConfig->getRef(),
                        'token'     => $pipelineConfig->getToken(),
                        'variables' => $pipelineConfig->getVariables()
                    ]
                ]
            );

            $json = json_decode($response->getBody(), true);

            $log = new GitlabPipelineLog();
            $log->setPid($pipelineConfig->id);
            $log->setResponse($response->getBody());
            $log->setPipelineId($json['id']);
            $log->setStatus($json['status']);
            $log->setWebUrl($json['web_url']);
            foreach (['created_at', 'updated_at', 'started_at', 'finished_at'] as $k) {
                $log->$k = strtotime($json[$k]) ?: null;
            }

            $log->save();

        } catch (GuzzleException $e) {
            // Ignored, response will be logged.
        }

        throw new RedirectResponseException(
            $this->router->generate('contao_backend', ['do' => 'gitlab_pipeline_log'])
        );
    }
}
