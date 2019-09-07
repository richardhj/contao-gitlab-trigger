<?php

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

$GLOBALS['TL_DCA']['tl_gitlab_pipeline'] = [

    // Config
    'config'   => [
        'dataContainer' => 'Table',
        'sql'           => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // List
    'list'     => [
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
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
            'run'    => [
                'label' => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['run'],
                'href'  => 'key=run',
                'icon'  => 'bundles/erdmannfreundecontaogitlabtrigger/img/trigger.svg',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{title_legend},name,host,project,token,ref;{variables_legend:hide},variables',
    ],

    // Fields
    'fields'   => [
        'id'        => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'    => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'name'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['name'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class'  => 'long',
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'host'      => [
            'label'         => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['host'],
            'exclude'       => true,
            'filter'        => true,
            'inputType'     => 'text',
            'eval'          => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class'  => 'w50',
            ],
            'load_callback' => [
                static function ($value) {
                    return empty($value) ? 'https://gitlab.com' : $value;
                }
            ],
            'sql'           => "varchar(255) NOT NULL default ''",
        ],
        'project'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['project'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class'  => 'w50',
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'ref'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['ref'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'text',
            'eval'      => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class'  => 'w50',
            ],
            'load_callback' => [
                static function ($value) {
                    return empty($value) ? 'master' : $value;
                }
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'token'     => [
            'label'         => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['token'],
            'exclude'       => true,
            'inputType'     => 'text',
            'eval'          => [
                'mandatory'    => true,
                'maxlength'    => 255,
                'preserveTags' => true,
                'tl_class'     => 'w50',
            ],
            'load_callback' => [
                static function ($value) {
                    return empty($value) ? '' : '*****';
                }
            ],
            'save_callback' => [
                static function ($value, DataContainer $dc) {
                    if ('*****' === $value) {
                        return $dc->activeRecord->token;
                    }

                    $secret = \Contao\System::getContainer()->getParameter('secret');

                    return Crypto::encryptWithPassword($value, $secret);
                }
            ],
            'sql'           => 'text NULL',
        ],
        'variables' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['variables'],
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => [
                'maxlength'    => 255,
                'tl_class'     => 'clr',
                'dragAndDrop'  => false,
                'columnFields' => [
                    'variables_key'   => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['variables_key'],
                        'exclude'   => true,
                        'inputType' => 'text',
                        'eval'      => [
                            'style' => 'width:250px',
                        ],
                    ],
                    'variables_value' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_gitlab_pipeline']['variables_value'],
                        'exclude'   => true,
                        'inputType' => 'text',
                        'eval'      => ['style' => 'width:400px'],
                    ],
                ],
            ],
            'sql'       => 'blob NULL',
        ],
    ],
];
