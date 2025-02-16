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

// Inicialização otimizada (fora da classe)
add_action('admin_init', function() {
    // Carrega apenas no admin e verifica necessidade
    if (!current_user_can('update_plugins')) return;

    $plugin_slug = basename(__DIR__);
    if (substr($plugin_slug, -5) === '-main') {
        $plugin_slug = substr($plugin_slug, 0, -5);
    }
    $self_plugin_dir = basename(__DIR__);

    require_once plugin_dir_path(__FILE__) . 'update-handler.php';

    // Self Update
    new Emu_Updater($plugin_slug, $self_plugin_dir);
    
// Força verificação de atualizações
add_action('admin_init', function() {
    wp_update_plugins();
});
// Atualizações de terceiros
add_action('load-update-core.php', 'emu_load_plugin_updates', 6);
add_action('load-plugins.php', 'emu_load_plugin_updates', 6);

});
