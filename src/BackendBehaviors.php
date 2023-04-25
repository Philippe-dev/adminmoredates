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
        $settings = dcCore::app()->blog->settings->adminmoredates;

        if ($settings->adminmoredates_creadt) {
            $cols['posts'][1]['creadt'] = [true, __('Creation date')];
            $cols['pages'][1]['creadt'] = [true, __('Creation date')];
        }
        if ($settings->adminmoredates_upddt) {
            $cols['posts'][1]['upddt'] = [true, __('Update date')];
            $cols['pages'][1]['upddt'] = [true, __('Update date')];
        }
    }

    private static function adminEntryListHeader($core, $rs, $cols)
    {
        $settings = dcCore::app()->blog->settings->adminmoredates;

        if ($settings->adminmoredates_creadt) {
            $cols['creadt'] = '<th scope="col">' . __('Created') . '</th>';
        }
        if ($settings->adminmoredates_upddt) {
            $cols['upddt'] = '<th scope="col">' . __('Updated') . '</th>';
        }
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
        $settings = dcCore::app()->blog->settings->adminmoredates;

        if ($settings->adminmoredates_creadt) {
            $cols['creadt'] = '<td class="nowrap">' . Date::dt2str(__('%Y-%m-%d %H:%M'), $rs->post_creadt) . '</td>';
        }
        if ($settings->adminmoredates_upddt) {
            $cols['upddt'] = '<td class="nowrap">' . Date::dt2str(__('%Y-%m-%d %H:%M'), $rs->post_upddt) . '</td>';
        }
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
        $settings = dcCore::app()->blog->settings->adminmoredates;

        if ($settings->adminmoredates_creadt) {
            $container[0][__('Creation date')] = 'post_creadt';
        }
        if ($settings->adminmoredates_upddt) {
            $container[0][__('Update date')] = 'post_upddt';
        }
    }
}
