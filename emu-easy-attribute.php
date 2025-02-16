<?php
/**
 * Plugin Name: Emu Easy Attribute
 * Description: Plugin para atribuição de ativos gratuitos
 * Version: 1.1.0
 * Author: Emu Plugins
 */

require_once plugin_dir_path(__FILE__) . 'includes/post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/option-page/core.php';

// Sistema de atualização do plugin

$plugin_slug = basename(__DIR__);
if (substr($plugin_slug, -5) === '-main') {
    $plugin_slug = substr($plugin_slug, 0, -5);
}
$self_plugin_dir = basename(__DIR__);

require_once plugin_dir_path(__FILE__) . 'update-handler.php';

new Emu_Updater($plugin_slug, $self_plugin_dir);
add_action('upgrader_process_complete', 'emu_handle_plugin_update', 10, 2);
