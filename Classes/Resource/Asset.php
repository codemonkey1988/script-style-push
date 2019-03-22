<?php
declare(strict_types=1);
namespace Codemonkey1988\ScriptStylePush\Resource;

/**
 * Class Asset
 */
class Asset
{
    /**
     * @var array
     */
    protected $assetTypes = [
        'css' => 'style',
        'js' => 'script',
        'svg' => 'image',
        'gif' => 'image',
        'png' => 'image',
        'jpg' => 'image',
        'jpeg' => 'image',
        'bmp' => 'image',
        'tiff' => 'image',
        'mp4' => 'media',
        'ogv' => 'media',
        'woff' => 'font',
        'woff2' => 'font',
        'eot' => 'font',
        'ttf' => 'font',
    ];

    /**
     * @var array
     */
    protected $types = [
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'eot' => 'font/eot',
        'ttf' => 'font/ttf',
    ];

    /**
     * @var array
     */
    protected $extensionsForCrossorigin = [
        'woff',
        'woff2',
        'eot',
        'ttf',
    ];

    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
        $this->extension = end(explode('.', parse_url($file, PHP_URL_PATH)));
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Checks is a file path is a local file or an external file.
     *
     * @return bool
     */
    public function isLocal(): bool
    {
        $components = parse_url($this->file);

        return empty($components['host']) && empty($components['scheme']);
    }

    /**
     * @return string
     */
    public function getAssetType(): string
    {
        return $this->assetTypes[$this->extension] ?? '';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->types[$this->extension] ?? '';
    }

    /**
     * @return bool
     */
    public function isPushEnabled(): bool
    {
        return $this->isLocal();
    }

    /**
     * @return bool
     */
    public function isCrossorigin(): bool
    {
        return in_array($this->extension, $this->extensionsForCrossorigin);
    }
}
