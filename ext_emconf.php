<?php

########################################################################
# Extension Manager/Repository config file for ext "squeezr".
#
# Auto generated 03-01-2014 12:10
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
    'title' => 'tollwerk squeezr',
    'description' => 'TYPO3 extension version of squeezr, another take on device-aware adaptive images and server side CSS3 media queries',
    'category' => 'plugin',
    'shy' => 0,
    'version' => '2.0.0',
    'dependencies' => 'extbase,fluid',
    'conflicts' => '',
    'priority' => '',
    'loadOrder' => '',
    'module' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => 'squeezr/cache',
    'modify_tables' => '',
    'clearcacheonload' => 0,
    'lockType' => '',
    'author' => 'Joschi Kuphal',
    'author_email' => 'joschi@tollwerk.de',
    'author_company' => 'tollwerk GmbH',
    'CGLcompliance' => '',
    'CGLcompliance_note' => '',
    'constraints' => array(
        'depends' => array(
            'extbase' => '7.0.0-0.0.0',
            'fluid' => '7.0.0-0.0.0',
            'php' => '5.3.0-0.0.0',
            'typo3' => '7.0.0-0.0.0',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
    'suggests' => array(),
);
