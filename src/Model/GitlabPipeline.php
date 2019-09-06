<?php


namespace ErdmannFreunde\ContaoGitlabTriggerBundle\Model;


use Contao\Model;
use Contao\StringUtil;
use Contao\System;
use Defuse\Crypto\Crypto;

class GitlabPipeline extends Model
{
    protected static $strTable = 'tl_gitlab_pipeline';

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
     */
    public function getToken(): ?string
    {
        $ciphertext = $this->arrData['token'];
        $key        = System::getContainer()->getParameter('secret');
        $token      = Crypto::decrypt($ciphertext, $key);

        return $token ?? null;
    }

    public function getVariables(): ?array
    {
        return StringUtil::deserialize($this->arrData['host'], true);
    }
}
