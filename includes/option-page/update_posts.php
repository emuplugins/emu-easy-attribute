<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Processar o envio de arquivos via AJAX
add_action('wp_ajax_emu_add_files_via_ajax', 'emu_add_files_via_ajax');

function emu_add_files_via_ajax() {
    // Verifica o nonce de segurança para uploads
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
                    'mp4', 'avi', 'mov', 'mkv', 'flv'            => 'Vídeo',
                    'mp3', 'wav', 'ogg', 'flac'                    => 'Música',
                    'ico', 'svg', 'eps', 'ai'                      => 'Ícone',
                    'ttf', 'otf', 'woff', 'woff2'                  => 'Fonte',
                    default                                       => NULL,
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

// Adiciona o nonce de segurança para uploads ao frontend
add_action('admin_footer', 'emu_add_nonce_to_admin_footer');
function emu_add_nonce_to_admin_footer() {
    ?>
    <script>
        // Este nonce será usado na operação de upload de arquivos
        var emu_add_files_nonce = "<?php echo wp_create_nonce('emu_add_files_nonce'); ?>";
    </script>
    <?php
}

// Atualizar campos via AJAX
add_action('wp_ajax_emu_update_attribution_field', 'emu_update_attribution_field');

function emu_update_attribution_field() {
    // Verifica o nonce de segurança para atualização de campos
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'emu_update_attribution_nonce')) {
        wp_send_json_error('Erro de segurança');
    }

    // Verifica permissões do usuário
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Permissão negada');
    }

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $field = isset($_POST['field']) ? sanitize_text_field($_POST['field']) : '';
    $value = isset($_POST['value']) ? sanitize_text_field($_POST['value']) : '';

    if (!$post_id || !$field) {
        wp_send_json_error('Parâmetros ausentes');
    }

    // Mapear o campo para a meta_key correta
    $meta_key = '';
    switch ($field) {
        case 'author':
            $meta_key = '_autor';
            break;
        case 'file_link':
            $meta_key = '_link_arquivo';
            $value = esc_url_raw($value); // Validar URL
            break;
        case 'file_type':
            $meta_key = '_tipo_arquivo';
            // Converter valor para formato adequado (ex: 'icone' para 'Ícone')
            $value_map = [
                'icone'       => 'Ícone',
                'imagem'      => 'Imagem',
                'video'       => 'Vídeo',
                'musica'      => 'Música',
                'fonte'       => 'Fonte',
            ];
            $value = $value_map[$value] ?? $value;
            break;
        default:
            wp_send_json_error('Campo inválido');
    }

    // Atualizar a meta do post
    update_post_meta($post_id, $meta_key, $value);

    wp_send_json_success('Campo atualizado');
}

