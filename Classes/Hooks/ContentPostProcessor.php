<?php

namespace Codemonkey1988\ScriptStylePush\Hooks;

/***************************************************************
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class ContentPostProcessor
 *
 * @package    Codemonkey1988\ScriptStylePush
 * @subpackage Hooks
 * @author     Tim Schreiner <schreiner.tim@gmail.com>
 */
class ContentPostProcessor
{
    var $headerLinkContent = [];

    /**
     * Render method for cached pages
     *
     * @param array $params
     * @return void
     */
    public function renderAll(array &$params)
    {
        if ($this->isTypoScriptFrontendInstance($params['pObj'])) {
            $this->addPushHeaderTagsFromDocument($params['pObj']);
            $this->addPushHeaderTagsFromTypoScript($params['pObj']);
        }
    }

    /**
     * Render method for INT pages
     *
     * @param array $params
     * @return void
     */
    public function renderOutput(array &$params)
    {
        if ($this->isTypoScriptFrontendInstance($params['pObj'])) {
            $this->addPushHeaderTagsFromDocument($params['pObj']);
            $this->addPushHeaderTagsFromTypoScript($params['pObj']);
        }
    }

    /**
     * Check if the parameter is of type \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     *
     * @param mixed $tsfe
     * @return boolean
     */
    protected function isTypoScriptFrontendInstance(&$tsfe)
    {
        return is_object($tsfe) && $tsfe instanceof TypoScriptFrontendController;
    }

    /**
     * Add link headers that are defined in typoscript.
     *
     * @param TypoScriptFrontendController $tsfe
     * @return void
     */
    protected function addPushHeaderTagsFromTypoScript(TypoScriptFrontendController &$tsfe)
    {
        if (isset($tsfe->tmpl->setup['plugin.']['tx_scriptstylepush.']['settings.']['headers.'])
            && is_array(
                $tsfe->tmpl->setup['plugin.']['tx_scriptstylepush.']['settings.']['headers.']
            )
        ) {
            $absPathLength = strlen(PATH_site);
            foreach ($tsfe->tmpl->setup['plugin.']['tx_scriptstylepush.']['settings.']['headers.'] as $file) {
                if ($this->checkFileForInternal($file)) {
                    $file = GeneralUtility::getFileAbsFileName($file);

                    if ($file) {
                        $file = substr($file, $absPathLength);
                        $absFilePrefix = $GLOBALS['TSFE']->absRefPrefix;

                        $fileUrl = '/' . ltrim($absFilePrefix, '/') . ltrim($file, '/');
                        header('Link: <' . $fileUrl . '>; ' . $this->getConfigForFiletype($file), false);
                    }
                }
            }
        }
    }

    /**
     * Parse the output content for stylesheets and script files.
     *
     * @param TypoScriptFrontendController $tsfe
     * @return void
     */
    protected function addPushHeaderTagsFromDocument(TypoScriptFrontendController &$tsfe)
    {
        preg_match_all('/href="([^"]+\.css[^"]*)"|src="([^"]+\.js[^"]*)"/', $tsfe->content, $matches);
        $result = array_filter(array_merge($matches[1], $matches[2]));
        foreach ($result as $file) {
            if ($this->checkFileForInternal($file)) {
                $fileUrl = '/' . ltrim($file, '/');
                header('Link: <' . $fileUrl . '>; ' . $this->getConfigForFiletype($file), false);
            }
        }
    }

    /**
     * @param string $file
     * @return bool
     */
    protected function checkFileForInternal($file)
    {
        $components = parse_url($file);
        if (!isset($components['host']) && !isset($components['scheme'])) {
            return true;
        } elseif (isset($components['scheme']) && $components['scheme'] === 'EXT') {
            return true;
        }

        return false;
    }

    /**
     * @param string $file
     * @return string
     */
    protected function getConfigForFiletype($file)
    {
        $extension = end(explode('.', parse_url($file, PHP_URL_PATH)));
        switch ($extension) {
            case "css":
                return 'rel=preload; as=style';
                break;
            case "js":
                return 'rel=preload; as=script';
                break;
            case 'svg':
            case 'gif':
            case 'png':
            case 'jpg':
            case 'jpeg':
                return 'rel=preload; as=image';
                break;
            case 'mp4':
                return 'rel=preload; as=media';
                break;
            case 'woff':
                return 'rel=preload; as=font; type="font/woff';
            case 'woff2':
                return 'rel=preload; as=font; type="font/woff2';
            case 'eot':
                return 'rel=preload; as=font; type="font/eot';
            case 'ttf':
                return 'rel=preload; as=font; type="font/ttf';
            default:
                // Do not push the resource when conent type does not match.
                return 'rel=preload; nopush';
        }
    }
}