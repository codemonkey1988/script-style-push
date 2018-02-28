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
 * Class ScriptViewHelper
 *
 * @package Codemonkey1988\ScriptStylePush\ViewHelpers\Asset
 * @author  Tim Schreiner <schreiner.tim@gmail.com>
 */
class ScriptViewHelper extends AbstractAssetViewHelper
{
    /**
     * @var string
     */
    protected $defaultTagPosition = 'footer';

    /**
     * Initialize arguments
     *
     * @return void
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