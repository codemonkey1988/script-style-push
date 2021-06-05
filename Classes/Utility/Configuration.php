<?php

declare(strict_types=1);

/*
 * This file is part of the "script_style_push" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\ScriptStylePush\Utility;

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
