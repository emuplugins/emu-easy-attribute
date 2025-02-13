<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Registrar Post Type 'emu_attribution'
function emu_register_attribution_post_type() {
    register_post_type('emu_attribution', [
        'labels' => [
            'name' => 'Atribuições',
            'singular_name' => 'Atribuição'
        ],
        'public' => false,
        'show_ui' => false, // Não exibe no painel
        'show_in_menu' => false, // Não aparece no menu
        'supports' => ['title', 'thumbnail'], // Suporte para título e thumbnail
        'menu_position' => 20,
        'menu_icon' => 'dashicons-admin-links',
        'has_archive' => false, // Desativa o arquivo para este tipo de post
        'publicly_queryable' => false, // Não pode ser acessado publicamente
        'exclude_from_search' => true, // Exclui das buscas
    ]);
}
add_action('init', 'emu_register_attribution_post_type');

// Função para excluir qualquer tipo de anexo quando o post for deletado
function emu_delete_post_attachments($post_id) {
    // Verifica se o post é do tipo 'emu_attribution'
    if (get_post_type($post_id) == 'emu_attribution') {
        // Exclui a imagem destacada (thumbnail)
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if ($thumbnail_id) {
            wp_delete_attachment($thumbnail_id, true); // Exclui o anexo permanentemente
        }

        // Exclui outros arquivos relacionados ao post (se houver)
        $file_url = get_post_meta($post_id, '_link_arquivo', true); // Link do arquivo
        if ($file_url) {
            // Obtém o ID do anexo associado ao link do arquivo
            $attachment_id = attachment_url_to_postid($file_url);
            
            // Verifica se o arquivo é um anexo e se o ID existe
            if ($attachment_id) {
                wp_delete_attachment($attachment_id, true); // Exclui o anexo permanentemente
            } else {
                // Caso não seja um anexo, verifica se o arquivo ainda existe fisicamente
                $file_path = get_attached_file($attachment_id);
                if (file_exists($file_path)) {
                    unlink($file_path); // Exclui o arquivo
                }
            }
        }
    }
}
add_action('before_delete_post', 'emu_delete_post_attachments');


