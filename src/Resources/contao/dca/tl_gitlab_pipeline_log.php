<?php


$GLOBALS['TL_DCA']['tl_gitlab_pipeline'] = [

    // Config
    'config' => [
        'dataContainer' => 'Table',
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
                'name',
            ],
            'flag'        => 1,
            'panelLayout' => 'filter,search;limit',
        ],
        'label'             => [
            'fields' => [
                'name',
            ],
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
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                . '\'))return false;Backend.getScrollOffset()"',
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
            'label' => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['created_at'],
            'sql'   => 'int(10) NULL',
        ],
        'started_at'  => [
            'label'  => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['started_at'],
            'filter' => true,
            'sql'    => 'int(10) NULL',
        ],
        'updated_at'  => [
            'label'  => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['updated_at'],
            'filter' => true,
            'sql'    => 'int(10) NULL',
        ],
        'finished_at'  => [
            'label'  => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['finished_at'],
            'filter' => true,
            'sql'    => 'int(10) NULL',
        ],
        'response'    => [
            'label'  => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline_log']['response'],
            'filter' => true,
            'sql'    => 'text NULL',
        ],
    ],
];
