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
use Dotclear\Helper\Date;

class BackendBehaviors
{
    public static function adminColumnsLists($cols)
    {
        $cols['posts'][1]['upddt']  = [true, __('Update date')];
        $cols['posts'][1]['creadt'] = [true, __('Creation date')];
        $cols['pages'][1]['upddt']  = [true, __('Update date')];
        $cols['pages'][1]['creadt'] = [true, __('Creation date')];
    }

    private static function adminEntryListHeader($core, $rs, $cols)
    {
        $cols['upddt']  = '<th scope="col">' . __('Updated') . '</th>';
        $cols['creadt'] = '<th scope="col">' . __('Created') . '</th>';
    }

    public static function adminPostListHeader($rs, $cols)
    {
        self::adminEntryListHeader(dcCore::app(), $rs, $cols);
    }

    public static function adminPagesListHeader($rs, $cols)
    {
        self::adminEntryListHeader(dcCore::app(), $rs, $cols);
    }

    public static function adminEntryListValue($core, $rs, $cols)
    {
        $cols['upddt']  = '<td class="nowrap">' . Date::dt2str(__('%Y-%m-%d %H:%M'), $rs->post_upddt) . '</td>';
        $cols['creadt'] = '<td class="nowrap">' . Date::dt2str(__('%Y-%m-%d %H:%M'), $rs->post_creadt) . '</td>';
    }

    public static function adminPostListValue($rs, $cols)
    {
        self::adminEntryListValue(dcCore::app(), $rs, $cols);
    }

    public static function adminPagesListValue($rs, $cols)
    {
        self::adminEntryListValue(dcCore::app(), $rs, $cols);
    }

    public static function adminPostsSortbyCombo($container)
    {
        $container[0][__('Update date')]   = 'post_upddt';
        $container[0][__('Creation date')] = 'post_creadt';
    }
}
