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
        $_COOKIE['typo3_ssp_assets'] = '/my-file.jpg';

        $asset = new Asset('/my-file.jpg');
        $subject = new AssetCache();

        $this->assertFalse($subject->shouldPush($asset));
    }

    /**
     * @test
     */
    public function addAssetAndCheckIfItsAddded()
    {
        $asset = new Asset('/my-file.jpg');
        $subject = new AssetCache();

        $subject->add($asset);

        $this->assertFalse($subject->shouldPush($asset));
    }
}
