<?php
namespace Codemonkey1988\ScriptStylePush\ViewHelpers\Asset;

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

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Class AbstractAssetViewHelper
 *
 * @package Codemonkey1988\ScriptStylePush\ViewHelpers\Asset
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
abstract class AbstractAssetViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $defaultTagPosition = 'header';

    /**
     * Initialize arguments.
     * Do not call parent to skip default arguments for tag based viewhelpers.
     *
     * @return void
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('path', 'string', 'Path to file', true);
        $this->registerArgument('name', 'string', 'Unique name of this file', true);
        $this->registerArgument('external', 'boolean', 'Is this an external file?', false, false);
        $this->registerArgument('position', 'string', 'Where should the file be added? (header / footer)', false);
    }

    /**
     * @return void
     */
    public function render()
    {
        if (!is_array($GLOBALS['SCRIPT_STYPE_PUSH_ASSETS'])) {
            $GLOBALS['SCRIPT_STYPE_PUSH_ASSETS'] = [];
        }

        if (in_array($this->arguments['name'], $GLOBALS['SCRIPT_STYPE_PUSH_ASSETS'])) {
            // If an asset with the given name is already registered, skip the rendering.
            return;
        }

        $filePath = $this->getPublicFilePath($this->arguments['path']);

        $this->addTagToPageRenderer($this->buildTag($filePath));
    }

    /**
     * Renders the tag.
     *
     * @param $filePath
     * @return string The rendered tag.
     */
    abstract protected function buildTag($filePath);

    /**
     * @param string $filePath
     * @return string
     */
    protected function getPublicFilePath($filePath)
    {
        if (!$this->arguments['external']) {
            $absFilePath = GeneralUtility::getFileAbsFileName($filePath);

            if ($absFilePath) {
                $filePath = substr($absFilePath, strlen(PATH_site));
                $filePath = GeneralUtility::createVersionNumberedFilename($filePath);
            }
        }

        return $filePath;
    }

    /**
     * @param string $tag
     * @return void
     */
    protected function addTagToPageRenderer($tag)
    {
        if ($this->arguments['position'] === 'header') {
            $this->getPageRenderer()->addHeaderData($tag);
        } elseif ($this->arguments['position'] === 'footer') {
            $this->getPageRenderer()->addFooterData($tag);
        } else {
            if ($this->defaultTagPosition === 'header') {
                $this->getPageRenderer()->addHeaderData($tag);
            } else {
                $this->getPageRenderer()->addFooterData($tag);
            }
        }
    }

    /**
     * Provides a shared (singleton) instance of PageRenderer
     *
     * @return PageRenderer
     */
    protected function getPageRenderer()
    {
        return GeneralUtility::makeInstance(PageRenderer::class);
    }
}