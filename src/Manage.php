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
use Dotclear\Core\Backend\Page;
use Dotclear\Core\Backend\Notices;
use Dotclear\Core\Process;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Html;

class Manage extends Process
{
    public static function init(): bool
    {
        self::status(My::checkContext(My::MANAGE));

        return self::status();
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        // Saving configurations
        if (isset($_POST['save'])) {
            My::settings()->put('enabled', !empty($_POST['enabled']));
            My::settings()->put('creadt', !empty($_POST['creadt']));
            My::settings()->put('upddt', !empty($_POST['upddt']));
            My::settings()->put('lists', !empty($_POST['lists']));
            My::settings()->put('posts', !empty($_POST['posts']));

            dcCore::app()->blog->triggerBlog();
            My::redirect(['upd' => 1]);
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        Page::openModule(
            My::name(),
            Page::jsConfirmClose('config-form')
        );

        echo Page::breadcrumb(
            [
                Html::escapeHTML(dcCore::app()->blog->name) => '',
                My::name()                                  => '',
            ]
        ) .
        Notices::getNotices();

        if (isset($_GET['upd']) && $_GET['upd'] == 1) {
            Notices::success(__('Configuration successfully saved'));
        }

        echo
        (new Form('config-form'))
            ->action(dcCore::app()->admin->getPageURL())
            ->method('post')
            ->fields([
                (new Fieldset('activation'))
                ->legend((new Legend(__('Activation'))))
                ->fields([
                    (new Para())->items([
                        (new Checkbox('enabled', My::settings()->enabled))
                            ->value(1)
                            ->label((new Label(__('Activate plugin on this blog'), Label::INSIDE_TEXT_AFTER))),
                    ]),
                ]),
                (new Fieldset('dates'))
                ->legend((new Legend(__('Dates'))))
                ->fields([
                    (new Para())->items([
                        (new Checkbox('creadt', My::settings()->creadt))
                            ->value(1)
                            ->label((new Label(__('Display posts creation date'), Label::INSIDE_TEXT_AFTER))),
                    ]),
                    (new Para())->items([
                        (new Checkbox('upddt', My::settings()->upddt))
                            ->value(1)
                            ->label((new Label(__('Display posts update date'), Label::INSIDE_TEXT_AFTER))),
                    ]),
                ]),
                (new Fieldset('places'))
                ->legend((new Legend(__('Places'))))
                ->fields([
                    (new Para())->items([
                        (new Checkbox('lists', My::settings()->lists))
                            ->value(1)
                            ->label((new Label(__('Display dates on posts lists'), Label::INSIDE_TEXT_AFTER))),
                    ]),
                    (new Para())->items([
                        (new Checkbox('posts', My::settings()->posts))
                            ->value(1)
                            ->label((new Label(__('Display dates on post form'), Label::INSIDE_TEXT_AFTER))),
                    ]),
                ]),
                // Submit
                (new Para())->items([
                    (new Submit(['save']))
                        ->value(__('Save configuration')),
                    dcCore::app()->formNonce(false),
                ]),
            ])
        ->render();

        Page::helpBlock('adminmoredatesconfig');
        Page::closeModule();
    }
}
