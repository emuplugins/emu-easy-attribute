<?php
/**
 * Plugin Name: Emu Easy Attribute
 * Description: Plugin para atribuição de ativos gratuitos
 * Version: 1.0.8
 * Author: Emu Plugins
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Obtendo o nome do plugin e removendo o sufixo '-main'
$plugin_slug = rtrim(plugin_dir_path(__FILE__), '/') ;
$plugin_slug = basename($plugin_slug);
if (substr($plugin_slug, -5) === '-main') {
    $plugin_slug = substr($plugin_slug, 0, -5); // Remove o sufixo '-main'
}

require_once plugin_dir_path(__FILE__) . 'update-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/option-page/core.php';
