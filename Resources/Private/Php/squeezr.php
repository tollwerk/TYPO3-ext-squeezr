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

/**
 * Main squeezr processing hub (TYPO3 extension version)
 *
 * This is the main script called whenever a file has to be processed by squeezr. Based on the
 * GET parameters given (which have been introduced by the .htaccess file responsible for the
 * rewrite rules) it will decide which squeezr engine to use.
 *
 * Furthermore, this script is also in charge for cleaning the squeezr file cache.
 * Whenever called without parameters it will start a full cache cleaning cycle. You could
 * e.g. implement a call to this script into your favourite CMS, so that the cache is refreshed
 * whenever you alter any of your images ...
 *
 * @package squeezr
 * @author Joschi Kuphal <joschi@kuphal.net>
 * @copyright Copyright © 2017 Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>, tollwerk® GmbH (http://tollwerk.de)
 * @link http://squeezr.it
 * @github https://github.com/jkphl/squeezr
 * @twitter @squeezr
 * @license http://creativecommons.org/licenses/by/3.0/ Creative Commons Attribution 3.0 Unported License
 */

require_once __DIR__.DIRECTORY_SEPARATOR.'config.php';

// If a squeezr engine has been requested and a file to process is given
if (!empty($_GET['engine']) && !empty($_GET['source'])) {
    switch ($_GET['engine']) {

        // CSS engine
        case 'css':

            // If the CSS engine hasn't been disabled temporarily
            if (SQUEEZR_CSS) {

                // Include the CSS engine itself
                require_once SQUEEZR_ROOT.'lib'.DIRECTORY_SEPARATOR.'Tollwerk'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'Css.php';

                // Squeeze, cache and send the CSS file
                \Tollwerk\Squeezr\Css::instance($_GET['source'])->send();

                // Else: Don't care about caching and deliver the original file
            } else {
                readfile(SQUEEZR_DOCROOT.$_GET['source']);
            }

            break;

        // Image engine
        case 'image':

            // If the CSS engine hasn't been disabled temporarily
            if (SQUEEZR_IMAGE) {

                // Include the image engine itself
                require_once SQUEEZR_ROOT.'lib'.DIRECTORY_SEPARATOR.'Tollwerk'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'Image.php';

                // Squeeze, cache and send the image file
                \Tollwerk\Squeezr\Image::instance($_GET['source'])->send();

                // Else: Don't care about caching and deliver the original file
            } else {
                readfile(SQUEEZR_DOCROOT.$_GET['source']);
            }
            break;
    }

    exit;

// Else: Cache cleaning / Garbage collection
} else {

    // Include the cache cleaner engine
    require_once SQUEEZR_ROOT.'lib'.DIRECTORY_SEPARATOR.'Tollwerk'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'Cleaner.php';

    // Clean the cache root directory
    \Tollwerk\Squeezr\Cleaner::instance(SQUEEZR_CACHEROOT)->clean();

    // Respond with an empty content
    header('HTTP/1.1 204 No Content');
    exit;
}
