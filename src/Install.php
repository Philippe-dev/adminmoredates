<?php
/**
 * @brief adminmoredates, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Philippe
 *
 * @copyright philippe@dissitou.org
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace Dotclear\Plugin\adminmoredates;

use dcCore;
use dcNsProcess;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        $module = basename(dirname(__DIR__));
        $check  = dcCore::app()->newVersion($module, dcCore::app()->plugins->moduleInfo($module, 'version'));

        self::$init = defined('DC_CONTEXT_ADMIN') && $check;

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        $settings = dcCore::app()->blog->settings->adminmoredates;

        $settings->put('adminmoredates_enabled', false, 'boolean', 'Enable related entries', false, true);
        $settings->put('adminmoredates_creadt', false, 'boolean', 'Display creation date', false, true);
        $settings->put('adminmoredates_upddt', false, 'boolean', 'Display update date', false, true);

        return true;
    }
}
