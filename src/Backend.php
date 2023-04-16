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
use dcNsProcess;

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

        dcCore::app()->addBehavior('adminColumnsListsV2', [BackendBehaviors::class, 'adminColumnsLists']);
        dcCore::app()->addBehavior('adminPostListHeaderV2', [BackendBehaviors::class, 'adminPostListHeader']);
        dcCore::app()->addBehavior('adminPostListValueV2', [BackendBehaviors::class, 'adminPostListValue']);
        dcCore::app()->addBehavior('adminPagesListHeaderV2', [BackendBehaviors::class, 'adminPagesListHeader']);
        dcCore::app()->addBehavior('adminPagesListValueV2', [BackendBehaviors::class, 'adminPagesListValue']);
        dcCore::app()->addBehavior('adminPostsSortbyCombo', [BackendBehaviors::class, 'adminPostsSortbyCombo']);

        return true;
    }
}
