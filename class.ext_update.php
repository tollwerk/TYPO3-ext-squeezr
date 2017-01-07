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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * squeezr configuration update script
 *
 * @package squeezr
 * @copyright Copyright © 2017 Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>, tollwerk® GmbH (http://tollwerk.de)
 * @author Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>
 */
class ext_update
{
    /**
     * Main function, returning the HTML content of the module
     *
     * @return    string        HTML
     */
    function main()
    {
        $abstractSqueezrClassReflection = new \ReflectionClass('Tollwerk\\Squeezr');
        $squeezrRoot = dirname(dirname(dirname($abstractSqueezrClassReflection->getFileName())));
        $vendorDir = dirname(dirname(dirname($squeezrRoot))).DIRECTORY_SEPARATOR;
        $squeezrConfigFile = PATH_site.'squeezr'.DIRECTORY_SEPARATOR.'config.php';

        // Update the squeezr hub script
        $config = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extParams']['tw_squeezr'];
        $squeezrConfig = implode("\n", array(
            "define('SQUEEZR_DOCROOT', '".PATH_site."');",
            "define('SQUEEZR_ROOT', '".$squeezrRoot."');",
            "define('SQUEEZR_CACHE_LIFETIME', ".intval($config['lifetime']).");",
            "define('SQUEEZR_CACHEROOT', '".PATH_site.'squeezr'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR."');",
            "define('SQUEEZR_BREAKPOINT', empty(\$_GET['breakpoint']) ? null : trim(\$_GET['breakpoint']));",
            "define('SQUEEZR_IMAGE', ".(intval($config['images']) ? 'true' : 'false').");",
            "define('SQUEEZR_IMAGE_JPEG_QUALITY', ".min(100, max(1, intval($config['quality']))).");",
            "define('SQUEEZR_IMAGE_SHARPEN', ".(intval($config['sharpen']) ? 'true' : 'false').");",
            "define('SQUEEZR_IMAGE_FORCE_SHARPEN', ".(intval($config['forcesharpen']) ? 'true' : 'false').");",
            "define('SQUEEZR_IMAGE_COPY_UNDERSIZED', ".(intval($config['undersized']) ? 'true' : 'false').");",
            "define('SQUEEZR_IMAGE_PNG_QUANTIZER', '".(strlen(trim($config['pngquantizer'])) ? strtolower(trim($config['pngquantizer'])) : 'internal')."');",
            "define('SQUEEZR_IMAGE_PNG_QUANTIZER_SPEED', ".max(1, min(10, intval($config['pngquantizerspeed']))).");",
            "define('SQUEEZR_CSS', ".(intval($config['css']) ? 'true' : 'false').");",
            "define('SQUEEZR_CSS_MINIFY', ".(intval($config['minify']) ? 'true' : 'false').");",
        ));
        file_put_contents($squeezrConfigFile, '<?php'."\n\n$squeezrConfig");

        // Install the squeezr main script
        $squeezrMain = file_get_contents($squeezrRoot.DIRECTORY_SEPARATOR.'index.php');
        file_put_contents(
            PATH_site.'squeezr'.DIRECTORY_SEPARATOR.'index.php',
            "<?php\n\n".implode("\n", array(
                "define('SQUEEZR_VENDOR_DIR', '$vendorDir');",
                "define('SQUEEZR_CONFIG_COMMON', '$squeezrConfigFile');",
                "define('SQUEEZR_CONFIG_IMAGE', '$squeezrConfigFile');",
                "define('SQUEEZR_CONFIG_CSS', '$squeezrConfigFile');"
            ))."\n\n?>$squeezrMain"
        );

        // Install the .htacces file in case it doesn't exist yet
        if (!@is_file(PATH_site.'squeezr'.DIRECTORY_SEPARATOR.'.htaccess')) {
            copy($squeezrRoot.DIRECTORY_SEPARATOR.'.htaccess', PATH_site.'squeezr'.DIRECTORY_SEPARATOR.'.htaccess');
        }

        // Extract the necessary rewrite rules
        $htaccess = file_get_contents(dirname($squeezrRoot).DIRECTORY_SEPARATOR.'.htaccess');
        if (preg_match('%RewriteBase /(.+?)</IfModule>%s', $htaccess, $rules)) {
            return sprintf(
                $GLOBALS['LANG']->getLLL('config.update', $this->includeLocalLang()),
                trim($rules[1]),
                'RewriteCond %{REQUEST_URI} !^/typo3/'
            );
        } else {
            return $GLOBALS['LANG']->getLLL('config.update.error', $this->includeLocalLang());
        }
    }

    /**
     * Includes the language file and returns the found language labels
     *
     * @return array                            Language labels
     */
    public function includeLocalLang()
    {
        /* @var $parserFactory \TYPO3\CMS\Core\Localization\LocalizationFactory */
        $parserFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Localization\\LocalizationFactory');
        return $parserFactory->getParsedData(ExtensionManagementUtility::extPath('tw_squeezr',
            'Resources'.DIRECTORY_SEPARATOR.'Private'.DIRECTORY_SEPARATOR.'Language'.DIRECTORY_SEPARATOR.'locallang_db.xlf'),
            $GLOBALS['LANG']->lang, 'utf-8', 1);
    }

    /**
     * Access is always allowed
     *
     * @return    boolean        Always returns true
     */
    function access()
    {
        return true;
    }
}

// Include extension?
if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/squeezr/class.ext_update.php']) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/squeezr/class.ext_update.php']);
}
