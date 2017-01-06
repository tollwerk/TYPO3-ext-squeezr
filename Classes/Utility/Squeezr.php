<?php

namespace Tollwerk\Squeezr\Utility;

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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;

/**
 * Squeezr helper class
 *
 * @package squeezr
 * @copyright Copyright © 2017 Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>, tollwerk® GmbH (http://tollwerk.de)
 * @author Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>
 */
class Squeezr implements \TYPO3\CMS\Core\SingletonInterface, \TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface
{
    /**
     * <head> tag has already been checked / altered
     *
     * @var boolean
     */
    protected static $_headTag = false;

    /**
     * Integrate the squeezr JavaScript into the <head> section of the output
     *
     * @param TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe Frontend engine
     * @return void
     */
    public function checkDataSubmission(\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe)
    {
        if (!self::$_headTag && !empty($tsfe->tmpl->setup['config.']['squeezr.'])) {
            $config = $tsfe->tmpl->setup['config.']['squeezr.'];
            if (!empty($config['enable']) && intval($config['enable']) && !empty($config['images.']) && is_array($config['images.']) && !empty($config['css.']) && is_array($config['css.'])) {
                $headTag = empty($tsfe->pSetup['headTag']) ? '<head>' : trim($tsfe->pSetup['headTag']);
                if (preg_match("%^(\<head[^\>]*\>)(.*)$%i", $headTag, $headTagParts)) {
                    $tsfe->pSetup['headTag'] = $headTagParts[1];
                    $tsfe->pSetup['headTag'] .= '<script type="text/javascript" id="squeezr" data-em-precision="'.min(10,
                            max(0.001, floatval(empty($config['em2px']) ? 0.5 : $config['em2px']))).'"';

                    // Configure the image engine
                    if (!empty($config['images.']['disable']) && intval($config['images.']['disable'])) {
                        $tsfe->pSetup['headTag'] .= ' data-disable-images="1"';
                    } else {
                        $breakpoints = (empty($config['images.']['breakpoints']) || !strlen(trim($config['images.']['breakpoints']))) ? array() : GeneralUtility::trimExplode(',',
                            trim($config['images.']['breakpoints']), true);
                        if (count($breakpoints)) {
                            sort($breakpoints, SORT_NUMERIC);
                            $tsfe->pSetup['headTag'] .= ' data-breakpoints-images="'.implode(',', $breakpoints).'"';
                        } else {
                            $tsfe->pSetup['headTag'] .= ' data-disable-images="1"';
                        }
                    }

                    // Configure the CSS engine
                    if (!empty($config['css.']['disable']) && intval(!empty($config['css.']['disable']))) {
                        $tsfe->pSetup['headTag'] .= ' data-disable-css="1"';
                    }

//                    $tsfe->pSetup['headTag'] .= '>'.file_get_contents(ExtensionManagementUtility::extPath('tw_squeezr',
//                            'Resources'.DIRECTORY_SEPARATOR.'Private'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'squeezr'.DIRECTORY_SEPARATOR.'squeezr.min.js')).'</script>';
                    $tsfe->pSetup['headTag'] .= $headTagParts[2];
                }
            }

            self::$_headTag = true;
        }
    }

    /**
     * Adds a menu entry to the clear cache menu to clear the squeezr file cache
     *
     * @param array $cacheActions List of CacheMenuItems
     * @param array $optionValues List of AccessConfigurations identifiers (typically used by userTS with options.clearCache.identifier)
     */
    public function manipulateCacheActions(&$cacheActions, &$optionValues)
    {
        if ($GLOBALS['BE_USER']->isAdmin()) {
            $LL = $this->includeLocalLang();
            $title = $GLOBALS['LANG']->getLLL('cache.update', $LL);
            $cacheActions[] = array(
                'id' => 'clearUpdateSqueezrCache',
                'title' => $title,
                'href' => $GLOBALS['BACK_PATH'].'ajax.php?ajaxID=squeezr::clearUpdateSqueezrCache',
                'icon' => '<img '.IconUtility::skinImg($GLOBALS['BACK_PATH'],
                        ExtensionManagementUtility::extRelPath('tw_squeezr').'ext_icon.gif',
                        'width="16" height="16"').' title="'.$title.'" alt="'.$title.'" />'
            );
            $optionValues[] = 'clearUpdateSqueezrCache';
        }
    }

    /**
     * Includes the language file and returns the contained language labels
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
     * Cleans the squeezr cache
     *
     * @return void
     */
    public function updateCache()
    {
        $squeezrConfig = PATH_site.'squeezr'.DIRECTORY_SEPARATOR.'config.php';
        if (@is_readable($squeezrConfig)) {

            // Include the squeezr configuration
            require_once $squeezrConfig;

            // Include the cache cleaner engine
            require_once SQUEEZR_ROOT.'lib'.DIRECTORY_SEPARATOR.'Tollwerk'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'Cleaner.php';

            // Clean the cache root directory
            \Tollwerk\Squeezr\Cleaner::instance(SQUEEZR_CACHEROOT)->clean();
        }
    }
}
