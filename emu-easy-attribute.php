<?php
/**
 * Plugin Name: Emu Easy Attribute
 * Description: Plugin para atribuição de ativos gratuitos
 * Version: 1.1.2
 * Author: Emu Plugins
 */
if(!is_defined('ABSPATH')){
    exit;
}

// Cancela qualquer tentativa de traduzir o plugin

add_action('init', function () {
    load_plugin_textdomain('emu-easy-attribute', false, false);
});

// Carrega os arquivos necessários

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
