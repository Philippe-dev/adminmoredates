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
use dcPage;
use Exception;
use form;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Network\Http;

class Manage extends dcNsProcess
{
    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        if (is_null(dcCore::app()->blog->settings->get(My::id())->enabled)) {
            try {
                // Add default settings values if necessary
                $settings = dcCore::app()->blog->settings->get(My::id());

                $settings->put('enabled', false, 'boolean', 'Enable plugin', false, true);
                $settings->put('creadt', false, 'boolean', 'Display creation date', false, true);
                $settings->put('upddt', false, 'boolean', 'Display update date', false, true);
                $settings->put('lists', false, 'boolean', 'Display on posts lists', false, true);
                $settings->put('posts', false, 'boolean', 'Display on post form', false, true);

                dcCore::app()->blog->triggerBlog();
                Http::redirect(dcCore::app()->admin->getPageURL());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        self::$init = true;

        return self::$init;
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        $settings = dcCore::app()->blog->settings->get(My::id());

        // Saving configurations
        if (isset($_POST['save'])) {
            $settings->put('enabled', !empty($_POST['enabled']));
            $settings->put('creadt', !empty($_POST['creadt']));
            $settings->put('upddt', !empty($_POST['upddt']));
            $settings->put('lists', !empty($_POST['lists']));
            $settings->put('posts', !empty($_POST['posts']));

            dcCore::app()->blog->triggerBlog();
            Http::redirect(dcCore::app()->admin->getPageURL() . '&upd=1');
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::$init) {
            return;
        }

        $settings = dcCore::app()->blog->settings->get(My::id());

        dcPage::openModule(
            My::name(),
            dcPage::jsConfirmClose('config-form')
        );

        echo dcPage::breadcrumb(
            [
                Html::escapeHTML(dcCore::app()->blog->name) => '',
                My::name()                                  => '',
            ]
        ) .
        dcPage::notices();

        if (isset($_GET['upd']) && $_GET['upd'] == 1) {
            dcPage::success(__('Configuration successfully saved'));
        }

        // Config tab

        echo
        '<form action="' . dcCore::app()->admin->getPageURL() . '" method="post" id="config-form">' .
        '<div class="fieldset"><h3>' . __('Activation') . '</h3>' .
            '<p><label class="classic" for="enabled">' .
            form::checkbox('enabled', '1', $settings->enabled) .
            __('Activate plugin on this blog') . '</label></p>' .
        '</div>' .
        '<div class="fieldset"><h3>' . __('Dates') . '</h3>' .
            '<p><label class="classic" for="creadt">' .
            form::checkbox('creadt', '1', $settings->creadt) .
            __('Display posts creation date') . '</label></p>' .
            '<p><label class="classic" for="upddt">' .
            form::checkbox('upddt', '1', $settings->upddt) .
            __('Display posts update date') . '</label></p>' .
        '</div>' . '<div class="fieldset"><h3>' . __('Places') . '</h3>' .
            '<p><label class="classic" for="lists">' .
            form::checkbox('lists', '1', $settings->lists) .
            __('Display dates on posts lists') . '</label></p>' .
            '<p><label class="classic" for="posts">' .
            form::checkbox('posts', '1', $settings->posts) .
            __('Display dates on post form') . '</label></p>' .
        '</div>';

        echo
        '<p class="clear"><input type="submit" name="save" value="' . __('Save configuration') . '" />' . dcCore::app()->formNonce() . '</p>' .
        '</form>' .

        dcPage::helpBlock('adminmoredatesconfig');
        dcPage::closeModule();
    }
}
