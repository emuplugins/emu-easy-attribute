<?php
/**
 * Plugin Name: Emu Easy Attribute
 * Description: Plugin para gerenciamento de atributos de arquivos.
 * Version: 1.0
 * Author: Seu Nome
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Incluir arquivos necessários
require_once plugin_dir_path(__FILE__) . 'update-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/option-page/core.php';
