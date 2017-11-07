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

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class ContentPostProcessor
 *
 * @package Codemonkey1988\ScriptStylePush\Hooks
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class ContentPostProcessor
{
    /**
     * @var int
     */
    protected $addtionalHeadersStartKey = 1578;

    /**
     * @var array
     */
    protected $assets = [];

    /**
     * Render method for cached pages
     *
     * @return void
     * @throws \UnexpectedValueException
     */
    public function renderAll()
    {
        // Run this hook only if there is no http referrer. When there is one, that means that this template is loaded by an
        // ajax request and shoul not contain data to be pushed.
        if (!GeneralUtility::getIndpEnv('HTTP_REFERRER')) {
            $this->addPushHeaderTagsFromDocument($GLOBALS['TSFE']);
            $this->addPushHeaderTagsFromTypoScript($GLOBALS['TSFE']);
            $this->addHeader();
        }
    }

    /**
     * Add link headers that are defined in typoscript.
     *
     * @param TypoScriptFrontendController $tsfe
     * @return void
     * @throws \UnexpectedValueException
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
                        $file          = substr($file, $absPathLength);
                        $absFilePrefix = $GLOBALS['TSFE']->absRefPrefix;

                        $fileUrl = '/' . ltrim($absFilePrefix, '/') . ltrim($file, '/');
                        $this->addAsset($fileUrl);
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
     * @throws \UnexpectedValueException
     */
    protected function addPushHeaderTagsFromDocument(TypoScriptFrontendController &$tsfe)
    {
        preg_match_all('/href="([^"]+\.css[^"]*)"|src="([^"]+\.js[^"]*)"/', $tsfe->content, $matches);
        $result = array_filter(array_merge($matches[1], $matches[2]));

        foreach ($result as $file) {
            if ($this->checkFileForInternal($file)) {
                $fileUrl = '/' . ltrim($file, '/');
                $this->addAsset($fileUrl);
            }
        }
    }

    /**
     * @param string $fileUrl
     * @return void
     * @throws \UnexpectedValueException
     */
    protected function addAsset($fileUrl)
    {
        $host = GeneralUtility::getIndpEnv('HTTP_HOST');
        $ssl = GeneralUtility::getIndpEnv('TYPO3_SSL');
        $absFileUrl = ($ssl ? 'https' : 'http')  . '://' . $host . '/' . ltrim($fileUrl, '/');

        $this->assets[] = '<' . $absFileUrl . '>; ' . $this->getConfigForFiletype($fileUrl);
    }

    /**
     * @return void
     */
    protected function addHeader()
    {
        $additionalHeaders = [
            $this->addtionalHeadersStartKey . '.' => [
                'header'  => 'Link: ' . implode(', ', $this->assets),
                'replace' => '1'
            ]
        ];

        if (!isset($GLOBALS['TSFE']->config['config']['additionalHeaders.'])) {
            $GLOBALS['TSFE']->config['config']['additionalHeaders.'] = [];
        }

        ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TSFE']->config['config']['additionalHeaders.'], $additionalHeaders);

        $this->addtionalHeadersStartKey++;
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