<?php
/**
 * Plugin Name: Emu Easy Attribute
 * Description: Plugin para atribuição de ativos gratuitos
 * Version: 1.1.0
 * Author: Emu Plugins
 */

 if (!defined('ABSPATH')) exit;

 require_once 'update_handler.php';
 
 // Configuração do auto-update para o próprio plugin
 $plugin_slug = basename(__DIR__);
 if (substr($plugin_slug, -5) === '-main') {
     $plugin_slug = substr($plugin_slug, 0, -5);
 }
 $self_plugin_dir = basename(__DIR__);
 new Emu_Updater($plugin_slug, $self_plugin_dir);
 
require_once plugin_dir_path(__FILE__) . 'update-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/option-page/core.php';
