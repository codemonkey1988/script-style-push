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

use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Class StyleViewHelper
 *
 * @package Codemonkey1988\ScriptStylePush\ViewHelpers\Asset
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class StyleViewHelper extends AbstractAssetViewHelper
{
    /**
     * Initialize arguments
     *
     * @return void
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