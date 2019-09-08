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

use ErdmannFreunde\ContaoGitlabTriggerBundle\EventListener\DataContainer\PipelineLogLabelCallback;

$GLOBALS['TL_DCA']['tl_gitlab_pipeline_log'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'ptable'        => 'tl_gitlab_pipeline',
        'closed'        => true,
        'notEditable'   => true,
        'notDeletable'  => true,
        'sql'           => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // List
    'list'   => [
        'sorting'           => [
            'mode'        => 1,
            'fields'      => [
                'created_at DESC',
            ],
            'flag'        => 1,
            'panelLayout' => 'filter,search;limit',
        ],
        'label'             => [
            'fields' => [
                'pipeline_id',
            ],
            'label_callback' => [PipelineLogLabelCallback::class, 'onLabelCallback'],
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'run' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['run'],
                'href'       => 'key=run',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                .'\'))return false;Backend.getScrollOffset()"',
                'icon'       => 'bundles/richardhjcontaonewsletter2gosync/be-user-auth.png',
            ],
        ],
    ],

    // Fields
    'fields' => [
        'id'          => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid'         => [
            'label'  => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['pid'],
            'filter' => true,
            'sql'    => "int(10) NOT NULL default '0'",
        ],
        'pipeline_id' => [
            'label' => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['pipeline_id'],
            'sql'   => "int(10) NOT NULL default '0'",
        ],
        'status'      => [
            'label'  => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['status'],
            'filter' => true,
            'sql'    => "varchar(64) NOT NULL default ''",
        ],
        'web_url'     => [
            'label' => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['web_url'],
            'sql'   => "varchar(255) NOT NULL default ''",
        ],
        'created_at'  => [
            'label'  => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['created_at'],
            'flag'   => 8,
            'filter' => true,
            'sql'    => 'int(10) NULL',
        ],
        'started_at'  => [
            'label' => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['started_at'],
            'flag'  => 8,
            'sql'   => 'int(10) NULL',
        ],
        'updated_at'  => [
            'label' => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['updated_at'],
            'flag'  => 8,
            'sql'   => 'int(10) NULL',
        ],
        'finished_at' => [
            'label'  => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['finished_at'],
            'flag'   => 8,
            'filter' => true,
            'sql'    => 'int(10) NULL',
        ],
        'response'    => [
            'label' => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['response'],
            'sql'   => 'text NULL',
        ],
    ],
];
