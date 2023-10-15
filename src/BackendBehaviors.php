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
use Dotclear\Helper\Date;

class BackendBehaviors
{
    public static function adminColumnsLists($cols)
    {
        if (My::settings()->creadt) {
            $cols['posts'][1]['creadt'] = [true, __('Creation date')];
            $cols['pages'][1]['creadt'] = [true, __('Creation date')];
        }
        if (My::settings()->upddt) {
            $cols['posts'][1]['upddt'] = [true, __('Update date')];
            $cols['pages'][1]['upddt'] = [true, __('Update date')];
        }
    }

    private static function adminEntryListHeader($rs, $cols)
    {
        if (My::settings()->creadt) {
            $cols['creadt'] = '<th scope="col">' . __('Created') . '</th>';
        }
        if (My::settings()->upddt) {
            $cols['upddt'] = '<th scope="col">' . __('Updated') . '</th>';
        }
    }

    public static function adminPostListHeader($rs, $cols)
    {
        self::adminEntryListHeader($rs, $cols);
    }

    public static function adminPagesListHeader($rs, $cols)
    {
        self::adminEntryListHeader($rs, $cols);
    }

    public static function adminEntryListValue($rs, $cols)
    {
        if (My::settings()->creadt) {
            $cols['creadt'] = '<td class="nowrap">' . Date::dt2str(__('%Y-%m-%d %H:%M'), $rs->post_creadt) . '</td>';
        }
        if (My::settings()->upddt) {
            $cols['upddt'] = '<td class="nowrap">' . Date::dt2str(__('%Y-%m-%d %H:%M'), $rs->post_upddt) . '</td>';
        }
    }

    public static function adminPostListValue($rs, $cols)
    {
        self::adminEntryListValue($rs, $cols);
    }

    public static function adminPagesListValue($rs, $cols)
    {
        self::adminEntryListValue($rs, $cols);
    }

    public static function adminPostsSortbyCombo($container)
    {
        if (My::settings()->creadt) {
            $container[0][__('Creation date')] = 'post_creadt';
        }
        if (My::settings()->upddt) {
            $container[0][__('Update date')] = 'post_upddt';
        }
    }
}
