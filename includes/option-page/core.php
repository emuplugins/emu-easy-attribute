<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Registra o menu da página de opções
function emu_register_options_page() {
    add_menu_page(
        'Atribuições',                      // Título da página
        'Atribuições',                      // Nome do menu
        'manage_options',                   // Capacidade do usuário
        'emu_options_page',                 // Slug do menu
        'emu_render_options_page',          // Função que renderiza a página
        'dashicons-upload',                 // Ícone do menu
        20                                  // Posição no menu
    );
}

add_action('admin_menu', 'emu_register_options_page');

require_once plugin_dir_path(__FILE__) . 'templates/render_page.php';
require_once plugin_dir_path(__FILE__) . 'update_posts.php';
