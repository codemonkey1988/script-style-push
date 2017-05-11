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
        $this->registerArgument('async', 'boolean', '', false, false);
    }

    /**
     * @param string $filePath
     * @return string
     */
    protected function buildTag($filePath)
    {
        $this->tag->reset();
        $this->tag->setTagName('script');
        $this->tag->forceClosingTag(true);

        // Build the tag.
        $this->tag->addAttribute('src', $filePath);
        $this->tag->addAttribute('type', 'text/javascript');

        if ($this->arguments['async'] === true) {
            $this->tag->addAttribute('async', 'async');
        }

        return $this->tag->render();
    }
}