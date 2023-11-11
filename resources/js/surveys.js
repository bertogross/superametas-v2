import {
    toastAlert,
    initFlatpickr,
    initFlatpickrRange,
    maxLengthTextarea,
    makeFormPreviewRequest,
    revalidationOnInput,
    allowUncheckRadioButtons
} from './helpers.js';

import {
    choicesListeners
} from './surveys-terms.js';


document.addEventListener('DOMContentLoaded', function() {

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
            if (!form) {
                console.error('Form not found');
                return;
            }

            const choiceContainers = form.querySelectorAll('.choices__inner');

            if (!form.checkValidity()) {
                event.stopPropagation();

                form.classList.add('was-validated');

                if(choiceContainers){
                    choiceContainers.forEach(container => {
                        let select = container.parentElement.querySelector('select');
                        if (select && !select.checkValidity()) {
                            container.classList.add('is-invalid');
                        }
                        if (select && select.checkValidity()) {
                            container.classList.add('is-valid');
                        }
                    });
                }

                toastAlert('Preencha os campos obrigatórios', 'danger', 5000);

                return;
            }else{
                form.classList.remove('was-validated');

                choiceContainers.forEach(container => {
                    container.classList.remove('is-invalid');
                    container.classList.remove('is-valid');
                });
            }

            // Prevent to submit choices input
            var searchInput = document.querySelectorAll('.choices__input--cloned');
            if (searchInput) {
                searchInput.forEach(function (choicesSearchTermsInput) {
                    choicesSearchTermsInput.disabled = true;
                });
            }

            // Validate ID
            const surveyId = form.querySelector('input[name="id"]').value;

            const formData = new FormData(form);

            /*
            var groupedData = {};
            for (var pair of formData.entries()) {
                var key = pair[0];
                var value = pair[1];

                var stepMatch = key.match(/item\[(\d+)\]/);
                var topicMatch = key.match(/\['topic_id'\]\[(\d+)\]/);

                var stepIndex = stepMatch ? stepMatch[1] : null;
                var topicIndex = topicMatch ? topicMatch[1] : null;

                if (stepIndex !== null) {
                    if (!groupedData[stepIndex]) {
                        groupedData[stepIndex] = {
                            stepData: {},
                            topicData: {}
                        };
                    }

                    if (topicIndex !== null) {
                        if (!groupedData[stepIndex].topicData[topicIndex]) {
                            groupedData[stepIndex].topicData[topicIndex] = {};
                        }
                        groupedData[stepIndex].topicData[topicIndex][key] = value;
                    } else {
                        groupedData[stepIndex].stepData[key] = value;
                    }
                } else {
                    groupedData[key] = value;
                }
            }

            if (Object.keys(groupedData).length === 0) {
                toastAlert('Necessário adicionar Blocos', 'danger', 10000);
                return;
            }
            */
            var object = {};
            formData.forEach((value, key) => {
              if (!object[key]) {
                object[key] = value;
                return;
              }
              if (!Array.isArray(object[key])) {
                object[key] = [object[key]];
              }
              object[key].push(value);
            });
            console.log(JSON.stringify(object, null, 2));
            //return;

            // Transform data
            var data = object;
            const transformedData = [];
            for (let i = 0; ; i++) {
                const stepNameKey = `['stepData']['step_name']`;
                if (!data[stepNameKey]) break;

                const stepData = {
                    step_name: data[stepNameKey],
                    original_position: parseInt(data[`['stepData']['original_position']`], 10),
                    new_position: parseInt(data[`['stepData']['new_position']`], 10)
                };

                const topicData = [];
                for (let j = 0; ; j++) {
                    const topicIdKey = `['topicsData'][${j}]['topic_id']`;
                    if (!data[topicIdKey]) break;

                    const topic = {
                        topic_id: data[topicIdKey],
                        original_position: parseInt(data[`['topicsData'][${j}]['original_position']`], 10),
                        new_position: parseInt(data[`['topicsData'][${j}]['new_position']`], 10)
                    };
                    topicData.push(topic);
                }

                transformedData.push({ stepData, topicData });
            }
            //console.log(JSON.stringify(transformedData, null, 2));
            //return;

            formData.append('jsondata', JSON.stringify(transformedData, null, 2));

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
                    toastAlert(data.message, 'success', 10000);

                    btnStoreOrUpdate.textContent = 'Atualizar'; // Change button text
                    btnStoreOrUpdate.classList.remove('btn-theme'); // Remove old class
                    btnStoreOrUpdate.classList.add('btn-outline-theme'); // Add new class

                    //localStorage.setItem('statusAlert', 'Status atualizado para ' + translatedStatus);

                    document.querySelector('input[name="id"]').value = data.id;

                    // Make the preview request
                    makeFormPreviewRequest(data.id, surveysShowURL);
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
            makeFormPreviewRequest(idValue, surveysShowURL, 'load-preview-' + selectId, 'edition=true');
        });
    });
    */


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

    // Make the preview request
    var idInput = document.querySelector('input[name="id"]');
    var idValue = idInput ? idInput.value : null;
    makeFormPreviewRequest(idValue, surveysShowURL);


    // Call the function when the DOM is fully loaded
    initFlatpickrRange();
    initFlatpickr();
    revalidationOnInput();
    maxLengthTextarea();
    allowUncheckRadioButtons();
    choicesListeners(surveysTermsSearchURL, surveysStoreOrUpdateURL, choicesSelectorClass);

});
