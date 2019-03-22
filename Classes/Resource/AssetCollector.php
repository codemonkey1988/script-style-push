<?php
declare(strict_types=1);
namespace Codemonkey1988\ScriptStylePush\Resource;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AssetCollector implements SingletonInterface
{
    /**
     * @var string
     */
    protected $html;

    /**
     * @var string
     */
    protected $additionalAssets;

    /**
     * @var string
     */
    protected $excludePattern;

    /**
     * @var \SplObjectStorage
     */
    protected $assets;

    /**
     * @param string $html The html to look for assets
     * @param string $additionalAssets Comma-separated string of asset urls
     * @param string $excludePattern A regex pattern to exclude files from getting pushed
     */
    public function __construct(string $html, string $additionalAssets = '', string $excludePattern = '')
    {
        $this->html = $html;
        $this->additionalAssets = $additionalAssets;
        $this->excludePattern = $excludePattern;
        $this->assets = new \SplObjectStorage();
    }

    /**
     * Fetch all assets from given html body and additional asset string.
     *
     * @return \SplObjectStorage
     */
    public function fetch(): \SplObjectStorage
    {
        if ($this->assets->count() === 0) {
            $this->fetchAssetsFromBody();
            $this->fetchAdditionalAssets();
        }

        return $this->assets;
    }

    protected function fetchAssetsFromBody()
    {
        preg_match_all('/href="([^"]+\.css[^"]*)"|src="([^"]+\.js[^"]*)"/', $this->html, $matches);
        $result = array_filter(array_merge($matches[1], $matches[2]));

        foreach ($result as $file) {
            $asset = new Asset($file);
            if (!$this->assetIsExcluded($asset)) {
                $this->assets->attach($asset);
            }
        }
    }

    protected function fetchAdditionalAssets()
    {
        $additionalAssets = GeneralUtility::trimExplode(',', $this->additionalAssets);
        $absPathLength = strlen(PATH_site);

        foreach ($additionalAssets as $file) {
            $file = GeneralUtility::getFileAbsFileName($file);

            if ($file) {
                $file = substr($file, $absPathLength);
                $absFilePrefix = $GLOBALS['TSFE']->absRefPrefix;

                $asset = new Asset('/' . ltrim($absFilePrefix, '/') . ltrim($file, '/'));

                if (!$this->assetIsExcluded($asset)) {
                    $this->assets->attach($asset);
                }
            }
        }
    }

    /**
     * @param Asset $asset
     * @return bool
     */
    protected function assetIsExcluded(Asset $asset): bool
    {
        if (!$this->excludePattern) {
            return false;
        }

        return (bool)preg_match('/' . $this->excludePattern . '/', $asset->getFile());
    }
}
