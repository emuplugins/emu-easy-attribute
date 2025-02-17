<?php
/**
 * Plugin Name: Emu Easy Attribute
 * Description: Plugin para atribuição de ativos gratuitos
 * Version: 1.1.2
 * Author: Emu Plugins
 */
 if (!defined('ABSPATH')) exit;

 $plugin_slug = basename(__DIR__);
 if (substr($plugin_slug, -5) === '-main') {
     $plugin_slug = substr($plugin_slug, 0, -5);
 }
 $self_plugin_dir = basename(__DIR__);

// Impedir qualquer tentativa de carregar traduções aqui

add_action('init', function () use ($plugin_slug) {
    remove_action('init', 'load_plugin_textdomain', 10);
}, 9999);

add_filter('load_textdomain_mofile', function ($mofile, $domain) use ($plugin_slug) {
    // Impede o carregamento da tradução para o seu plugin, verificando o slug
    if ($domain === $plugin_slug) {
        return false;  // Retorna falso para não carregar o arquivo de tradução
    }
    return $mofile;
}, 10, 2);

// Carrega os arquivos necessários

require_once plugin_dir_path(__FILE__) . 'includes/post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/option-page/core.php';

// Sistema de atualização do plugin

require_once plugin_dir_path(__FILE__) . 'update-handler.php';

new Emu_Updater($plugin_slug, $self_plugin_dir);
