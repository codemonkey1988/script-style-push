<?php
declare(strict_types=1);
namespace Codemonkey1988\ScriptStylePush\Tests\Unit\Cache;

use Codemonkey1988\ScriptStylePush\Cache\AssetCache;
use Codemonkey1988\ScriptStylePush\Resource\Asset;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class AssetCacheTest
 */
class AssetCacheTest extends UnitTestCase
{
    /**
     * @test
     */
    public function shouldPushAssetReturnsTrue()
    {
        $asset = new Asset('/my-file.jpg');
        $subject = new AssetCache();

        $this->assertTrue($subject->shouldPush($asset));
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

        $this->assertFalse($subject->shouldPush($asset));
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
        $this->assertFalse($subject->shouldPush($asset));
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
            ->expects($this->once())
            ->method('writeCache')
            ->with('typo3_ssp_assets', $assets, 7*86400);

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

        $this->assertEquals('/test/styles.css', $subject->_call('removeVersionNumberFromAsset', '/test/styles.css?1234567890'));
    }

    /**
     * @test
     */
    public function removeEmbeddedVersionNumberFromAssetWithQueryString()
    {
        $GLOBALS['TYPO3_CONF_VARS']['FE']['versionNumberInFilename'] = 'embed';
        $subject= $this->getAccessibleMockForAbstractClass(AssetCache::class);

        $this->assertEquals('/test/styles.css', $subject->_call('removeVersionNumberFromAsset', '/test/styles.1234567890.css'));
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

        $this->assertFalse($subject->shouldPush($asset));
        $this->assertCount(2, $subject->_get('pushedAssets'));
        $this->assertEquals(['/test/my-file.css', '/test/my-file2.css'], $subject->_get('pushedAssets'));
    }
}
