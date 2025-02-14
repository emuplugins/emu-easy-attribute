document.addEventListener('DOMContentLoaded', function () {
    const editableInputs = document.querySelectorAll('.editable');
    const editableSelects = document.querySelectorAll('.editable-select');

    function salvarCampo(element) {
        const newValue = element.value;
        const postId = element.getAttribute('data-post-id');
        const field = element.getAttribute('data-field');
        
        if (!postId || !field) return;

        const data = new URLSearchParams();
        data.append('action', 'emu_update_attribution_field');
        data.append('post_id', postId);
        data.append('field', field);
        data.append('value', newValue);
        data.append('security', emu_update_nonce.nonce);

        fetch(ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: data
        })
        .then(response => response.json())
        .then(responseData => {
            if (!responseData.success) {
                alert('Erro ao atualizar: ' + (responseData.message || 'Erro desconhecido.'));
            }
        })
        .catch(error => console.error('Erro:', error));
    }

    // Manipula os inputs editáveis
    editableInputs.forEach(function (input) {
        input.setAttribute('readonly', 'readonly');

        input.addEventListener('click', function () {
            input.removeAttribute('readonly');
        });

        input.addEventListener('blur', function () {
            salvarCampo(input);
            input.setAttribute('readonly', 'readonly');
        });

        input.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Impede o comportamento padrão de pressionar Enter
                input.blur();
                input.setAttribute('readonly', 'readonly');
                alert('Já atualizou! Não precisa recarregar a página :D');
            }
        });
    });

    // Manipula os selects editáveis
    editableSelects.forEach(function (select) {
        select.addEventListener('change', function () {
            salvarCampo(select);
            select.blur();
        });

        select.addEventListener('blur', function () {
            salvarCampo(select);
        });
    });

    // Captura clique fora dos inputs e selects
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.editable') && !e.target.closest('.editable-select')) {
            editableInputs.forEach(input => input.setAttribute('readonly', 'readonly'));
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const uploadForm = document.querySelector('form[enctype="multipart/form-data"]');
    const fileInput = document.getElementById('emu_files');
    // Elemento do preloader (certifique-se de que ele existe no HTML)
    const preloader = document.getElementById('preloader');
    // Elemento onde será exibido o progresso do upload
    const progressDiv = document.getElementById('upload-progress');

    if (uploadForm && fileInput) {
        uploadForm.addEventListener('submit', async function (e) {
            e.preventDefault(); // Impede o envio tradicional do formulário

            if (preloader) {
                preloader.style.display = 'block';
            }
            if (progressDiv) {
                progressDiv.style.display = 'block';
            }

            const files = fileInput.files;
            const totalFiles = files.length;

            // Itera sobre cada arquivo (post) e envia individualmente
            for (let i = 0; i < totalFiles; i++) {
                if (progressDiv) {
                    progressDiv.innerText = `Enviando ${i + 1} post de ${totalFiles}`;
                }

                let formData = new FormData();
                formData.append('emu_files[]', files[i]);
                formData.append('action', 'emu_add_files_via_ajax');
                formData.append('security', emu_add_files_nonce);

                try {
                    const response = await fetch(ajaxurl, {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    if (!data.success) {
                        alert(`Erro ao enviar o post ${i + 1}: ${data.data || 'Erro desconhecido.'}`);
                    }
                } catch (error) {
                    
                    
                }
            }

            if (preloader) {
                preloader.style.display = 'none';
            }
            if (progressDiv) {
                progressDiv.style.display = 'none';
            }
            
            location.reload();
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const selectAllBtn = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.delete-post-ids');
    const deleteBtn = document.querySelector('.delete-selected');

    if (selectAllBtn && checkboxes.length) {
        selectAllBtn.addEventListener('click', function () {
            let allChecked = [...checkboxes].every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            deleteBtn.style.display = checkboxes.length > 0 ? 'inline-block' : 'none';
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                let anyChecked = [...checkboxes].some(cb => cb.checked);
                deleteBtn.style.display = anyChecked ? 'inline-block' : 'none';
            });
        });
    }
});
