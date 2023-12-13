import {
    toastAlert,
    sweetWizardAlert,
    maxLengthTextarea,
    makeFormPreviewRequest,
    revalidationOnInput,
    allowUncheckRadioButtons,
    showButtonWhenInputChange
} from './helpers.js';

import {
    addTerms
} from './surveys-terms.js';

document.addEventListener('DOMContentLoaded', function() {

    // store/update surveyTemplateForm
    document.addEventListener('click', async function(event) {

        // The event.target contains the clicked element
        const clickedElement = event.target;
        //console.log('Clicked element:', clickedElement);

        if(clickedElement){

            const clickedElementId = clickedElement.id;
            console.log(clickedElementId);

            // store/update surveyTemplateForm
            if ( clickedElementId === 'btn-survey-template-store-or-update' || clickedElementId === 'btn-survey-template-autosave' ) {
                event.preventDefault();

                const form = document.getElementById('surveyTemplateForm');
                if (!form) {
                    console.error('Form not found');
                    return;
                }

                const checkAutosave = document.getElementById(''+clickedElementId+'').getAttribute('data-autosave');

                //const choiceContainers = form.querySelectorAll('.choices__inner');

                if (!form.checkValidity()) {
                    if(checkAutosave === 'no'){
                        event.stopPropagation();

                        form.classList.add('was-validated');

                        /*if(choiceContainers){
                            choiceContainers.forEach(container => {
                                let select = container.parentElement.querySelector('select');
                                if (select && !select.checkValidity()) {
                                    container.classList.add('is-invalid');
                                }
                                if (select && select.checkValidity()) {
                                    container.classList.add('is-valid');
                                }
                            });
                        }*/

                        toastAlert('Preencha os campos obrigatórios', 'danger', 5000);

                        return;
                    }
                }else{
                    form.classList.remove('was-validated');

                    /*choiceContainers.forEach(container => {
                        container.classList.remove('is-invalid');
                        container.classList.remove('is-valid');
                    });*/
                }

                // Prevent to submit choices input
                /*var searchInput = document.querySelectorAll('.choices__input--cloned');
                if (searchInput) {
                    searchInput.forEach(function (choicesSearchTermsInput) {
                        choicesSearchTermsInput.disabled = true;
                    });
                }*/

                // Validate ID
                const surveyTemplateId = form.querySelector('input[name="id"]').value;

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
                for (let i = 0; data.hasOwnProperty(`steps[${i}]['stepData']['term_name']`); i++) {
                    const stepData = {
                        term_name: data[`steps[${i}]['stepData']['term_name']`],
                        term_id: data[`steps[${i}]['stepData']['term_id']`],
                        type: data[`steps[${i}]['stepData']['type']`],
                        original_position: parseInt(data[`steps[${i}]['stepData']['original_position']`], 10),
                        new_position: parseInt(data[`steps[${i}]['stepData']['new_position']`], 10)
                    };

                    const topics = [];
                    const questions = data[`steps[${i}]['topics']['question']`];
                    const topicLength = Array.isArray(questions) ? questions.length : (questions ? 1 : 0);
                    if(topicLength){
                        for (let j = 0; j < topicLength; j++) {
                            //const question = data[`steps[${i}]['topics']['question']`][j];
                            const theQuestion = Array.isArray(questions) ? questions[j] : questions;
                            const originalPosition = data[`steps[${i}]['topics']['original_position']`][j];
                            const newPosition = data[`steps[${i}]['topics']['new_position']`][j];
                            if(theQuestion){
                                const topic = {
                                    question: theQuestion,
                                    new_position: parseInt(newPosition, 10),
                                    original_position: parseInt(originalPosition, 10)
                                };
                                topics.push(topic);
                            }
                        }
                    }

                    transformedData.push({ stepData, topics });
                }
                //console.log(transformedData);
                //console.log(JSON.stringify(transformedData, null, 2));
                //return;

                formData.append('template_data', JSON.stringify(transformedData, null, 2));

                try {
                    let url = surveyTemplateId ? surveysTemplateStoreOrUpdateURL + `/${surveyTemplateId}` : surveysTemplateStoreOrUpdateURL;

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
                        // Add value to input id
                        document.querySelector('input[name="id"]').value = data.id;

                        // Make the preview request
                        makeFormPreviewRequest(data.id, surveysTemplatePreviewURL);

                        if(checkAutosave === 'no'){
                            toastAlert(data.message, 'success');

                            sweetWizardAlert(data.message, surveysIndexURL);
                        }
                    } else {
                        toastAlert(data.message, 'danger', 10000);
                    }
                } catch (error) {
                    toastAlert('Error: ' + error, 'danger', 60000);
                    console.error('Error:', error);
                }
            }
        }
    });



    // Make the preview request after page load
    var idInput = document.querySelector('input[name="id"]');
    var idValue = idInput ? idInput.value : null;
    makeFormPreviewRequest(idValue, surveysTemplatePreviewURL);


    // Call the function when the DOM is fully loaded
    revalidationOnInput();
    maxLengthTextarea();
    allowUncheckRadioButtons();
    showButtonWhenInputChange();
    addTerms();
   // choicesListeners(surveysTermsSearchURL, surveysTemplateStoreOrUpdateURL, choicesSelectorClass);

});
