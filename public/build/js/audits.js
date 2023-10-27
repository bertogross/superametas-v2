import {
    toastAlert,
    multipleModal,
    initFlatpickr,
    initFlatpickrRange,
    goTo,
    bsPopoverTooltip,
    formatNumber,
    maxLengthTextarea
} from './helpers.js';



window.addEventListener('load', function () {


    initFlatpickrRange();
    initFlatpickr();


    /**
     * Load the content for the Goal Sales Edit Modal
     */
    async function loadAuditsEditModal(auditId = null) {
        try {
            let url = `/audits/form/${auditId}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const content = await response.text();
            document.getElementById('modalContainer').insertAdjacentHTML('beforeend', content);

            const modalElement = document.getElementById('auditsEditModal');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false,
            });
            modal.show();

            multipleModal();

            initFlatpickr();

            attachCustomFieldListeners();

            maxLengthTextarea();

            preventCustomFieldsWhiteSpace();

            if(auditId){
                document.querySelector("#auditsEditModal .modal-title").innerHTML = `<u>Editar</u> Auditoria ID: <span class="text-theme">${auditId}</span>`;

                document.querySelector("#auditsEditModal #btn-audits-update").innerHTML = 'Atualizar';
            }else{
                document.querySelector("#auditsEditModal .modal-title").innerHTML = `<u>Compor</u> Auditoria</span>`;
                document.querySelector("#auditsEditModal #btn-audits-update").innerHTML = 'Salvar';
            }

            attachAuditsUpdateListeners(auditId);

        } catch (error) {
            console.error('Error fetching modal content:', error);
            toastAlert('Não foi possível carregar o conteúdo', 'error', 10000);
        }
    }

    // Event listeners for 'Update' button
    function attachAuditsUpdateListeners(auditId) {

        // store/update auditsForm
        document.getElementById('btn-audits-update').addEventListener('click', async function(event) {
            event.preventDefault();

            const form = document.getElementById('auditsForm');

            if (!form.checkValidity()) {
                event.stopPropagation();
                form.classList.add('was-validated');

                toastAlert('Preencha os campos obrigatórios', 'danger', 5000);

                return;
            }

            let formData = new FormData(form);

            try {
                let url = auditId ? `/audits/post/${auditId}` : `/audits/post/`;

                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Close current modal
                    document.getElementById('btn-audits-update').closest('.modal').querySelector('.btn-close').click();

                    let loading = '<span class="spinner-grow spinner-grow-sm text-theme ms-2" role="status"><span class="visually-hidden">Loading...</span></span>';

                    toastAlert(data.message + loading, 'success', 10000);

                    setTimeout(function () {
                        location.reload(true);
                    }, 5000);
                } else {
                    toastAlert(data.message, 'danger', 60000);
                }
            } catch (error) {
                toastAlert('Error: ' + error, 'danger', 60000);
                console.error('Error:', error);
            }
        });
    }

    // Event listeners for each 'Edit Goal Sales' button
    function attachModalEventListeners(){
        var editButtons = document.querySelectorAll('.btn-audit-edit');
        if(editButtons){
            editButtons.forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();

                    const auditId = this.getAttribute("data-audit-id");

                    loadAuditsEditModal(auditId);
                });

            });
        }
    }
    attachModalEventListeners();


    function attachCustomFieldListeners() {
        if(document.getElementById('add-custom-field')){
            document.getElementById('add-custom-field').addEventListener('click', function() {
                const container = document.getElementById('custom-fields-container');
                const index = container.children.length;
                const field = document.createElement('div');
                field.innerHTML = `
                    <div class="custom-field row mb-2">
                        <div class="col">
                            <select name="custom_fields[${index}][type]" class="form-select" required>
                                <option value="">Tipo</option>
                                <option value="text">Texto</option>
                                <option value="date">Data</option>
                                <option value="textarea">Área de texto</option>
                                <option value="file">Carregar arquivo</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="radio">Radio Button</option>
                                <option value="select">Selecionador</option>
                            </select>
                        </div>
                        <div class="col">
                            <input type="text" name="custom_fields[${index}][name]" placeholder="nome_do_campo" class="form-control" maxlength="30" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Digite somente letras minúsculas e sem espaços" required>
                        </div>
                        <div class="col">
                            <input type="text" name="custom_fields[${index}][label]" value="{${index}" placeholder="Título do Campo" class="form-control" maxlength="50" required>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-ghost-danger btn-remove-custom-field"><i class="ri-delete-bin-3-line"></i></button>
                        </div>
                    </div>
                `;
                container.appendChild(field);

                // Reattach the event listeners to include the new remove button
                removeCustomField();

                bsPopoverTooltip();

                preventCustomFieldsWhiteSpace();

            });
        }
    }

    function removeCustomField() {
        // Get all elements with the class '.btn-remove-custom-field'
        const removeButtons = document.querySelectorAll('.btn-remove-custom-field');

        // Add a click event listener to each button
        removeButtons.forEach(function(button) {
            // First, remove any existing event listeners to prevent duplicates
            button.removeEventListener('click', removeFieldHandler);

            // Then, add the new event listener
            button.addEventListener('click', removeFieldHandler);
        });
    }

    function removeFieldHandler() {
        // Get the nearest ancestor of the clicked button with the class '.custom-field'
        const customFieldElement = this.closest('.custom-field');

        // Remove the custom field element from the DOM
        if (customFieldElement) {
            customFieldElement.remove();
        }
    }


    // Prevent all characters except alpha and underscore for the custom input field name... and only lowercase
    function preventCustomFieldsWhiteSpace(){
        document.addEventListener('input', function (event) {
            if (event.target.name && event.target.name.match(/^custom_fields\[\d+\]\[name\]$/)) {
                event.target.value = event.target.value.replace(/[^a-z_]/g, '');
            }
        });
    }
    preventCustomFieldsWhiteSpace();




});
