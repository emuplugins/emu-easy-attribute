<?php
/**
 * Plugin Name: Emu Easy Attribute
 * Description: Plugin para atribuição de ativos gratuitos
 * Version: 1.1.0
 * Author: Emu Plugins
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


// Caminho absoluto do arquivo principal do plugin

$plugin_slug = basename(__DIR__);  // Diretório do plugin
if (substr($plugin_slug, -5) === '-main') {
    $plugin_slug = substr($plugin_slug, 0, -5); // Remove o sufixo '-main'
}
$plugin_dir = basename(__DIR__); // Mantemos o diretório original para referência
require_once plugin_dir_path(__FILE__) . 'update-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/option-page/core.php';
