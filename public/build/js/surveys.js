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

            // Transform data
            var data = {};
            formData.forEach((value, key) => {
                // Handle array formation for keys with multiple values
                if (data.hasOwnProperty(key)) {
                    if (!Array.isArray(data[key])) {
                        data[key] = [data[key]];
                    }
                    data[key].push(value);
                } else {
                    data[key] = value;
                }
            });
            //console.log(data);
            //return;

            const transformedData = [];
            for (let i = 0; data.hasOwnProperty(`steps[${i}]['stepData']['step_name']`); i++) {
                const stepData = {
                    step_name: data[`steps[${i}]['stepData']['step_name']`],
                    type: data[`steps[${i}]['stepData']['type']`],
                    original_position: parseInt(data[`steps[${i}]['stepData']['original_position']`], 10),
                    new_position: parseInt(data[`steps[${i}]['stepData']['new_position']`], 10)
                };

                const topics = [];
                // Assuming topic_id is an array, iterate based on its length for each step.
                const topicLength = data[`steps[${i}]['topics']['topic_id']`].length;
                for (let j = 0; j < topicLength; j++) {
                    const topicId = data[`steps[${i}]['topics']['topic_id']`][j];
                    const originalPosition = data[`steps[${i}]['topics']['original_position']`][j];
                    const newPosition = data[`steps[${i}]['topics']['new_position']`][j];
                    const topic = {
                        topic_id: topicId,
                        original_position: parseInt(originalPosition, 10),
                        new_position: parseInt(newPosition, 10)
                    };
                    topics.push(topic);
                }

                transformedData.push({ stepData, topics });
            }
            //console.log(transformedData);
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
