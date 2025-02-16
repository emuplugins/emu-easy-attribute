<?php
/**
 * Plugin Name: Emu Easy Attribute
 * Description: Plugin para atribuição de ativos gratuitos
 * Version: 1.1.0
 * Author: Emu Plugins
 */

 if (!defined('ABSPATH')) exit;

// Carrega funções de atualização apenas na página de atualizações do wordpress

function emu_load_on_update_pages() {
    global $pagenow;

    $update_pages = ['update-core.php', 'update.php', 'plugins.php', 'themes.php'];

    if (in_array($pagenow, $update_pages)) {

        require_once plugin_dir_path(__FILE__) . 'update-handler.php';
        // Configuração do auto-update para o próprio plugin
        $plugin_slug = basename(__DIR__);
        if (substr($plugin_slug, -5) === '-main') {
            $plugin_slug = substr($plugin_slug, 0, -5);
        }
        $self_plugin_dir = basename(__DIR__);

        new Emu_Updater($plugin_slug, $self_plugin_dir);
        add_action('upgrader_process_complete', 'emu_handle_plugin_update', 10, 2);

    }
}

require_once plugin_dir_path(__FILE__) . 'includes/post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/option-page/core.php';