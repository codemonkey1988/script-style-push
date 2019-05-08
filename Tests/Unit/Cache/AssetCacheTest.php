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
            ->with('typo3_ssp_assets', $assets, 0);

        $subject->add(new Asset($assets[0]));
        $subject->add(new Asset($assets[1]));

        $subject->persist();
    }
}
