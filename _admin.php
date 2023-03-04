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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

// Add behaviour callback for post/page lists
dcCore::app()->addBehavior('adminColumnsListsV2', [adminmoredates::class, 'adminColumnsLists']);
dcCore::app()->addBehavior('adminPostListHeaderV2', [adminmoredates::class, 'adminPostListHeader']);
dcCore::app()->addBehavior('adminPostListValueV2', [adminmoredates::class, 'adminPostListValue']);
dcCore::app()->addBehavior('adminPagesListHeaderV2', [adminmoredates::class, 'adminPagesListHeader']);
dcCore::app()->addBehavior('adminPagesListValueV2', [adminmoredates::class, 'adminPagesListValue']);
