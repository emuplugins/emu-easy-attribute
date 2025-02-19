<?php
/**
 * Plugin Name: Emu Easy Attribute
 * Description: Plugin para atribuição de ativos gratuitos
 * Version: 1.1.2
 * Author: Emu Plugins
 */
 if (!defined('ABSPATH')) exit;

 
// ==============================================================================================================
// UPDATE SYSTEM
// ==============================================================================================================
 
require_once plugin_dir_path(__FILE__) . 'update-handler.php';

// Load backend files

require_once plugin_dir_path(__FILE__) . 'includes/post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/option-page/core.php';