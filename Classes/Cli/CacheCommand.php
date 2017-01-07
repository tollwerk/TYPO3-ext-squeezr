<?php

/***************************************************************
 *  Copyright notice
 *
 *  Copyright © 2017 Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>, tollwerk® GmbH (http://tollwerk.de)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace Tollwerk\Squeezr\Cli;

use Tollwerk\Squeezr\Utility\Squeezr;
use TYPO3\CMS\Core\Controller\CommandLineController;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * CLI interface for the squeezr cache
 *
 * @package squeezr
 * @copyright Copyright © 2017 Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>, tollwerk® GmbH (http://tollwerk.de)
 * @author Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>
 **/
class CacheCommand extends CommandLineController
{
    /**
     * Konstruktor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->cli_help['name'] = 'squeezr -- Squeezr cache interface';
        $this->cli_help['synopsis'] = 'clear ###OPTIONS###';
        $this->cli_help['description'] = 'Command line interface for clearing the squeezr image and CSS cache';
        $this->cli_help['examples'] = '/.../cli_dispatch.phpsh squeezr clear';
        $this->cli_help['author'] = 'Dipl.-Ing. Joschi Kuphal | tollwerk GmbH';
    }

    /**
     * CLI routine
     *
     * @param \array $argv Command line arguments
     * @return void
     */
    public function cli_main($argv)
    {
        $task = (string)$this->cli_args['_DEFAULT'][1];

        if (!$task) {
            $this->cli_validateArgs();
            $this->cli_help();
            exit;
        }

        // If the cache should be cleared
        if ($task == 'clear') {
            $this->_clearCache();
        }
    }

    /************************************************************************************************
     * PRIVATE METHODS
     ***********************************************************************************************/

    /**
     * Clear the squeezr image and CSS cache
     *
     * @return void
     */
    protected function _clearCache()
    {
        /* @var $squeezrObj Squeezr */
        $squeezrObj = GeneralUtility::makeInstance('Tollwerk\\Squeezr\\Utility\\Squeezr');
        $squeezrObj->updateCache();
    }
}
