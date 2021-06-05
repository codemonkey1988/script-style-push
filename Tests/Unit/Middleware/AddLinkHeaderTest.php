<?php

/*
 * This file is part of the "script_style_push" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ScriptStylePush\Tests\Unit\Middleware;

use Codemonkey1988\ScriptStylePush\Middleware\AddLinkHeader;
use Codemonkey1988\ScriptStylePush\Resource\Asset;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class AddLinkHeaderTest extends UnitTestCase
{
    /**
     * @test
     */
    public function areAllAssetsUsedForHeaderContentGeneration()
    {
        $assets = new \SplObjectStorage();
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['script_style_push']['enableOverpushPrevention'] = '0';

        $styleAsset = new Asset('/my-folder/styles.css');
        $scriptAsset = new Asset('/my-folder/scripts.js');
        $externalScriptAsset = new Asset('https://example.tld/my-folder/scripts2.js');
        $fontAsset = new Asset('/my-folder/font.woff');

        $assets->attach($styleAsset);
        $assets->attach($scriptAsset);
        $assets->attach($externalScriptAsset);
        $assets->attach($fontAsset);

        $proxyClass = $this->buildAccessibleProxy(AddLinkHeader::class);
        $subject = new $proxyClass();
        $headerContent = $subject->_call('renderHeaderContent', $assets);

        $expected = [
            '</my-folder/styles.css>;rel=preload;as=style',
            '</my-folder/scripts.js>;rel=preload;as=script',
            '</my-folder/font.woff>;rel=preload;as=font;type=font/woff;crossorigin',
        ];

        self::assertSame(implode(',', $expected), $headerContent);
    }
}
