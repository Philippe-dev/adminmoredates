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
use dcBlog;
use dcNsProcess;
use form;
use dcAdmin;
use dcAuth;
use dcFavorites;
use dcPage;
use ArrayObject;
use Dotclear\Database\{
    Cursor,
    MetaRecord
};
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

        if ($settings->enabled) {
            if ($settings->lists) {
                dcCore::app()->addBehavior('adminColumnsListsV2', [BackendBehaviors::class, 'adminColumnsLists']);
                dcCore::app()->addBehavior('adminPostListHeaderV2', [BackendBehaviors::class, 'adminPostListHeader']);
                dcCore::app()->addBehavior('adminPostListValueV2', [BackendBehaviors::class, 'adminPostListValue']);
                dcCore::app()->addBehavior('adminPagesListHeaderV2', [BackendBehaviors::class, 'adminPagesListHeader']);
                dcCore::app()->addBehavior('adminPagesListValueV2', [BackendBehaviors::class, 'adminPagesListValue']);
                dcCore::app()->addBehavior('adminPostsSortbyCombo', [BackendBehaviors::class, 'adminPostsSortbyCombo']);
            }
            if ($settings->posts) {
                dcCore::app()->addBehavior('adminPostFormItems', [self::class, 'adminPostFormItems']);
                dcCore::app()->addBehavior('adminPageFormItems', [self::class, 'adminPostFormItems']);
                dcCore::app()->addBehavior('adminPostHeaders', [self::class,  'adminPostHeaders']);
                dcCore::app()->addBehavior('adminPageHeaders', [self::class,  'adminPostHeaders']);
                dcCore::app()->addBehavior('adminAfterPostUpdate', [self::class,  'adminAfterPostUpdate']);
                dcCore::app()->addBehavior('adminAfterPageUpdate', [self::class,  'adminAfterPostUpdate']);
            }
        }
        if (!empty($_REQUEST['id'])) {
            $params['post_id']         = $_REQUEST['id'];
            dcCore::app()->admin->post = dcCore::app()->blog->getPosts($params);

            dcCore::app()->admin->post_id            = (int) dcCore::app()->admin->post->post_id;
            dcCore::app()->admin->post_creadt        = (int) dcCore::app()->admin->post->post_creadt;
            dcCore::app()->admin->post_upddt        = (int) dcCore::app()->admin->post->post_upddt;
            dcCore::app()->admin->can_edit_page      = dcCore::app()->admin->post->isEditable();
        }
        if (!empty($_POST) && dcCore::app()->admin->can_edit_page) {
            // Format content

            if (empty($_POST['post_creadt'])) {
                dcCore::app()->admin->post_creadt = '';
            } else {
                try {
                    dcCore::app()->admin->post_creadt = strtotime($_POST['post_creadt']);
                    if (!dcCore::app()->admin->post_creadt || dcCore::app()->admin->post_creadt == -1) {
                        dcCore::app()->admin->bad_dt = true;

                        throw new Exception(__('Invalid publication date'));
                    }
                    dcCore::app()->admin->post_creadt = date('Y-m-d H:i', dcCore::app()->admin->post_creadt);
                } catch (Exception $e) {
                    dcCore::app()->error->add($e->getMessage());
                }
            }

            if (empty($_POST['post_upddt'])) {
                dcCore::app()->admin->post_upddt = '';
            } else {
                try {
                    dcCore::app()->admin->post_upddt = strtotime($_POST['post_upddt']);
                    if (!dcCore::app()->admin->post_upddt || dcCore::app()->admin->post_upddt == -1) {
                        dcCore::app()->admin->bad_dt = true;

                        throw new Exception(__('Invalid publication date'));
                    }
                    dcCore::app()->admin->post_upddt = date('Y-m-d H:i', dcCore::app()->admin->post_upddt);
                } catch (Exception $e) {
                    dcCore::app()->error->add($e->getMessage());
                }
            }
        }

        return true;
    }

    public static function adminAfterPostUpdate(Cursor $cur, ?int $post_id): void
    {
        //creation date

        if (!isset($_POST['post_creadt']) || $post_id === null) {
            return;
        }
        $cur = dcCore::app()->con->openCursor(dcCore::app()->prefix . dcBlog::POST_TABLE_NAME);
        $cur->post_creadt = dcCore::app()->admin->post_creadt ? $_POST['post_creadt'] : Date::dt2str(__('%Y-%m-%d %H:%M'), dcCore::app()->admin->post_creadt);

        $cur->update(
            'WHERE post_id = ' . dcCore::app()->admin->post_id . ' ' .
            "AND blog_id = '" . dcCore::app()->con->escapeStr(dcCore::app()->blog->id) . "' "
        );

        //update date

        if (!isset($_POST['post_upddt']) || $post_id === null) {
            return;
        }
        $cur = dcCore::app()->con->openCursor(dcCore::app()->prefix . dcBlog::POST_TABLE_NAME);
        $cur->post_upddt = dcCore::app()->admin->post_upddt ? $_POST['post_upddt'] : Date::dt2str(__('%Y-%m-%d %H:%M'), dcCore::app()->admin->post_upddt);

        $cur->update(
            'WHERE post_id = ' . dcCore::app()->admin->post_id . ' ' .
            "AND blog_id = '" . dcCore::app()->con->escapeStr(dcCore::app()->blog->id) . "' "
        );
    }

    public static function adminPostFormItems(ArrayObject $main, ArrayObject $sidebar, ?MetaRecord $post): void
    {
        if ($post !== null) {
            $settings = dcCore::app()->blog->settings->adminmoredates;

            $item = '<p><label for="post_dt">' . __('Publication date and hour') . '</label>' .
            form::datetime('post_dt', [
                'default' => Html::escapeHTML(Date::str('%Y-%m-%dT%H:%M', strtotime((string) $post->post_dt))),
                'class'   => (dcCore::app()->admin->bad_dt ? 'invalid' : ''),
            ]) .
            '</p>' .
            '<div class="more_dates"><label for="more_dates">' . __('More dates') . '</label>' .
                '<div id="more_dates">' ;

            if ($settings->upddt) {
                $item .= '<p><label for="post_upddt">' . __('Update date and hour') . '</label>' .
                form::datetime('post_upddt', [
                    'default' => Html::escapeHTML(Date::str('%Y-%m-%dT%H:%M', strtotime((string) $post->post_upddt))),
                    'class'   => (dcCore::app()->admin->bad_dt ? 'invalid' : ''),

                ]) .
                '</p>';
            }

            if ($settings->creadt) {
                $item .= '<p><label for="post_creadt">' . __('Creation date and hour') . '</label>' .
                form::datetime('post_creadt', [
                    'default' => Html::escapeHTML(Date::str('%Y-%m-%dT%H:%M', strtotime((string) $post->post_creadt))),
                    'class'   => (dcCore::app()->admin->bad_dt ? 'invalid' : ''),

                ]) .
                '</p>';
            }

            $item .= '</div>' .
            '</div>';

            if ($settings->creadt || $settings->upddt) {
                $sidebar['status-box']['items']['post_dt'] = $item;
            }
        }
    }

    public static function adminPostHeaders(): string
    {
        $url = dcCore::app()->blog->getQmarkURL() . 'pf=adminmoredates';

        return '<style type="text/css">' . "\n" .
        '.more_dates {margin: 0 0 1em 0;}' . "\n" .
        '.more_dates:first-of-type label {margin-top:.5em}' . "\n" .
        '.today_helper{min-width: 12em}' . "\n" .
        '</style>' .
        '<script src="' . $url . '/js/adminmoredates.js"><script>';
    }
}
