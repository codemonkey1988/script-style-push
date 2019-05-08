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
    const DEFAULT_COOKIE_NAME = 'typo3_ssp_assets';
    const DEFAULT_COOKIE_LIFETIME = 7;

    /**
     * @var bool
     */
    protected $enabled;

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
     * AssetCache constructor.
     */
    public function __construct()
    {
        $configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('script_style_push');
        $this->pushedAssets = [];
        $this->cookieName = $configuration['overpushPreventionCookieName'] ?? self::DEFAULT_COOKIE_NAME;
        $this->lifetime = (int)($configuration['overpushPreventionCookieLifetime'] ?? self::DEFAULT_COOKIE_LIFETIME) * 86400;
        $this->enabled = (bool)$configuration['enableOverpushPrevention'] ?? true;
    }

    /**
     * Loads the current cache items.
     */
    public function load()
    {
        $this->pushedAssets = $this->removeVersionNumberFromAssets($this->readCache($this->cookieName));
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
        $assetPath = $this->removeVersionNumberFromAsset($asset->getFile());

        return !in_array($assetPath, $this->pushedAssets);
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
        }
    }

    /**
     * Set the cookie with all pushed assets.
     */
    public function persist()
    {
        if ($this->enabled && $this->readCache($this->cookieName) !== $this->pushedAssets) {
            $this->writeCache($this->cookieName, $this->pushedAssets, $this->lifetime);
        }
    }

    /**
     * @param string $identifier
     * @return array
     */
    protected function readCache(string $identifier): array
    {
        $cookieContent = $_COOKIE[$identifier] ?? '';

        return GeneralUtility::trimExplode(',', $cookieContent);
    }

    /**
     * @param string $identifier
     * @param array $value
     * @param int $lifetime
     */
    protected function writeCache(string $identifier, array $value, int $lifetime = 0)
    {
        $normalizedParams = $GLOBALS['TYPO3_REQUEST']->getAttribute('normalizedParams');
        $isHttps = $normalizedParams->isHttps();
        $cookieSecure = (bool)$GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieSecure'] && $isHttps;

        setcookie($identifier, implode(',', $value), $lifetime, '/', '', $cookieSecure, true);
    }

    /**
     * @param array $assets
     * @return array
     */
    protected function removeVersionNumberFromAssets(array $assets): array
    {
        $assets = array_map([$this, 'removeVersionNumberFromAsset'], $assets);

        return array_filter(array_unique($assets));
    }

    /**
     * @param string $asset
     * @return string
     */
    protected function removeVersionNumberFromAsset(string $asset): string
    {
        if (!$this->assetHasVersionNumber($asset)) {
            return $asset;
        }

        if ($this->isVersionNumberEmbedded()) {
            return preg_replace('/^(.*)(\.\d{10})(\..*)$/i', '$1$3', $asset);
        }

        // We know that the asset has exactly 10 numbers as version string and a leading ? = 11 characters.
        return substr($asset, 0, strlen($asset)-11);
    }

    /**
     * @param string $asset
     * @return bool
     */
    protected function assetHasVersionNumber(string $asset): bool
    {
        $pattern = '/\?\d{10}$/i';

        if ($this->isVersionNumberEmbedded()) {
            $pattern = '/\.\d{10}\..*/i';
        }

        return (bool)preg_match($pattern, $asset);
    }

    /**
     * @return bool
     */
    protected function isVersionNumberEmbedded(): bool
    {
        return $GLOBALS['TYPO3_CONF_VARS']['FE']['versionNumberInFilename'] === 'embed';
    }
}
