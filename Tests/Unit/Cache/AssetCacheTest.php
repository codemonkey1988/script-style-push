<?php

declare(strict_types=1);

/*
 * This file is part of the "script_style_push" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ScriptStylePush\Tests\Unit\Cache;

use Codemonkey1988\ScriptStylePush\Cache\AssetCache;
use Codemonkey1988\ScriptStylePush\Resource\Asset;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class AssetCacheTest extends UnitTestCase
{
    /**
     * @test
     */
    public function shouldPushAssetReturnsTrue()
    {
        $asset = new Asset('/my-file.jpg');
        $subject = new AssetCache();

        self::assertTrue($subject->shouldPush($asset));
    }

    /**
     * @test
     */
    public function shouldPushAssetReturnsFalse()
    {
        $subject = $this->getAccessibleMock(AssetCache::class, ['readCache']);
        $subject
            ->method('readCache')
            ->willReturn(['/my-file.jpg']);
        $subject->load();

        $asset = new Asset('/my-file.jpg');

        self::assertFalse($subject->shouldPush($asset));
    }

    /**
     * @test
     */
    public function addAssetAndCheckIfItsAddded()
    {
        $asset = new Asset('/test/my-file.css?123456');
        $subject = $this->getAccessibleMock(AssetCache::class, ['readCache']);
        $subject
            ->method('readCache')
            ->willReturn(['/test/my-file.css?123456', '/test/my-file2.css?123456']);

        $subject->load();
        self::assertFalse($subject->shouldPush($asset));
    }

    /**
     * @test
     */
    public function persistAssets()
    {
        $assets = [
            '/test/my-file.css?123456',
            '/test/my-file2.css?123456',
        ];

        $subject = $this->getAccessibleMock(AssetCache::class, ['writeCache']);
        $subject
            ->expects(self::once())
            ->method('writeCache')
            ->with('typo3_ssp_assets', $assets, $GLOBALS['EXEC_TIME']+7*86400);

        $subject->add(new Asset($assets[0]));
        $subject->add(new Asset($assets[1]));

        $subject->persist();
    }

    /**
     * @test
     */
    public function removeQueryStringVersionNumberFromAsset()
    {
        $subject= $this->getAccessibleMockForAbstractClass(AssetCache::class);

        self::assertEquals('/test/styles.css', $subject->_call('removeVersionNumberFromAsset', '/test/styles.css?1234567890'));
    }

    /**
     * @test
     */
    public function removeEmbeddedVersionNumberFromAssetWithQueryString()
    {
        $GLOBALS['TYPO3_CONF_VARS']['FE']['versionNumberInFilename'] = 'embed';
        $subject= $this->getAccessibleMockForAbstractClass(AssetCache::class);

        self::assertEquals('/test/styles.css', $subject->_call('removeVersionNumberFromAsset', '/test/styles.1234567890.css'));
    }

    /**
     * @test
     */
    public function addNewerVersionOfAssetRespectingVersionString()
    {
        $subject= $this->getAccessibleMockForAbstractClass(
            AssetCache::class,
            [],
            '',
            true,
            true,
            true,
            ['readCache', 'writeCache']
        );
        $subject
            ->method('readCache')
            ->willReturn(['/test/my-file.css?1557316022', '/test/my-file2.css?1557316022']);
        $subject->load();

        $asset = new Asset('/test/my-file.css?1557316025');

        self::assertFalse($subject->shouldPush($asset));
        self::assertCount(2, $subject->_get('pushedAssets'));
        self::assertEquals(['/test/my-file.css', '/test/my-file2.css'], $subject->_get('pushedAssets'));
    }
}
