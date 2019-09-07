<?php


namespace ErdmannFreunde\ContaoGitlabTriggerBundle\Model;


use Contao\Model;
use Contao\StringUtil;
use Contao\System;
use Defuse\Crypto\Crypto;

class GitlabPipeline extends Model
{
    protected static $strTable = 'tl_gitlab_pipeline';

    public function getName(): ?string
    {
        return $this->arrData['name'] ?? null;
    }

    public function getHost(): ?string
    {
        return $this->arrData['host'] ?? null;
    }

    public function getProject(): ?string
    {
        return $this->arrData['project'] ?? null;
    }

    public function getRef(): ?string
    {
        return $this->arrData['ref'] ?? null;
    }

    /**
     * @return string|null
     *
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     * @throws \Defuse\Crypto\Exception\BadFormatException
     */
    public function getToken(): ?string
    {
        $ciphertext = $this->arrData['token'];
        $secret     = System::getContainer()->getParameter('secret');
        $token      = Crypto::decryptWithPassword($ciphertext, $secret);

        return $token ?? null;
    }

    public function getVariables(): ?array
    {
        $vars = StringUtil::deserialize($this->arrData['variables'], true);
        if ([] === $vars) {
            return null;
        }

        return array_combine(array_column($vars, 'variables_key'), array_column($vars, 'variables_value'));
    }
}
