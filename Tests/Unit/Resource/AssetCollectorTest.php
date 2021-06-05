<?php

/*
 * This file is part of the "script_style_push" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ScriptStylePush\Tests\Unit\Resource;

use Codemonkey1988\ScriptStylePush\Resource\AssetCollector;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class AssetCollectorTest extends UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        $GLOBALS['TSFE'] = new \stdClass();
        $GLOBALS['TSFE']->absRefPrefix = '';
    }

    /**
     * @test
     */
    public function areAllAssetsFetchedFromHtml()
    {
        $html = file_get_contents(__DIR__ . '/../Fixtures/Website.html');
        $subject = new AssetCollector($html);
        $assets = $subject->fetch();

        self::assertCount(6, $assets);

        $assets->rewind();
        self::assertSame('/folder/my-style.css', $assets->current()->getFile());
        $assets->next();
        self::assertSame('/folder/my-style2.css', $assets->current()->getFile());
        $assets->next();
        self::assertSame('/folder/my-print-style.css', $assets->current()->getFile());
        $assets->next();
        self::assertSame('/folder/my-script.js', $assets->current()->getFile());
        $assets->next();
        self::assertSame('/folder/my-script2.js', $assets->current()->getFile());
        $assets->next();
        self::assertSame('/folder/my-script3.js', $assets->current()->getFile());
    }

    /**
     * @test
     */
    public function areAllAssetsAddedFromConfiguration()
    {
        $additionalAssets = 'my-folder/my-file.css, my-folder/my-font.woff2';
        $subject = new AssetCollector('', $additionalAssets);
        $assets = $subject->fetch();

        self::assertCount(2, $assets);

        $assets->rewind();
        self::assertSame('/my-folder/my-file.css', $assets->current()->getFile());
        $assets->next();
        self::assertSame('/my-folder/my-font.woff2', $assets->current()->getFile());
    }

    /**
     * @test
     */
    public function areAssetsFromHtmlAndConfigurationCorrectlyMerged()
    {
        $html = file_get_contents(__DIR__ . '/../Fixtures/Website.html');
        $additionalAssets = 'my-folder/my-file.css, my-folder/my-font.woff2';
        $subject = new AssetCollector($html, $additionalAssets);
        $assets = $subject->fetch();

        self::assertCount(8, $assets);
    }

    /**
     * @test
     */
    public function ignoreAssetForPushing()
    {
        $html = file_get_contents(__DIR__ . '/../Fixtures/Website.html');
        $additionalAssets = 'my-folder/my-file.css, my-folder/my-font.woff2';
        $subject = new AssetCollector($html, $additionalAssets, '(\/my-style2\.css)');
        $assets = $subject->fetch();

        self::assertCount(7, $assets);
    }
}
