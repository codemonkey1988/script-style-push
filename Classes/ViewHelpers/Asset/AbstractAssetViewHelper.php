<?php

/*
 * This file is part of the "script_style_push" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ScriptStylePush\ViewHelpers\Asset;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

abstract class AbstractAssetViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var int index counter for scripts and styles in page template setup
     */
    protected static $templateSetupDataIndexCounter = 30000;

    /**
     * @var string
     */
    protected $defaultTagPosition = 'header';

    /**
     * Initialize arguments.
     * Do not call parent to skip default arguments for tag based viewhelpers.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('path', 'string', 'Path to file', true);
        $this->registerArgument('name', 'string', 'Unique name of this file', true);
        $this->registerArgument('external', 'boolean', 'Is this an external file?', false, false);
        $this->registerArgument('position', 'string', 'Where should the file be added? (header / footer)', false);
        $this->registerArgument('disableCompression', 'boolean', 'If config.compress{TYPE} is enabled, this disables the compression of this file', false, true);
        $this->registerArgument('forceOnTop', 'boolean', 'Boolean flag. If set, this file will be added on top of all other files', false, false);
        $this->registerArgument('excludeFromConcatenation', 'boolean', 'If config.concatenate{TYPE} is enabled, this prevents the file from being concatenated', false, false);
    }

    public function render()
    {
        if (!is_array($GLOBALS['SCRIPT_STYPE_PUSH_ASSETS'])) {
            $GLOBALS['SCRIPT_STYPE_PUSH_ASSETS'] = [];
        }

        if (in_array($this->arguments['name'], $GLOBALS['SCRIPT_STYPE_PUSH_ASSETS'])) {
            // If an asset with the given name is already registered, skip the rendering.
            return;
        }

        $position = $this->arguments['position'] ?: $this->defaultTagPosition;
        $this->addResourceToPageTemplateSetup($position);

        $GLOBALS['SCRIPT_STYPE_PUSH_ASSETS'][] = $this->arguments['name'];
    }

    /**
     * Add the resource to the template setup according to the given position
     *
     * @param string $position
     */
    protected function addResourceToPageTemplateSetup($position)
    {
        $key = $this->getTemplateSetupKeyForPosition($position) . '.';

        // Modify page template setup
        $setup = &$GLOBALS['TSFE']->pSetup;

        if (!array_key_exists($key, $setup)) {
            $setup[$key] = [];
        }

        $resource = $this->buildResourceInformation();
        $resourceIndex = static::$templateSetupDataIndexCounter++;
        $setup[$key][$resourceIndex] = $resource[0];
        if (count($resource) > 1) {
            $setup[$key][$resourceIndex . '.'] = $resource[1];
        }
    }

    /**
     * Builds the resource array for the file included into the page template setup
     *
     * @link https://docs.typo3.org/typo3cms/TyposcriptReference/8.7/Setup/Page/#includecss-array
     * @link https://docs.typo3.org/typo3cms/TyposcriptReference/8.7/Setup/Page/#includejs-array
     *
     * @return array resource information for use in page template setup. First value is the filepath, second the configuration
     */
    protected function buildResourceInformation()
    {
        return [
            $this->arguments['path'],
            [
                'external' => $this->arguments['external'],
                'disableCompression' => $this->arguments['disableCompression'],
                'forceOnTop' => $this->arguments['forceOnTop'],
                'excludeFromConcatenation' => $this->arguments['excludeFromConcatenation']
            ]
        ];
    }

    /**
     * Receives the position
     * @param string $position name of the position
     * @return string key name from template setup section
     */
    abstract public function getTemplateSetupKeyForPosition(string $position);
}
