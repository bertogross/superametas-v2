import {
    toastAlert,
    multipleModal,
    initFlatpickr,
    initFlatpickrRange,
    goTo,
    bsPopoverTooltip,
    formatNumber,
    maxLengthTextarea,
    makeFormPreviewRequest
} from './helpers.js';



document.addEventListener('DOMContentLoaded', function() {
    initFlatpickrRange();
    initFlatpickr();


    /**
     * Load the content for the Goal Sales Edit Modal
     */
    /*async function loadsurveysEditForm(surveyId = null) {
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


        } catch (error) {
            console.error('Error fetching modal content:', error);
            toastAlert('Não foi possível carregar o conteúdo', 'danger', 10000);
        }
    }*/


    // store/update surveysForm
    var btnStoreOrUpdate = document.getElementById('btn-surveys-store-or-update');
    if(btnStoreOrUpdate){
        btnStoreOrUpdate.addEventListener('click', async function(event) {
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
                let url = surveyId ? surveysStoreOrUpdateURL + `/${surveyId}` : surveysStoreOrUpdateURL;

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



    var selectForms = document.querySelectorAll('.select-survey-form');
    selectForms.forEach(function(selectForm) {
        // Set the selected value on load
        var selectedValue = selectForm.dataset.selectedValue;
        if (selectedValue) {
            selectForm.value = selectedValue;
        }

        // Attach a change event listener to each select form
        selectForm.addEventListener('change', function(event) {
            event.preventDefault();

            var selectId = this.getAttribute('data-id');
            var idValue = this.value;

            // that takes the selected value and updates some preview content
            makeFormPreviewRequest(idValue, surveysComposeShowURL, 'load-preview-' + selectId, 'edition=true');
        });
    });



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
