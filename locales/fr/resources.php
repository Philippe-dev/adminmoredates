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
if (!isset(dcCore::app()->resources['help']['adminmoredatesconfig'])) {
    dcCore::app()->resources['help']['adminmoredatesconfig'] = dirname(__FILE__) . '/help/adminmoredatesconfig.html';
}
