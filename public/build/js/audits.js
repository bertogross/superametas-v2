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



window.addEventListener('load', function() {
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

            maxLengthTextarea();

            if(auditId){
                document.querySelector("#auditsEditModal .modal-title").innerHTML = `<u>Editar</u> Vistoria ID: <span class="text-theme">${auditId}</span>`;

                document.querySelector("#auditsEditModal #btn-audits-update").innerHTML = 'Atualizar';
            }else{
                document.querySelector("#auditsEditModal .modal-title").innerHTML = `<u>Adicionar</u> Vistoria</span>`;
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
                let url = auditId ? `/audits/store/${auditId}` : `/audits/store/`;

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

                    let loading = '<span class="spinner-grow spinner-grow-sm text-theme ms-3" role="status"><span class="visually-hidden">Loading...</span></span>';

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

});
