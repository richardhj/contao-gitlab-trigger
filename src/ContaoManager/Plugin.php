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

namespace Richardhj\ContaoGitlabTriggerBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Config\ConfigInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Dependency\DependentPluginInterface;
use Richardhj\ContaoGitlabTriggerBundle\RichardhjContaoGitlabTriggerBundle;
use Zeichen32\GitLabApiBundle\Zeichen32GitLabApiBundle;

/**
 * Contao Manager plugin.
 */
class Plugin implements BundlePluginInterface, DependentPluginInterface
{
    /**
     * Gets a list of autoload configurations for this bundle.
     *
     * @return ConfigInterface[]
     */
    public function getBundles(ParserInterface $parser): array
    {
        if (class_exists(Zeichen32GitLabApiBundle::class)) {
            return [
                BundleConfig::create(Zeichen32GitLabApiBundle::class)
                    ->setLoadAfter([ContaoCoreBundle::class]),
                BundleConfig::create(RichardhjContaoGitlabTriggerBundle::class)
                    ->setLoadAfter([Zeichen32GitLabApiBundle::class]),
            ];
        }

        return [
            BundleConfig::create(RichardhjContaoGitlabTriggerBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }

    public function getPackageDependencies()
    {
        return [
            'zeichen32/gitlabapibundle',
        ];
    }
}
