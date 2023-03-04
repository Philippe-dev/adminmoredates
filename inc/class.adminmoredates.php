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
class adminmoredates
{
    public static function adminColumnsLists($cols)
    {
        $cols['posts'][1]['upddt'] = [true, __('Updated')];
        $cols['posts'][2]['creadt'] = [true, __('Created')];
        $cols['pages'][1]['upddt'] = [true, __('Updated')];
        $cols['pages'][2]['creadt'] = [true, __('Created')];
    }

    private static function adminEntryListHeader($core, $rs, $cols)
    {
        $cols['upddt'] = '<th scope="col">' . __('Updated') . '</th>';
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
        $cols['upddt'] = '<td class="nowrap">' . dt::dt2str(__('%Y-%m-%d %H:%M'), $rs->post_upddt) . '</td>';
        $cols['creadt'] = '<td class="nowrap">' . dt::dt2str(__('%Y-%m-%d %H:%M'), $rs->post_creadt) . '</td>';
    }

    public static function adminPostListValue($rs, $cols)
    {
        self::adminEntryListValue(dcCore::app(), $rs, $cols);
    }

    public static function adminPagesListValue($rs, $cols)
    {
        self::adminEntryListValue(dcCore::app(), $rs, $cols);
    }
}
