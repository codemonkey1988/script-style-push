<?php
declare(strict_types=1);
namespace Codemonkey1988\ScriptStylePush\Cache;

use Codemonkey1988\ScriptStylePush\Resource\Asset;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AssetCache
 */
class AssetCache implements SingletonInterface
{
    /**
     * @var string
     */
    protected $cookieName;

    /**
     * @var string
     */
    protected $lifetime;

    /**
     * @var array
     */
    protected $pushedAssets;

    /**
     * @var bool An flag that indicates, if an assets was added to the cache.
     */
    protected $changed;

    /**
     * AssetCache constructor.
     * @param string $cookieName The name of the cookie
     * @param int $lifetime Lifetime of the cookie in seconds
     */
    public function __construct($cookieName = 'typo3_ssp_assets', $lifetime = 0)
    {
        $this->changed = false;
        $this->cookieName = $cookieName;
        $this->lifetime = $lifetime;
        $this->pushedAssets = explode(',', $_COOKIE[$this->cookieName] ?? '');
    }

    /**
     * Checks if a given assets should be pushed.
     * This method checks, if this file were already in earlier request by checking the cookie value.
     *
     * @param Asset $asset
     * @return bool
     */
    public function shouldPush(Asset $asset): bool
    {
        return !in_array($asset->getFile(), $this->pushedAssets);
    }

    /**
     * Add an asset to the list of pushed assets.
     *
     * @param Asset $asset
     */
    public function add(Asset $asset)
    {
        if ($this->shouldPush($asset)) {
            $this->pushedAssets[] = $asset->getFile();
            $this->changed = true;
        }
    }

    /**
     * Set the cookie with all pushed assets.
     */
    public function persist()
    {
        $doSetCookie = (bool)GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('script_style_push', 'enableOverpushPrevention');

        if ($doSetCookie && $this->changed) {
            $normalizedParams = $GLOBALS['TYPO3_REQUEST']->getAttribute('normalizedParams');
            $isHttps = $normalizedParams->isHttps();
            $cookieSecure = (bool)$GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieSecure'] && $isHttps;

            setcookie($this->cookieName, implode(',', $this->pushedAssets), $this->lifetime, '/', '', $cookieSecure, true);
        }
    }
}
