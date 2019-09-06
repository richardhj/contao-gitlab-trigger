<?php

/**
 * This file is part of erdmannfreunde/contao-gitlab-trigger.
 *
 * Copyright (c) 2019-2019 Erdmann & Freunde
 *
 * @package   erdmannfreunde/contao-gitlab-trigger
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2018-2018 Richard Henkenjohann
 * @license   https://github.com/erdmannfreunde/contao-gitlab-trigger/blob/master/LICENSE LGPL-3.0
 */

namespace ErdmannFreunde\ContaoGitlabTriggerBundle\ContaoManager;

use BM\BackupManagerBundle\BMBackupManagerBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Config\ConfigInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Contao\ManagerPlugin\Dependency\DependentPluginInterface;
use ErdmannFreunde\ContaoGitlabTriggerBundle\ErdmannFreundeContaoGitlabTriggerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
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
