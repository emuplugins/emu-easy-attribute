<?php
// Função para renderizar a página de opções
function emu_render_options_page() {
?>
<div class="wrap">
    <h1>Atribuições</h1>
    <!-- Formulário para upload de arquivos -->
    <form method="post" enctype="multipart/form-data">
        <h2>Adicionar Nova Atribuição</h2>
        <p>
            <label for="emu_files">Enviar Arquivos:</label>
            <input type="file" name="emu_files[]" id="emu_files" multiple />
        </p>
        <input type="hidden" name="security" value="<?php echo wp_create_nonce('emu_add_files_nonce'); ?>" />
        <input type="submit" name="emu_add_new" value="Adicionar" class="button-primary" />
    </form>
    <!-- Preloader (estilize conforme sua necessidade) -->
        <div id="preloader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999; text-align: center; padding-top: 20%;">
            <img src="caminho/para/spinner.gif" alt="Carregando...">
        </div>

        <!-- Exibição do progresso do upload -->
        <div id="upload-progress" style="display: none; text-align: center; margin-top: 10px;"></div>

    <!-- Formulário para exclusão de posts -->
    <form method="post">
        <!-- Adiciona o nonce também para exclusão -->
        <input type="hidden" name="security" value="<?php echo wp_create_nonce('emu_add_files_nonce'); ?>" />
        <button type="button" id="select-all" class="button-secondary" style="margin-bottom: 20px;">Selecionar Todos</button>
        <button type="submit" name="delete_selected" class="button-primary delete-selected" style="margin-bottom: 20px; display:none">Excluir Selecionados</button>
        <ul style="display:flex; flex-wrap: wrap;">
        <?php
        // Obter todas as postagens de atribuição
        $attributions = get_posts(['post_type' => 'emu_attribution', 'numberposts' => -1]);
        foreach ($attributions as $att) {
            $post_id   = $att->ID;
            $image     = get_the_post_thumbnail_url($post_id); // URL da imagem destacada
            $author    = get_post_meta($post_id, '_autor', true); // Autor
            $file_link = get_post_meta($post_id, '_link_arquivo', true); // Link do arquivo
            $file_type = get_post_meta($post_id, '_tipo_arquivo', true); // Tipo de arquivo
        ?>
        <li style="list-style-type: none; margin-bottom: 20px; width: 100%; max-width: 400px;">
            <input type="checkbox" name="delete_post_ids[]" class="delete-post-ids" value="<?php echo esc_attr($post_id); ?>" />
            <?php if ($image): ?>
                <img src="<?php echo esc_url($image); ?>" alt="Imagem da atribuição" style="max-width: 100%; height: auto; margin-bottom: 10px;">
            <?php endif; ?>
            <ul style="margin-left: 20px;">
                <li>
                    <strong>Autor:</strong> 
                    <input type="text" class="editable" data-post-id="<?php echo esc_attr($post_id); ?>" data-field="author" value="<?php echo esc_attr($author); ?>" readonly />
                </li>
                <li>
                    <strong>Link para o Arquivo:</strong> 
                    <input type="text" class="editable" data-post-id="<?php echo esc_attr($post_id); ?>" data-field="file_link" value="<?php echo esc_attr($file_link); ?>" readonly />
                </li>
                <li>
                    <strong>Tipo de Arquivo:</strong>
                    <select class="editable-select" data-post-id="<?php echo esc_attr($post_id); ?>" data-field="file_type">
                        <option value="icone" <?php selected($file_type, 'Ícone'); ?>>Ícone</option>
                        <option value="imagem" <?php selected($file_type, 'Imagem'); ?>>Imagem</option>
                        <option value="video" <?php selected($file_type, 'Vídeo'); ?>>Vídeo</option>
                        <option value="musica" <?php selected($file_type, 'Música'); ?>>Música</option>
                        <option value="fonte" <?php selected($file_type, 'Fonte'); ?>>Fonte</option>
                        
                    </select>
                </li>
            </ul>
        </li>
        <?php
        }
        ?>
        </ul>
    </form>
</div>

<?php
    // Lidar com a exclusão de postagens selecionadas
    if ( isset($_POST['delete_selected']) && isset($_POST['delete_post_ids']) ) {
        // Verificar o nonce de segurança antes de prosseguir com a exclusão
        if ( isset($_POST['security']) && wp_verify_nonce($_POST['security'], 'emu_add_files_nonce') ) {
            $post_ids_to_delete = array_map('intval', $_POST['delete_post_ids']);
            foreach ($post_ids_to_delete as $post_id) {
                wp_delete_post($post_id, true); // Deleta permanentemente o post
            }
            // Atualizar a página para mostrar os posts deletados
            echo '<meta http-equiv="refresh" content="0">';
        } else {
            wp_send_json_error('Erro de segurança');
        }
    }
}




if (isset($_GET['page']) && $_GET['page'] === 'emu_options_page') {
// Carregar o script no painel
function emu_enqueue_scripts() {
    wp_enqueue_script('emu-script', plugin_dir_url(__DIR__) . 'assets/script.js', array(), null, true);
}
add_action('admin_enqueue_scripts', 'emu_enqueue_scripts');

// Adicionar nonce para atualização de campos
add_action('admin_footer', 'emu_add_update_nonce');
function emu_add_update_nonce() {
    ?>
    <script>
        var emu_update_nonce = { nonce: "<?php echo wp_create_nonce('emu_update_attribution_nonce'); ?>" };
    </script>
    <?php
}
    ?>
    <script>
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
    <?php
}
