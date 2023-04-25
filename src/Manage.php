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
        if (is_null(dcCore::app()->blog->settings->adminmoredates->adminmoredates_enabled)) {
            try {
                // Add default settings values if necessary
                $settings = dcCore::app()->blog->settings->adminmoredates;

                $settings->put('adminmoredates_enabled', false, 'boolean', 'Enable related entries', false, true);
                $settings->put('adminmoredates_creadt', false, 'boolean', 'Display creation date', false, true);
                $settings->put('adminmoredates_upddt', false, 'boolean', 'Display update date', false, true);

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

        $settings = dcCore::app()->blog->settings->adminmoredates;

        // Saving configurations
        if (isset($_POST['save'])) {
            $settings->put('adminmoredates_enabled', !empty($_POST['adminmoredates_enabled']));
            $settings->put('adminmoredates_creadt', !empty($_POST['adminmoredates_creadt']));
            $settings->put('adminmoredates_upddt', !empty($_POST['adminmoredates_upddt']));

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

        $settings = dcCore::app()->blog->settings->adminmoredates;

        dcPage::openModule(
            __('Admin More Dates'),
            dcPage::jsConfirmClose('config-form')
        );

        echo dcPage::breadcrumb(
            [
                Html::escapeHTML(dcCore::app()->blog->name) => '',
                __('Admin More Dates')                      => '',
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
            '<p><label class="classic" for="adminmoredates_enabled">' .
            form::checkbox('adminmoredates_enabled', '1', $settings->adminmoredates_enabled) .
            __('Activate plugin on this blog') . '</label></p>' .
        '</div>' .
        '<div class="fieldset"><h3>' . __('Options') . '</h3>' .
            '<p><label class="classic" for="adminmoredates_creadt">' .
            form::checkbox('adminmoredates_creadt', '1', $settings->adminmoredates_creadt) .
            __('Display posts creation date') . '</label></p>' .
            '<p><label class="classic" for="adminmoredates_upddt">' .
            form::checkbox('adminmoredates_upddt', '1', $settings->adminmoredates_upddt) .
            __('Display posts update date') . '</label></p>' .
        '</div>';

        echo
        '<p class="clear"><input type="submit" name="save" value="' . __('Save configuration') . '" />' . dcCore::app()->formNonce() . '</p>' .
        '</form>' .

        dcPage::helpBlock('adminmoredatesconfig');
        dcPage::closeModule();
    }
}
