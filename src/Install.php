<?php
/**
 * @brief adminmoredates, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Philippe aka amalgame
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace Dotclear\Plugin\adminmoredates;

use Dotclear\Core\Process;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        My::settings()->put('enabled', false, 'boolean', 'Enable plugin', false, true);
        My::settings()->put('creadt', false, 'boolean', 'Display creation date', false, true);
        My::settings()->put('upddt', false, 'boolean', 'Display update date', false, true);
        My::settings()->put('lists', false, 'boolean', 'Display on posts lists', false, true);
        My::settings()->put('posts', false, 'boolean', 'Display on post form', false, true);

        return true;
    }
}
