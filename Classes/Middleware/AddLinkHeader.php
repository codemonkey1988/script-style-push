<?php
declare(strict_types=1);
namespace Codemonkey1988\ScriptStylePush\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AddLinkHeader
 */
class AddLinkHeader implements MiddlewareInterface
{
    /**
     * @var array
     */
    protected $assets = [];

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $site = $request->getAttribute('site');

        // Run this hook only if there is no http referrer. When there is one, that means that this template is loaded by an
        // ajax request and should not contain data to be pushed.
        if ($this->isEnabled() && $site instanceof Site && !GeneralUtility::getIndpEnv('HTTP_REFERRER')) {
            $response->getBody()->rewind();
            $this->baseUrl = (string)$site->getBase();

            $this->addAssetsFromDocument($response->getBody()->getContents());
            $this->addAssetsFromSiteConfiguration($site);

            $response = $response->withHeader('Link', implode(', ', $this->assets));
        }

        return $response;
    }

    /**
     * Add link headers that are defined in typoscript.
     *
     * @param Site $site
     * @return void
     */
    protected function addAssetsFromSiteConfiguration(Site $site)
    {
        $assetConfiguration = $site->getConfiguration()['assetsToPush'];

        if (!empty($assetConfiguration)) {
            $assets = GeneralUtility::trimExplode(',', $assetConfiguration);
            $absPathLength = strlen(PATH_site);

            foreach ($assets as $file) {
                if ($this->fileCanBePushed($file)) {
                    $file = GeneralUtility::getFileAbsFileName($file);

                    if ($file) {
                        $file          = substr($file, $absPathLength);
                        $absFilePrefix = $GLOBALS['TSFE']->absRefPrefix;

                        $fileUrl = '/' . ltrim($absFilePrefix, '/') . ltrim($file, '/');
                        $this->addAsset($fileUrl);
                    }
                }
            }
        }
    }

    /**
     * Parse the output content for stylesheets and script files.
     *
     * @param string $body
     * @return void
     */
    protected function addAssetsFromDocument(string $body)
    {
        preg_match_all('/href="([^"]+\.css[^"]*)"|src="([^"]+\.js[^"]*)"/', $body, $matches);
        $result = array_filter(array_merge($matches[1], $matches[2]));

        foreach ($result as $file) {
            if ($this->fileCanBePushed($file)) {
                if (!$this->isExternalFile($file)) {
                    $file = '/' . ltrim($file, '/');
                }

                $this->addAsset($file);
            }
        }
    }

    /**
     * @param string $fileUrl
     * @return void
     * @throws \UnexpectedValueException
     */
    protected function addAsset($fileUrl)
    {
        if (!$this->isExternalFile($fileUrl)) {
            $fileUrl = $this->baseUrl . ltrim($fileUrl, '/');
            $this->assets[] = '<' . $fileUrl . '>; ' . $this->getConfigForFiletype($fileUrl);
        }
    }

    /**
     * @param string $file
     * @return bool
     */
    protected function fileCanBePushed($file)
    {
        $components = parse_url($file);
        if (!isset($components['host']) && !isset($components['scheme'])) {
            return true;
        } elseif (isset($components['scheme']) && $components['scheme'] === 'EXT') {
            return true;
        } elseif ($this->isExternalFile($file) && !empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_scriptstylepush.']['settings.']['domains.'])) {
            // Check if the domain is a valid push domain.
            if (is_array($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_scriptstylepush.']['settings.']['domains.'])) {
                foreach ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_scriptstylepush.']['settings.']['domains.'] as $domain) {
                    if (trim($domain) === $components['host']) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param string $file
     * @return bool
     */
    protected function isExternalFile($file)
    {
        $components = parse_url($file);

        return !empty($components['host']) && !empty($components['scheme']);
    }

    /**
     * @param string $file
     * @return string
     */
    protected function getConfigForFiletype($file)
    {
        $extension = end(explode('.', parse_url($file, PHP_URL_PATH)));
        switch ($extension) {
            case "css":
                return 'rel=preload; as=style';
                break;
            case "js":
                return 'rel=preload; as=script';
                break;
            case 'svg':
            case 'gif':
            case 'png':
            case 'jpg':
            case 'jpeg':
                return 'rel=preload; as=image';
                break;
            case 'mp4':
                return 'rel=preload; as=media';
                break;
            case 'woff':
                return 'rel=preload; as=font; type=font/woff; crossorigin';
            case 'woff2':
                return 'rel=preload; as=font; type=font/woff2; crossorigin';
            case 'eot':
                return 'rel=preload; as=font; type=font/eot; crossorigin';
            case 'ttf':
                return 'rel=preload; as=font; type=font/ttf; crossorigin';
            default:
                // Do not push the resource when conent type does not match.
                return 'rel=preload; nopush';
        }
    }

    /**
     * Checks if the plugin is enabled by typoscript.
     *
     * @return bool
     */
    protected function isEnabled()
    {
        return (bool)getenv('SCRIPT_STYLE_PUSH_ENABLED');
    }
}