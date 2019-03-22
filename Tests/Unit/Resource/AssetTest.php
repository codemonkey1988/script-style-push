<?php
namespace Codemonkey1988\ScriptStylePush\Tests\Unit\Resource;

use Codemonkey1988\ScriptStylePush\Resource\Asset;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class AssetTest
 */
class AssetTest extends UnitTestCase
{
    /**
     * @test
     */
    public function getFilePath()
    {
        $filePath = '/folder/my-file.css';
        $subject = new Asset($filePath);

        $this->assertSame($filePath, $subject->getFile());
    }

    /**
     * @test
     */
    public function isAssetTypeStyle()
    {
        $subject = new Asset('/folder/my-file.css');

        $this->assertSame('style', $subject->getAssetType());
    }

    /**
     * @test
     */
    public function isAssetTypeScript()
    {
        $subject = new Asset('/folder/my-file.js');

        $this->assertSame('script', $subject->getAssetType());
    }

    /**
     * @test
     */
    public function isAssetTypeImage()
    {
        $subjectJpg = new Asset('/folder/my-file.jpg');
        $subjectJpeg = new Asset('/folder/my-file.jpeg');
        $subjectPng = new Asset('/folder/my-file.png');
        $subjectGif = new Asset('/folder/my-file.gif');

        $this->assertSame('image', $subjectJpg->getAssetType());
        $this->assertSame('image', $subjectJpeg->getAssetType());
        $this->assertSame('image', $subjectPng->getAssetType());
        $this->assertSame('image', $subjectGif->getAssetType());
    }

    /**
     * @test
     */
    public function isAssetTypeFont()
    {
        $subjectWoff = new Asset('/folder/my-file.woff');
        $subjectWoff2 = new Asset('/folder/my-file.woff2');
        $subjectEot = new Asset('/folder/my-file.eot');
        $subjectTtf = new Asset('/folder/my-file.ttf');

        $this->assertSame('font', $subjectWoff->getAssetType());
        $this->assertSame('font', $subjectWoff2->getAssetType());
        $this->assertSame('font', $subjectEot->getAssetType());
        $this->assertSame('font', $subjectTtf->getAssetType());
    }

    /**
     * @test
     */
    public function isAssetTypeMedia()
    {
        $subjectWoff = new Asset('/folder/my-file.mp4');
        $subjectWoff2 = new Asset('/folder/my-file.ogv');

        $this->assertSame('media', $subjectWoff->getAssetType());
        $this->assertSame('media', $subjectWoff2->getAssetType());
    }

    /**
     * @test
     */
    public function isAssetTypeEmptyWithInvalidFileExtension()
    {
        $subject = new Asset('folder/my-file.abc');

        $this->assertSame('', $subject->getAssetType());
    }

    /**
     * @test
     */
    public function isLocalFileWithLocalUrl()
    {
        $subject = new Asset('/folder/my-file.jpg');

        $this->assertTrue($subject->isLocal());
    }

    /**
     * @test
     */
    public function isNotLocalFileWithExteralUrl()
    {
        $subject = new Asset('https://example.tld/folder/my-file.jpg');

        $this->assertFalse($subject->isLocal());
    }

    /**
     * @test
     */
    public function isPushEnabledForLocalUrl()
    {
        $subject = new Asset('/folder/my-file.jpg');

        $this->assertTrue($subject->isPushEnabled());
    }

    /**
     * @test
     */
    public function isPushDisabledForExternalUrl()
    {
        $subject = new Asset('https://example.tld/folder/my-file.jpg');

        $this->assertFalse($subject->isPushEnabled());
    }

    /**
     * @test
     */
    public function isTypeCorrectForFonts()
    {
        $subjectWoff = new Asset('/folder/my-file.woff');
        $subjectWoff2 = new Asset('/folder/my-file.woff2');
        $subjectEot = new Asset('/folder/my-file.eot');
        $subjectTtf = new Asset('/folder/my-file.ttf');

        $this->assertSame('font/woff', $subjectWoff->getType());
        $this->assertSame('font/woff2', $subjectWoff2->getType());
        $this->assertSame('font/eot', $subjectEot->getType());
        $this->assertSame('font/ttf', $subjectTtf->getType());
    }

    /**
     * @test
     */
    public function isTypeEmptyForNonFontFile()
    {
        $subject = new Asset('folder/my-file.jpg');

        $this->assertSame('', $subject->getType());
    }

    /**
     * @test
     */
    public function isCrossoriginEnabledForFontFile()
    {
        $subjectWoff = new Asset('/folder/my-file.woff');
        $subjectWoff2 = new Asset('/folder/my-file.woff2');
        $subjectEot = new Asset('/folder/my-file.eot');
        $subjectTtf = new Asset('/folder/my-file.ttf');

        $this->assertTrue($subjectWoff->isCrossorigin());
        $this->assertTrue($subjectWoff2->isCrossorigin());
        $this->assertTrue($subjectEot->isCrossorigin());
        $this->assertTrue($subjectTtf->isCrossorigin());
    }

    /**
     * @test
     */
    public function isCrossoriginDisabledForFontFile()
    {
        $subject = new Asset('/folder/my-file.jpg');

        $this->assertFalse($subject->isCrossorigin());
    }
}
