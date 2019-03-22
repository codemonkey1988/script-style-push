<?php
declare(strict_types=1);
namespace Codemonkey1988\ScriptStylePush\Utility;

/**
 * Class Configuration
 */
class Configuration
{
    /**
     * @return bool
     */
    public static function isPushDisabled(): bool
    {
        return (bool)getenv('SCRIPT_STYLE_PUSH_DISABLED');
    }
}
