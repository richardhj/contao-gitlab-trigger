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

        $config = GitlabPipeline::findByPk($dc->id);
        if (null === $config) {
            return '';
        }

        $client = new Client(
            [
                'base_uri' => $config->getHost() ?: 'https://gitlab.com',
            ]
        );

        try {
            $response = $client->request(
                'POST',
                sprintf('api/v4/projects/%s/trigger/pipeline', $config->getProject()),
                [
                    'form_params' => [
                        'ref'       => $config->getRef(),
                        'token'     => $config->getToken(),
                        'variables' => $config->getVariables()
                    ]
                ]
            );

            $json = json_decode($response->getBody(), true);

            $log = new GitlabPipelineLog();
            $log->setResponse($response->getBody());
            $log->setPipelineId($json['id']);
            $log->setStatus($json['status']);
            foreach (['created_at', 'updated_at', 'started_at', 'finished_at'] as $k) {
                $log->$k = $json[$k] ?: null;
            }

            $log->save();

            if (404 === $response->getStatusCode()) {
                // Project id or token invalid.
            }

            throw new RedirectResponseException(
                $this->router->generate('contao_backend', ['do' => 'gitlab_pipeline_log'])
            );

        } catch (GuzzleException $e) {
            return '';
        }
    }
}
