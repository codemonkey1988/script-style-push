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
     * @var \SplObjectStorage
     */
    protected $assets;

    /**
     * @param string $html The html to look for assets
     * @param string $additionalAssets Comma-separated string of asset urls
     */
    public function __construct(string $html, string $additionalAssets = '')
    {
        $this->html = $html;
        $this->additionalAssets = $additionalAssets;
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
            $this->assets->attach(new Asset($file));
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
                $this->assets->attach($asset);
            }
        }
    }
}
