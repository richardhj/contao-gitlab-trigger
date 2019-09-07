<?php


namespace ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\DataContainer;

use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\DataContainer;
use ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\UpdatePipelineLogTrait;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipeline;
use ErdmannFreunde\ContaoGitlabTriggerBundle\Model\GitlabPipelineLog;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Routing\RouterInterface;


class TriggerGitlabPipelineCommand
{
    use UpdatePipelineLogTrait;

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


            $log = new GitlabPipelineLog();
            $log->setPid($pipelineConfig->id);
            $log->setPipelineId($response['id']);

            $json = json_decode($response->getBody(), true);

            $this->updateLog($log, $json);
        } catch (GuzzleException $e) {
            // Ignored, response will be logged.
        }

        throw new RedirectResponseException(
            $this->router->generate('contao_backend', ['do' => 'gitlab_pipeline_log'])
        );
    }
}
