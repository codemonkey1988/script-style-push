<?php

/*
 * This file is part of the "script_style_push" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ScriptStylePush\ViewHelpers\Asset;

use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

class StyleViewHelper extends AbstractAssetViewHelper
{
    /**
     * Initialize arguments
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('alternate', 'boolean', 'If set then the rel-attribute will be "alternate stylesheet', false, false);
        $this->registerArgument('import', 'boolean', 'If set then the @import way of including a stylesheet is used instead of <link>', false, false);
        $this->registerArgument('inline', 'boolean', 'If set, the content of the CSS file is inlined using <style> tags. Note that external files are not inlined', false, false);
        $this->registerArgument('media', 'string', 'Setting the media attribute of the <style> tag', false);
        $this->registerArgument('title', 'string', 'Setting the title of the <style> tag', false);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if (version_compare(TYPO3_version, '10.3', '>=')) {
            trigger_error('StyleViewHelper (script_style_push) has been deprecated and will be removed in 3.0. Please use f:asset.css instead.', E_USER_DEPRECATED);
        }

        parent::render();
    }

    /**
     * {@inheritdoc}
     */
    protected function buildResourceInformation()
    {
        $resource = parent::buildResourceInformation();

        $resource[1]['alternate'] = $this->arguments['alternate'];
        $resource[1]['import'] = $this->arguments['import'];
        $resource[1]['inline'] = $this->arguments['inline'];

        if ($this->arguments['media']) {
            $resource[1]['media'] = $this->arguments['media'];
        }

        if ($this->arguments['title']) {
            $resource[1]['title'] = $this->arguments['title'];
        }

        return $resource;
    }

    /**
     * Returns the key in the page setup where these resources should be added to.
     * Always return "includeCSS" because the page renderer only supports this position for CSS files yet.
     *
     * @link https://docs.typo3.org/typo3cms/TyposcriptReference/8.7/Setup/Page/
     */
    public function getTemplateSetupKeyForPosition(string $position)
    {
        return 'includeCSS';
    }
}
