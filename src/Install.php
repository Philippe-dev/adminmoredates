<?php
/**
 * @brief adminmoredates, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Philippe aka amalgame and contributors
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
        static::$init = My::checkContext(My::INSTALL);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        $settings = dcCore::app()->blog->settings->get(My::id());

        $settings->put('enabled', false, 'boolean', 'Enable plugin', false, true);
        $settings->put('creadt', false, 'boolean', 'Display creation date', false, true);
        $settings->put('upddt', false, 'boolean', 'Display update date', false, true);
        $settings->put('lists', false, 'boolean', 'Display on posts lists', false, true);
        $settings->put('posts', false, 'boolean', 'Display on post form', false, true);

        return true;
    }
}
