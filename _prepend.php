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
if (!defined('DC_RC_PATH')) {
    return;
}
// public

if (!defined('DC_CONTEXT_ADMIN')) {
    return false;
}
// admin

Clearbricks::lib()->autoload(['adminmoredates' => __DIR__ . '/inc/class.adminmoredates.php']);
