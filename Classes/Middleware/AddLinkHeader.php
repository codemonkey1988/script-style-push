<?php

declare(strict_types=1);

/*
 * This file is part of the "script_style_push" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ScriptStylePush\Middleware;

use Codemonkey1988\ScriptStylePush\Cache\AssetCache;
use Codemonkey1988\ScriptStylePush\Resource\Asset;
use Codemonkey1988\ScriptStylePush\Resource\AssetCollector;
use Codemonkey1988\ScriptStylePush\Utility\Configuration;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AddLinkHeader implements MiddlewareInterface
{
    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $site = $request->getAttribute('site');

        if ($this->isHttp2Request($request) && !Configuration::isPushDisabled() && $site instanceof Site && !$this->isXhrRequest()) {
            $response->getBody()->rewind();
            $body = $response->getBody()->getContents();
            $additionalAssets = $site->getConfiguration()['assetsToPush'] ?? '';
            $excludePattern = $site->getConfiguration()['excludePattern'] ?? '';
            $assetCollector = new AssetCollector($body, $additionalAssets, $excludePattern);
            $headerContent = $this->renderHeaderContent($assetCollector->fetch());
            if ($headerContent) {
                $response = $response->withHeader('Link', $headerContent);
            }
        }

        return $response;
    }

    /**
     * @param \SplObjectStorage $assets
     * @return string
     */
    protected function renderHeaderContent(\SplObjectStorage $assets): string
    {
        $assetsToPush = [];
        $assetCache = GeneralUtility::makeInstance(AssetCache::class);
        $assetCache->load();

        /** @var Asset $asset */
        foreach ($assets as $asset) {
            if ($asset->isPushEnabled() && $assetCache->shouldPush($asset)) {
                $assetsToPush[] = $this->buildHeaderForSingleAsset($asset);
                $assetCache->add($asset);
            }
        }

        $assetCache->persist();

        return implode(',', $assetsToPush);
    }

    /**
     * @param Asset $asset
     * @return string
     */
    protected function buildHeaderForSingleAsset(Asset $asset)
    {
        $parts = [
            '<' . $asset->getFile() . '>',
            'rel=preload',
        ];

        if ($asset->getAssetType()) {
            $parts[] = 'as=' . $asset->getAssetType();
        }
        if ($asset->getType()) {
            $parts[] = 'type=' . $asset->getType();
        }
        if ($asset->isCrossorigin()) {
            $parts[] = 'crossorigin';
        }

        return implode(';', $parts);
    }

    /**
     * Checks is the current request may be an XHR request.
     * We typically do not need to push assets in xhr requests.
     *
     * @return bool
     */
    protected function isXhrRequest(): bool
    {
        return (bool)GeneralUtility::getIndpEnv('HTTP_REFERRER');
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    protected function isHttp2Request(ServerRequestInterface $request): bool
    {
        return $request->getUri()->getScheme() === 'https';
    }
}
