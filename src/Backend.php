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
use form;
use dcAdmin;
use dcAuth;
use dcFavorites;
use dcPage;
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Html;

class Backend extends dcNsProcess
{
    public static function init(): bool
    {
        self::$init = defined('DC_CONTEXT_ADMIN');

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        dcCore::app()->menu[dcAdmin::MENU_BLOG]->addItem(
            __('Admin More Dates'),
            dcCore::app()->adminurl->get('admin.plugin.adminmoredates'),
            [dcPage::getPF('adminmoredates/icon.svg'), dcPage::getPF('adminmoredates/icon-dark.svg')],
            preg_match('/' . preg_quote(dcCore::app()->adminurl->get('admin.plugin.adminmoredates')) . '(&.*)?$/', $_SERVER['REQUEST_URI']),
            dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([dcAuth::PERMISSION_CONTENT_ADMIN]), dcCore::app()->blog->id)
        );

        /* Register favorite */
        dcCore::app()->addBehavior('adminDashboardFavoritesV2', function (dcFavorites $favs) {
            $favs->register('adminmoredates', [
                'title'       => __('Admin More Dates'),
                'url'         => dcCore::app()->adminurl->get('admin.plugin.adminmoredates'),
                'small-icon'  => [dcPage::getPF('adminmoredates/icon.svg'), dcPage::getPF('adminmoredates/icon-dark.svg')],
                'large-icon'  => [dcPage::getPF('adminmoredates/icon.svg'), dcPage::getPF('adminmoredates/icon-dark.svg')],
                'permissions' => dcCore::app()->auth->makePermissions([dcAuth::PERMISSION_CONTENT_ADMIN]),
            ]);
        });

        $settings = dcCore::app()->blog->settings->adminmoredates;

        if ($settings->adminmoredates_enabled) {
            dcCore::app()->addBehavior('adminColumnsListsV2', [BackendBehaviors::class, 'adminColumnsLists']);
            dcCore::app()->addBehavior('adminPostListHeaderV2', [BackendBehaviors::class, 'adminPostListHeader']);
            dcCore::app()->addBehavior('adminPostListValueV2', [BackendBehaviors::class, 'adminPostListValue']);
            dcCore::app()->addBehavior('adminPagesListHeaderV2', [BackendBehaviors::class, 'adminPagesListHeader']);
            dcCore::app()->addBehavior('adminPagesListValueV2', [BackendBehaviors::class, 'adminPagesListValue']);
            dcCore::app()->addBehavior('adminPostsSortbyCombo', [BackendBehaviors::class, 'adminPostsSortbyCombo']);

            dcCore::app()->addBehavior('adminPostFormItems', [self::class, 'adminPostFormItems']);
            dcCore::app()->addBehavior('adminPostHeaders', [self::class,  'postHeaders']);
        }

        return true;
    }

    public static function adminPostFormItems($main, $sidebar, $post)
    {
        if ($post !== null) {
            dcCore::app()->admin->post_upddt  = $post->post_upddt;
            dcCore::app()->admin->post_creadt = $post->post_creadt;

            $settings = dcCore::app()->blog->settings->adminmoredates;

            $item = '<p><label for="post_dt">' . __('Publication date and hour') . '</label>' .
            form::datetime('post_dt', [
                'default' => Html::escapeHTML(Date::str('%Y-%m-%dT%H:%M', strtotime((string) dcCore::app()->admin->post_dt))),
                'class'   => (dcCore::app()->admin->bad_dt ? 'invalid' : ''),
            ]) .
            '</p>' .
            '<div><label class="more_dates" for="more_dates">' . __('More dates') . '</label>' .
                '<div id="more_dates">' ;

            if ($settings->adminmoredates_upddt) {
                $item .= '<p><label for="post_upddt">' . __('Update date and hour') . '</label>' .
                form::datetime('post_upddt', [
                    'default'  => Html::escapeHTML(Date::str('%Y-%m-%dT%H:%M', strtotime((string) dcCore::app()->admin->post_upddt))),
                    'class'    => (dcCore::app()->admin->bad_dt ? 'invalid' : 'maximal'),
                    'disabled' => true,
                ]) .
                '</p>';
            }

            if ($settings->adminmoredates_creadt) {
                $item .= '<p><label for="post_creadt">' . __('Creation date and hour') . '</label>' .
                form::datetime('post_creadt', [
                    'default'  => Html::escapeHTML(Date::str('%Y-%m-%dT%H:%M', strtotime((string) dcCore::app()->admin->post_creadt))),
                    'class'    => (dcCore::app()->admin->bad_dt ? 'invalid' : 'maximal'),
                    'disabled' => true,
                ]) .
                '</p>';
            }

            $item .= '</div>' .
            '</div>';

            if ($settings->adminmoredates_creadt || $settings->adminmoredates_upddt) {
                $sidebar['status-box']['items']['post_dt'] = $item;
            }
        }
    }

    public static function postHeaders(): string
    {
        return
        '<script>' . "\n" .
        '$(document).ready(function() {' . "\n" .
            '$("#more_dates")' . "\n" .
            '.parent()' . "\n" .
            '.children("label")' . "\n" .
            '.toggleWithLegend($("#more_dates").parent().children().not("label"), {' . "\n" .
                'user_pref: "dcx_post_more_dates",' . "\n" .
                'legend_click: true,' . "\n" .
            '});' . "\n" .
        '});' . "\n" .
        '</script>' .
        '<style type="text/css">' . "\n" .
        '.more_dates {' . "\n" .
        'margin-bottom: .8em;' . "\n" .
        '}' . "\n" .
        '</style>';
    }
}
