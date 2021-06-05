<?php

/*
 * This file is part of the "script_style_push" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ScriptStylePush\Tests\Unit\Resource;

use Codemonkey1988\ScriptStylePush\Resource\Asset;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class AssetTest extends UnitTestCase
{
    /**
     * @test
     */
    public function getFilePath()
    {
        $filePath = '/folder/my-file.css';
        $subject = new Asset($filePath);

        self::assertSame($filePath, $subject->getFile());
    }

    /**
     * @test
     */
    public function isAssetTypeStyle()
    {
        $subject = new Asset('/folder/my-file.css');

        self::assertSame('style', $subject->getAssetType());
    }

    /**
     * @test
     */
    public function isAssetTypeScript()
    {
        $subject = new Asset('/folder/my-file.js');

        self::assertSame('script', $subject->getAssetType());
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

        self::assertSame('image', $subjectJpg->getAssetType());
        self::assertSame('image', $subjectJpeg->getAssetType());
        self::assertSame('image', $subjectPng->getAssetType());
        self::assertSame('image', $subjectGif->getAssetType());
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

        self::assertSame('font', $subjectWoff->getAssetType());
        self::assertSame('font', $subjectWoff2->getAssetType());
        self::assertSame('font', $subjectEot->getAssetType());
        self::assertSame('font', $subjectTtf->getAssetType());
    }

    /**
     * @test
     */
    public function isAssetTypeMedia()
    {
        $subjectWoff = new Asset('/folder/my-file.mp4');
        $subjectWoff2 = new Asset('/folder/my-file.ogv');

        self::assertSame('media', $subjectWoff->getAssetType());
        self::assertSame('media', $subjectWoff2->getAssetType());
    }

    /**
     * @test
     */
    public function isAssetTypeEmptyWithInvalidFileExtension()
    {
        $subject = new Asset('folder/my-file.abc');

        self::assertSame('', $subject->getAssetType());
    }

    /**
     * @test
     */
    public function isLocalFileWithLocalUrl()
    {
        $subject = new Asset('/folder/my-file.jpg');

        self::assertTrue($subject->isLocal());
    }

    /**
     * @test
     */
    public function isNotLocalFileWithExteralUrl()
    {
        $subject = new Asset('https://example.tld/folder/my-file.jpg');

        self::assertFalse($subject->isLocal());
    }

    /**
     * @test
     */
    public function isPushEnabledForLocalUrl()
    {
        $subject = new Asset('/folder/my-file.jpg');

        self::assertTrue($subject->isPushEnabled());
    }

    /**
     * @test
     */
    public function isPushDisabledForExternalUrl()
    {
        $subject = new Asset('https://example.tld/folder/my-file.jpg');

        self::assertFalse($subject->isPushEnabled());
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

        self::assertSame('font/woff', $subjectWoff->getType());
        self::assertSame('font/woff2', $subjectWoff2->getType());
        self::assertSame('font/eot', $subjectEot->getType());
        self::assertSame('font/ttf', $subjectTtf->getType());
    }

    /**
     * @test
     */
    public function isTypeEmptyForNonFontFile()
    {
        $subject = new Asset('folder/my-file.jpg');

        self::assertSame('', $subject->getType());
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

        self::assertTrue($subjectWoff->isCrossorigin());
        self::assertTrue($subjectWoff2->isCrossorigin());
        self::assertTrue($subjectEot->isCrossorigin());
        self::assertTrue($subjectTtf->isCrossorigin());
    }

    /**
     * @test
     */
    public function isCrossoriginDisabledForFontFile()
    {
        $subject = new Asset('/folder/my-file.jpg');

        self::assertFalse($subject->isCrossorigin());
    }
}
