<?php

/***************************************************************
 *  Copyright notice
 *
 *  Copyright © 2013 Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>, tollwerk® GmbH (http://tollwerk.de)
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
 * squeezr configuration update script
 *
 * @package		squeezr
 * @copyright	Copyright © 2013 Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>, tollwerk® GmbH (http://tollwerk.de)
 * @author		Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>
 */
class ext_update  {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main()	{
		
		// Update the squeezr hub script
		$config					= $GLOBALS['TYPO3_CONF_VARS']['EXT']['extParams']['squeezr'];
		$squeezrConfig			= implode("\n", array(
			"define('SQUEEZR_DOCROOT', '".PATH_site."');",
			"define('SQUEEZR_ROOT', '".\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('squeezr', 'Resources'.DIRECTORY_SEPARATOR.'Private'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'squeezr'.DIRECTORY_SEPARATOR)."');",
			"define('SQUEEZR_CACHE_LIFETIME', ".intval($config['lifetime']).");",
			"define('SQUEEZR_CACHEROOT', '".PATH_site.'typo3temp'.DIRECTORY_SEPARATOR.'squeezr'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR."');",
			"define('SQUEEZR_PLUGINS', rtrim(SQUEEZR_ROOT, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR);",
			"define('SQUEEZR_BREAKPOINT', empty(\$_GET['breakpoint']) ? null : trim(\$_GET['breakpoint']));",
			"define('SQUEEZR_IMAGE', ".(intval($config['images']) ? 'true' : 'false').");",
			"define('SQUEEZR_IMAGE_JPEG_QUALITY', ".min(100, max(1, intval($config['quality']))).");",
			"define('SQUEEZR_IMAGE_SHARPEN', ".(intval($config['sharpen']) ? 'true' : 'false').");",
			"define('SQUEEZR_IMAGE_FORCE_SHARPEN', ".(intval($config['forcesharpen']) ? 'true' : 'false').");",
			"define('SQUEEZR_IMAGE_COPY_UNDERSIZED', ".(intval($config['undersized']) ? 'true' : 'false').");",
			"define('SQUEEZR_IMAGE_PNG_QUANTIZER', '".(strlen(trim($config['pngquantizer'])) ? strtolower(trim($config['pngquantizer'])) : 'internal')."');",
			"define('SQUEEZR_IMAGE_PNG_QUANTIZER_SPEED', ".max(1, min(10, intval($config['pngquantizerspeed']))).");",
			"define('SQUEEZR_CSS', ".(intval($config['css']) ? 'true' : 'false').");",
			"define('SQUEEZR_CSS_MINIFICATION_PROVIDER', '".(strlen(trim($config['minification'])) ? trim($config['minification']) : 'null')."');",
		));
		file_put_contents(PATH_site.'typo3temp'.DIRECTORY_SEPARATOR.'squeezr'.DIRECTORY_SEPARATOR.'config.php', '<?php'."\n\n$squeezrConfig");
		copy(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('squeezr', 'Resources'.DIRECTORY_SEPARATOR.'Private'.DIRECTORY_SEPARATOR.'Php'.DIRECTORY_SEPARATOR.'squeezr.php'), PATH_site.'typo3temp'.DIRECTORY_SEPARATOR.'squeezr'.DIRECTORY_SEPARATOR.'index.php');
		
		// Install the .htacces file in case it doesn't exist yet
		if (!@is_file(PATH_site.'typo3temp'.DIRECTORY_SEPARATOR.'squeezr'.DIRECTORY_SEPARATOR.'.htaccess')) {
			$htaccess			= file_get_contents(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('squeezr', 'Resources'.DIRECTORY_SEPARATOR.'Private'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'squeezr'.DIRECTORY_SEPARATOR.'.htaccess'));
			file_put_contents(PATH_site.'typo3temp'.DIRECTORY_SEPARATOR.'squeezr'.DIRECTORY_SEPARATOR.'.htaccess', str_replace('RewriteBase /squeezr', 'RewriteBase /typo3temp/squeezr', $htaccess));
		}
		
		return $GLOBALS['LANG']->getLLL('config.update', $this->includeLocalLang());
	}

	/**
	 * Includes the language file and returns the found language labels
	 *
	 * @return array							Language labels
	 */
	public function includeLocalLang()	{
		/* @var $parserFactory t3lib_l10n_Factory */
		$parserFactory									= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('t3lib_l10n_Factory');
		return $parserFactory->getParsedData(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('squeezr', 'Resources'.DIRECTORY_SEPARATOR.'Private'.DIRECTORY_SEPARATOR.'Language'.DIRECTORY_SEPARATOR.'locallang_db.xlf'), $GLOBALS['LANG']->lang, 'utf-8', 1);
	}
	
	/**
	 * Access is always allowed
	 *
	 * @return	boolean		Always returns true
	 */
	function access() {
		return true;
	}
}

// Include extension?
if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/squeezr/class.ext_update.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/squeezr/class.ext_update.php']);
}

?>