<?php

// Processar o envio de arquivos via AJAX
add_action('wp_ajax_emu_add_files_via_ajax', 'emu_add_files_via_ajax');

function emu_add_files_via_ajax() {
    // Verifica o nonce de segurança
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'emu_add_files_nonce')) {
        wp_send_json_error('Erro de segurança');
    }

    // Verifica se o usuário está logado
    if (!is_user_logged_in()) {
        wp_send_json_error('Você precisa estar logado para enviar arquivos.');
    }

    if (isset($_FILES['emu_files']) && !empty($_FILES['emu_files']['name'][0])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        
        $files = $_FILES['emu_files'];
        $upload_results = [];

        // Processa todos os arquivos e prepara os dados
        for ($i = 0; $i < count($files['name']); $i++) {
            $file = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i]
            ];

            // Enviar o arquivo para a biblioteca de mídia
            $upload = wp_handle_upload($file, ['test_form' => false]);
            if (isset($upload['file'])) {
                // Processar o tipo de arquivo
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $file_type = match (strtolower($file_extension)) {
                    'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp' => 'Imagem',
                    'mp4', 'avi', 'mov', 'mkv', 'flv' => 'Vídeo',
                    'mp3', 'wav', 'ogg', 'flac' => 'Música',
                    'ico', 'svg', 'eps', 'ai' => 'Ícone',
                    'ttf', 'otf', 'woff', 'woff2' => 'Fonte',
                    default => 'Desconhecido',
                };

                $upload_results[] = [
                    'file'      => $upload['file'],
                    'url'       => $upload['url'],
                    'file_name' => basename($file['name']),
                    'file_type' => $file_type
                ];
            }
        }

        // Cria os posts em batch após o upload dos arquivos
        foreach ($upload_results as $result) {
            $post_id = wp_insert_post([
                'post_title'  => "emu-attribution-" . $result['file_name'],
                'post_type'   => 'emu_attribution',
                'post_status' => 'publish'
            ]);

            // Adiciona o arquivo como anexo
            $attachment_id = wp_insert_attachment([
                'post_mime_type' => mime_content_type($result['file']),
                'post_title'     => basename($result['file']),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ], $result['file'], $post_id);

            // Regenera metadados de imagem
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $result['file']);
            wp_update_attachment_metadata($attachment_id, $attachment_data);

            // Define imagem destacada
            set_post_thumbnail($post_id, $attachment_id);

            // Atualiza metadados do post
            update_post_meta($post_id, '_id_arquivo', sanitize_text_field($result['file_name']));
            update_post_meta($post_id, '_autor', 'Autor do Arquivo');
            update_post_meta($post_id, '_link_arquivo', esc_url($result['url']));
            update_post_meta($post_id, '_tipo_arquivo', $result['file_type']);
        }

        wp_send_json_success('Arquivos enviados e posts criados com sucesso');
    } else {
        wp_send_json_error('Por favor, envie pelo menos um arquivo.');
    }
}

// Adiciona o nonce de segurança ao frontend
add_action('admin_footer', 'emu_add_nonce_to_admin_footer');
function emu_add_nonce_to_admin_footer() {
    ?>
    <script>
        var emu_add_files_nonce = "<?php echo wp_create_nonce('emu_add_files_nonce'); ?>";
    </script>
    <?php
}
