<?php

declare(strict_types=1);

/*
 * Contao GitLab Trigger Bundle for Contao Open Source CMS.
 *
 * @copyright  Copyright (c) 2019, Erdmann & Freunde
 * @author     Erdmann & Freunde <https://erdmann-freunde.de/>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/erdmannfreunde/contao-gitlab-trigger
 */

namespace ErdmannFreunde\ContaoGitlabTriggerBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Config\ConfigInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Dependency\DependentPluginInterface;
use ErdmannFreunde\ContaoGitlabTriggerBundle\ErdmannFreundeContaoGitlabTriggerBundle;
use Zeichen32\GitLabApiBundle\Zeichen32GitLabApiBundle;

/**
 * Contao Manager plugin.
 */
class Plugin implements BundlePluginInterface, DependentPluginInterface
{
    /**
     * Gets a list of autoload configurations for this bundle.
     *
     * @param ParserInterface $parser
     *
     * @return ConfigInterface[]
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(Zeichen32GitLabApiBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
            BundleConfig::create(ErdmannFreundeContaoGitlabTriggerBundle::class)
                ->setLoadAfter([Zeichen32GitLabApiBundle::class]),
        ];
    }

    public function getPackageDependencies()
    {
        return [
            'zeichen32/gitlabapibundle',
        ];
    }
}
