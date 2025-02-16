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

// Verifica se o plugin principal está ativo
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
if (!is_plugin_active('emu-update-core/emu-update-core.php')) {

    if (!defined('EMU_UPDATE_HANDLER')) {
        define('EMU_UPDATE_HANDLER', __FILE__); // Se o principal não estiver ativo, este assume
    }

    function emu_load_on_update_pages() {
        global $pagenow;

        $update_pages = ['update-core.php', 'update.php', 'plugins.php', 'themes.php'];

        if (in_array($pagenow, $update_pages)) {

            $plugin_slug = basename(__DIR__);
            if (substr($plugin_slug, -5) === '-main') {
                $plugin_slug = substr($plugin_slug, 0, -5);
            }
            $self_plugin_dir = basename(__DIR__);

            require_once plugin_dir_path(__FILE__) . 'update-handler.php';

            new Emu_Updater($plugin_slug, $self_plugin_dir);
            
            add_action('upgrader_process_complete', 'emu_handle_plugin_update', 10, 2);
        }
    }

    add_action('admin_init', 'emu_load_on_update_pages');
}

