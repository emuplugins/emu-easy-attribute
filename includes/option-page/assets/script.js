document.addEventListener('DOMContentLoaded', function() {
    // Criar um elemento de carregando
    const loadingElement = document.createElement('div');
    loadingElement.className = 'loading-indicator';
    loadingElement.innerText = 'Carregando...';
    loadingElement.style.display = 'none';
    loadingElement.style.position = 'absolute';
    loadingElement.style.top = '50%';
    loadingElement.style.left = '50%';
    loadingElement.style.transform = 'translate(-50%, -50%)';
    loadingElement.style.padding = '10px';
    loadingElement.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
    loadingElement.style.color = '#fff';
    loadingElement.style.borderRadius = '5px';
    document.body.appendChild(loadingElement);

    // Função para mostrar/ocultar o "carregando"
    function toggleLoading(show) {
        loadingElement.style.display = show ? 'block' : 'none';
    }

    // Lida com os inputs que já possuem a classe "editable"
    const editableInputs = document.querySelectorAll('.editable');
    editableInputs.forEach(function(input) {
        input.setAttribute('readonly', 'readonly');

        input.addEventListener('click', function() {
            input.removeAttribute('readonly');
        });

        input.addEventListener('blur', function() {
            input.setAttribute('readonly', 'readonly');
            const newValue = input.value;
            const postId = input.getAttribute('data-post-id');
            const field = input.getAttribute('data-field');

            const data = new URLSearchParams();
            data.append('action', 'emu_update_attribution_field');
            data.append('post_id', postId);
            data.append('field', field);
            data.append('value', newValue);
            data.append('security', emu_nonce);

            // Exibir indicador de carregamento
            toggleLoading(true);

            fetch(ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: data
            })
            .then(response => response.json())
            .then(responseData => {
                toggleLoading(false);
                if (!responseData.success) {
                    alert('Erro ao atualizar: ' + (responseData.message || ''));
                }
            })
            .catch(error => {
                toggleLoading(false);
                console.error('Erro:', error);
            });
        });

        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                input.blur();
            }
        });
    });

    // Lida com os selects que possuem a classe "editable-select"
    const editableSelects = document.querySelectorAll('.editable-select');
    editableSelects.forEach(function(select) {
        select.addEventListener('blur', function() {
            const newValue = select.value;
            const postId = select.getAttribute('data-post-id');
            const field = select.getAttribute('data-field');

            const data = new URLSearchParams();
            data.append('action', 'emu_update_attribution_field');
            data.append('post_id', postId);
            data.append('field', field);
            data.append('value', newValue);
            data.append('security', emu_nonce);

            // Exibir indicador de carregamento
            toggleLoading(true);

            fetch(ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: data
            })
            .then(response => response.json())
            .then(responseData => {
                toggleLoading(false);
                if (!responseData.success) {
                    alert('Erro ao atualizar: ' + (responseData.message || ''));
                }
            })
            .catch(error => {
                toggleLoading(false);
                console.error('Erro:', error);
            });
        });

        select.addEventListener('change', function() {
            select.blur();
        });
    });

    // Lida com os checkboxes e o botão de excluir
    const checkboxes = document.querySelectorAll('.delete-post-ids');
    const deleteButton = document.querySelector('.delete-selected');
    const selectAllButton = document.getElementById('select-all');

    function toggleDeleteButtonDisplay() {
        deleteButton.style.display = Array.from(checkboxes).some(checkbox => checkbox.checked) ? 'block' : 'none';
    }

    toggleDeleteButtonDisplay();

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleDeleteButtonDisplay);
    });

    selectAllButton.addEventListener('click', function() {
        checkboxes.forEach(checkbox => checkbox.checked = true);
        toggleDeleteButtonDisplay();
    });

    // Lida com o envio de arquivos
    const form = document.querySelector('form');
    const fileInput = document.getElementById('emu_files');
    const submitButton = form.querySelector('[type="submit"]');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Desabilita a interação com o corpo da página
        document.body.style.pointerEvents = 'none';

        if (fileInput.files.length > 0) {
            const formData = new FormData();
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append('emu_files[]', fileInput.files[i]);
            }
            formData.append('action', 'emu_add_files_via_ajax');
            formData.append('security', emu_add_files_nonce);

            // Exibir indicador de carregamento
            toggleLoading(true);
            submitButton.setAttribute('disabled', 'disabled');

            fetch(ajaxurl, {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(responseData => {
                toggleLoading(false);
                submitButton.removeAttribute('disabled');
                document.body.style.pointerEvents = 'auto'; // Restaura a interação com o corpo da página
                if (responseData.success) {
                    // Recarrega a página
                    location.reload();
                    form.reset();
                } else {
                    alert('Erro: ' + responseData.data);
                }
            })
            .catch(error => {
                toggleLoading(false);
                submitButton.removeAttribute('disabled');
                document.body.style.pointerEvents = 'auto'; // Restaura a interação com o corpo da página
                alert('Erro ao enviar os arquivos');
            });
        } else {
            alert('Por favor, selecione ao menos um arquivo');
        }
    });
});
 // Exibe o spinner e desabilita o pointer events
 function startUpload() {
    document.getElementById('loading-spinner').style.display = 'block';
    document.body.style.pointerEvents = 'none';  // Desabilita interação do usuário
}

// Esconde o spinner e habilita o pointer events
function endUpload() {
    document.getElementById('loading-spinner').style.display = 'none';
    document.body.style.pointerEvents = 'auto';  // Restaura interação do usuário
}

// Captura o envio do formulário
document.querySelector('form').addEventListener('submit', function(event) {
    startUpload(); // Inicia o carregamento
    // Se o envio for assíncrono, usamos AJAX para quando terminar o upload
    event.preventDefault(); // Previne o envio padrão do formulário
    
    var formData = new FormData(this);
    
    // Envio via AJAX
    fetch('<?php echo esc_url(admin_url("admin-ajax.php")); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        endUpload(); // Finaliza o carregamento
        // Aqui você pode adicionar outras lógicas, como mensagens de sucesso ou erro
    })
    .catch(error => {
        console.error('Erro no envio:', error);
        endUpload(); // Finaliza o carregamento em caso de erro
    });
});