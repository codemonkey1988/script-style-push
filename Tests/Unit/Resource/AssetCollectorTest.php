<?php
namespace Codemonkey1988\ScriptStylePush\Tests\Unit\Resource;

use Codemonkey1988\ScriptStylePush\Resource\AssetCollector;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class AssetCollectorTest
 */
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

        $this->assertCount(6, $assets);

        $assets->rewind();
        $this->assertSame('/folder/my-style.css', $assets->current()->getFile());
        $assets->next();
        $this->assertSame('/folder/my-style2.css', $assets->current()->getFile());
        $assets->next();
        $this->assertSame('/folder/my-print-style.css', $assets->current()->getFile());
        $assets->next();
        $this->assertSame('/folder/my-script.js', $assets->current()->getFile());
        $assets->next();
        $this->assertSame('/folder/my-script2.js', $assets->current()->getFile());
        $assets->next();
        $this->assertSame('/folder/my-script3.js', $assets->current()->getFile());
    }

    /**
     * @test
     */
    public function areAllAssetsAddedFromConfiguration()
    {
        $additionalAssets = 'my-folder/my-file.css, my-folder/my-font.woff2';
        $subject = new AssetCollector('', $additionalAssets);
        $assets = $subject->fetch();

        $this->assertCount(2, $assets);

        $assets->rewind();
        $this->assertSame('/my-folder/my-file.css', $assets->current()->getFile());
        $assets->next();
        $this->assertSame('/my-folder/my-font.woff2', $assets->current()->getFile());
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

        $this->assertCount(8, $assets);
    }
}
