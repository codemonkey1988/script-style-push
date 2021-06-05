<?php

/*
 * This file is part of the "script_style_push" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ScriptStylePush\ViewHelpers\Asset;

use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

class ScriptViewHelper extends AbstractAssetViewHelper
{
    /**
     * @var string
     */
    protected $defaultTagPosition = 'footer';

    /**
     * Initialize arguments
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('async', 'boolean', 'Allows the file to be loaded asynchronously', false, false);
        $this->registerArgument('type', 'boolean', 'Setting the MIME type of the script (default: text/javascript)', false, null);
        $this->registerArgument('integrity', 'boolean', 'Adds the integrity attribute to the script element to let browsers ensure subresource integrity. Useful in hosting scenarios with resources externalized to CDN\'s. See SRI for more details. Integrity hashes may be generated using https://srihash.org/', false, false);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if (version_compare(TYPO3_version, '10.3', '>=')) {
            trigger_error('ScriptViewHelper (script_style_push) has been deprecated and will be removed in 3.0. Please use f:asset.script instead.', E_USER_DEPRECATED);
        }

        parent::render();
    }

    /**
     * {@inheritdoc}
     */
    protected function buildResourceInformation()
    {
        $resource = parent::buildResourceInformation();

        $resource[1]['async'] = $this->arguments['async'];
        $resource[1]['integrity'] = $this->arguments['integrity'];

        if ($this->arguments['type']) {
            $resource[1]['type'] = $this->arguments['type'];
        }

        return $resource;
    }

    /**
     * {@inheritdoc}
     *
     * @link https://docs.typo3.org/typo3cms/TyposcriptReference/8.7/Setup/Page/
     */
    public function getTemplateSetupKeyForPosition(string $position)
    {
        return $position == 'header' ? 'includeJS' : 'includeJSFooter';
    }
}
