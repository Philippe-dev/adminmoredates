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

use dcBlog;
use form;
use ArrayObject;
use Exception;
use Dotclear\App;
use Dotclear\Core\Backend\Favorites;
use Dotclear\Core\Backend\Utility;
use Dotclear\Core\Process;
use Dotclear\Database\Cursor;
use Dotclear\Database\MetaRecord;
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Html;

class Backend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        App::behavior()->addBehaviors([
            'adminDashboardFavoritesV2' => function (Favorites $favs) {
                $favs->register(My::id(), [
                    'title'       => My::name(),
                    'url'         => My::manageUrl(),
                    'small-icon'  => My::icons(),
                    'large-icon'  => My::icons(),
                    'permissions' => App::auth()->makePermissions([
                        App::auth()::PERMISSION_ADMIN,
                    ]),
                ]);
            },
        ]);

        My::addBackendMenuItem(Utility::MENU_BLOG);

        if (My::settings()->enabled) {
            if (My::settings()->lists) {
                App::behavior()->addBehavior('adminColumnsListsV2', [BackendBehaviors::class, 'adminColumnsLists']);
                App::behavior()->addBehavior('adminPostListHeaderV2', [BackendBehaviors::class, 'adminPostListHeader']);
                App::behavior()->addBehavior('adminPostListValueV2', [BackendBehaviors::class, 'adminPostListValue']);
                App::behavior()->addBehavior('adminPagesListHeaderV2', [BackendBehaviors::class, 'adminPagesListHeader']);
                App::behavior()->addBehavior('adminPagesListValueV2', [BackendBehaviors::class, 'adminPagesListValue']);
                App::behavior()->addBehavior('adminPostsSortbyCombo', [BackendBehaviors::class, 'adminPostsSortbyCombo']);
            }
            if (My::settings()->posts) {
                App::behavior()->addBehavior('adminPostFormItems', [self::class, 'adminPostFormItems']);
                App::behavior()->addBehavior('adminPageFormItems', [self::class, 'adminPostFormItems']);
                App::behavior()->addBehavior('adminPostHeaders', [self::class,  'adminPostHeaders']);
                App::behavior()->addBehavior('adminPageHeaders', [self::class,  'adminPostHeaders']);
                App::behavior()->addBehavior('adminAfterPostUpdate', [self::class,  'adminAfterPostUpdate']);
                App::behavior()->addBehavior('adminAfterPageUpdate', [self::class,  'adminAfterPostUpdate']);
            }
        }

        if (!empty($_REQUEST['id'])) {
            $params['post_id']         = $_REQUEST['id'];
            App::backend()->post = App::blog()->getPosts($params);

            App::backend()->post_id       = (int) App::backend()->post->post_id;
            App::backend()->post_creadt   = (int) App::backend()->post->post_creadt;
            App::backend()->post_upddt    = (int) App::backend()->post->post_upddt;
            App::backend()->can_edit_page = App::backend()->post->isEditable();
        }

        if (!empty($_POST) && App::backend()->can_edit_page) {
            // Format content

            if (empty($_POST['post_creadt'])) {
                App::backend()->post_creadt = '';
            } else {
                try {
                    App::backend()->post_creadt = strtotime($_POST['post_creadt']);
                    if (!App::backend()->post_creadt || App::backend()->post_creadt == -1) {
                        App::backend()->bad_dt = true;

                        throw new Exception(__('Invalid publication date'));
                    }
                    App::backend()->post_creadt = date('Y-m-d H:i', App::backend()->post_creadt);
                } catch (Exception $e) {
                    App::error()->add($e->getMessage());
                }
            }

            if (empty($_POST['post_upddt'])) {
                App::backend()->post_upddt = '';
            } else {
                try {
                    App::backend()->post_upddt = strtotime($_POST['post_upddt']);
                    if (!App::backend()->post_upddt || App::backend()->post_upddt == -1) {
                        App::backend()->bad_dt = true;

                        throw new Exception(__('Invalid publication date'));
                    }
                    App::backend()->post_upddt = date('Y-m-d H:i', App::backend()->post_upddt);
                } catch (Exception $e) {
                    App::error()->add($e->getMessage());
                }
            }
        }

        return true;
    }

    public static function adminAfterPostUpdate(Cursor $cur, ?int $post_id): void
    {
        if (is_null($post_id)) {
            return;
        }

        //creation date

        if (!isset($_POST['post_creadt'])) {
            return;
        }
        $cur              = App::con()->openCursor(App::con()->prefix() . dcBlog::POST_TABLE_NAME);
        $cur->post_creadt = App::backend()->post_creadt ? $_POST['post_creadt'] : Date::dt2str(__('%Y-%m-%d %H:%M'), App::backend()->post_creadt);

        $cur->update(
            'WHERE post_id = ' . App::backend()->post_id . ' ' .
            "AND blog_id = '" . App::con()->escapeStr(App::blog()->id) . "' "
        );

        //update date

        if (!isset($_POST['post_upddt'])) {
            return;
        }
        $cur             = App::con()->openCursor(App::con()->prefix() . dcBlog::POST_TABLE_NAME);
        $cur->post_upddt = App::backend()->post_upddt ? $_POST['post_upddt'] : Date::dt2str(__('%Y-%m-%d %H:%M'), App::backend()->post_upddt);

        $cur->update(
            'WHERE post_id = ' . App::backend()->post_id . ' ' .
            "AND blog_id = '" . App::con()->escapeStr(App::blog()->id) . "' "
        );
    }

    public static function adminPostFormItems(ArrayObject $main, ArrayObject $sidebar, ?MetaRecord $post): void
    {
        if ($post !== null) {
            $item = '<p><label for="post_dt">' . __('Publication date and hour') . '</label>' .
            form::datetime('post_dt', [
                'default' => Html::escapeHTML(Date::str('%Y-%m-%dT%H:%M', strtotime((string) $post->post_dt))),
                'class'   => (App::backend()->bad_dt ? 'invalid' : ''),
            ]) .
            '</p>' .
            '<div class="more_dates"><label for="more_dates">' . __('More dates') . '</label>' .
                '<div id="more_dates">' ;

            if (My::settings()->upddt) {
                $item .= '<p><label for="post_upddt">' . __('Update date and hour') . '</label>' .
                form::datetime('post_upddt', [
                    'default' => Html::escapeHTML(Date::str('%Y-%m-%dT%H:%M', strtotime((string) $post->post_upddt))),
                    'class'   => (App::backend()->bad_dt ? 'invalid' : ''),
                ]) .
                '</p>';
            }

            if (My::settings()->creadt) {
                $item .= '<p><label for="post_creadt">' . __('Creation date and hour') . '</label>' .
                form::datetime('post_creadt', [
                    'default' => Html::escapeHTML(Date::str('%Y-%m-%dT%H:%M', strtotime((string) $post->post_creadt))),
                    'class'   => (App::backend()->bad_dt ? 'invalid' : ''),
                ]) .
                '</p>';
            }

            $item .= '</div>' .
            '</div>';

            if (My::settings()->creadt || My::settings()->upddt) {
                $sidebar['status-box']['items']['post_dt'] = $item;
            }
        }
    }

    public static function adminPostHeaders(): string
    {
        return
        '<style type="text/css">' . "\n" .
        '.more_dates {margin: 0 0 1em 0;}' . "\n" .
        '.more_dates:first-of-type label {margin-top:.5em}' . "\n" .
        '.today_helper{min-width: 12.5em}' . "\n" .
        '</style>' .
        My::jsLoad('adminmoredates.js');
    }
}
