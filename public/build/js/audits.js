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
    async function loadsurveysEditForm(surveyId = null) {
        try {
            let url = surveysEditURL + `/${surveyId}`;

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

            const modalElement = document.getElementById('surveysEditForm');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false,
            });
            modal.show();

            multipleModal();

            initFlatpickr();

            maxLengthTextarea();

            if(surveyId){
                document.querySelector("#surveysEditForm .modal-title").innerHTML = `<u>Editar</u> Vistoria ID: <span class="text-theme">${surveyId}</span>`;

                document.querySelector("#surveysEditForm #btn-surveys-store-or-update").innerHTML = 'Atualizar';
            }else{
                document.querySelector("#surveysEditForm .modal-title").innerHTML = `<u>Adicionar</u> Vistoria</span>`;
                document.querySelector("#surveysEditForm #btn-surveys-store-or-update").innerHTML = 'Salvar';
            }

            attachSurveyscreateOrUpdateListeners(surveyId);

        } catch (error) {
            console.error('Error fetching modal content:', error);
            toastAlert('Não foi possível carregar o conteúdo', 'error', 10000);
        }
    }

    // Event listeners for 'Update' button
    function attachSurveyscreateOrUpdateListeners(surveyId) {

        // store/update surveysForm
        document.getElementById('btn-surveys-store-or-update').addEventListener('click', async function(event) {
            event.preventDefault();

            const form = document.getElementById('surveysForm');

            if (!form.checkValidity()) {
                event.stopPropagation();
                form.classList.add('was-validated');

                toastAlert('Preencha os campos obrigatórios', 'danger', 5000);

                return;
            }

            let formData = new FormData(form);

            try {
                let url = surveyId ? surveysCreateOrUpdateURL + `/${surveyId}` : surveysCreateOrUpdateURL;

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
                    document.getElementById('btn-surveys-store-or-update').closest('.modal').querySelector('.btn-close').click();

                    toastAlert(data.message, 'success', 10000);

                    setTimeout(function () {
                        location.reload(true);
                    }, 500);
                } else {
                    toastAlert(data.message, 'danger', 60000);
                }
            } catch (error) {
                toastAlert('Error: ' + error, 'danger', 60000);
                console.error('Error:', error);
            }
        });
    }

    /*
    // Event listeners for each 'Edit Goal Sales' button
    function attachFormEventListeners(){
        var editButtons = document.querySelectorAll('.btn-survey-edit');
        if(editButtons){
            editButtons.forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();

                    const surveyId = this.getAttribute("data-survey-id");

                    loadsurveysEditForm(surveyId);
                });

            });
        }
    }
    attachFormEventListeners();
    */

});
