<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


// Adicionar Campos Personalizados
function emu_add_custom_meta_boxes() {
    add_meta_box('emu_attribution_meta', 'Detalhes da Atribuição', 'emu_attribution_meta_callback', 'emu_attribution');
}
add_action('add_meta_boxes', 'emu_add_custom_meta_boxes');

function emu_attribution_meta_callback($post) {
    // Recuperando os metadados do post
    $id_arquivo = get_post_meta($post->ID, '_id_arquivo', true);
    $autor = get_post_meta($post->ID, '_autor', true);
    $link_arquivo = get_post_meta($post->ID, '_link_arquivo', true);
    $tipo_arquivo = get_post_meta($post->ID, '_tipo_arquivo', true);
    ?>
    <p>
        <label>ID ou URL do Arquivo:</label>
        <input type="text" name="id_arquivo" value="<?php echo esc_attr($id_arquivo); ?>" class="widefat" />
    </p>
    <p>
        <label>Nome do Autor:</label>
        <input type="text" name="autor" value="<?php echo esc_attr($autor); ?>" class="widefat" />
    </p>
    <p>
        <label>Link do Arquivo:</label>
        <input type="url" name="link_arquivo" value="<?php echo esc_attr($link_arquivo); ?>" class="widefat" />
    </p>
    <p>
    <label>Tipo do Arquivo:</label>
    <select name="tipo_arquivo" class="widefat">
        <option value="Fonte" <?php selected($tipo_arquivo, 'Fonte'); ?>>Fonte</option>
        <option value="Imagem" <?php selected($tipo_arquivo, 'Imagem'); ?>>Imagem</option>
        <option value="Ícone" <?php selected($tipo_arquivo, 'Ícone'); ?>>Ícone</option>
        <option value="Vídeo" <?php selected($tipo_arquivo, 'Vídeo'); ?>>Vídeo</option>
        <option value="Música" <?php selected($tipo_arquivo, 'Música'); ?>>Música</option>
        <option value="Desconhecido" <?php selected($tipo_arquivo, 'Desconhecido'); ?>>Desconhecido</option>
    </select>
</p>

    <?php
}

// Salvar os Metadados
function emu_save_attribution_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['id_arquivo']) || !isset($_POST['autor']) || !isset($_POST['link_arquivo']) || !isset($_POST['tipo_arquivo'])) return;
    
    update_post_meta($post_id, '_id_arquivo', sanitize_text_field($_POST['id_arquivo']));
    update_post_meta($post_id, '_autor', sanitize_text_field($_POST['autor']));
    update_post_meta($post_id, '_link_arquivo', esc_url($_POST['link_arquivo']));
    update_post_meta($post_id, '_tipo_arquivo', sanitize_text_field($_POST['tipo_arquivo']));
}
add_action('save_post', 'emu_save_attribution_meta');

