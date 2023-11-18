import {
    toastAlert,
    sweetAlerts,
    initFlatpickr,
    initFlatpickrRange,
    maxLengthTextarea,
    makeFormPreviewRequest,
    revalidationOnInput,
    wizardFormSteps,
    multipleModal,
    bsPopoverTooltip,
    layouRightSide,
    toggleTableRows
} from './helpers.js';

/*
import {
    choicesListeners
} from './surveys-terms.js';
*/

document.addEventListener('DOMContentLoaded', function() {

    var btnCreate = document.getElementById('btn-surveys-create');
    if(btnCreate){
        btnCreate.addEventListener('click', async function(event) {
            event.preventDefault;

            loadFormModal();
        });
    }

    // Event listeners for each 'Edit' buttonS
    var editButtons = document.querySelectorAll('.btn-surveys-edit');
    if(editButtons){
        editButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                var surveyId = this.getAttribute("data-survey-id");

                loadFormModal(surveyId);
            });
        });
    }


    function loadFormModal(Id = null) {
        var xhr = new XMLHttpRequest();

        var url = Id ? surveysEditURL + '/' + Id : surveysCreateURL;

        xhr.open('GET', url, true);
        xhr.setRequestHeader('Cache-Control', 'no-cache'); // Set the Cache-Control header to no-cache
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if(xhr.responseText){
                    document.getElementById('modalContainer').innerHTML = xhr.responseText;

                    var modalElement = document.getElementById('surveysModal');
                    var modal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    modal.show();

                    attachModalEventListeners();

                    revalidationOnInput();

                    wizardFormSteps(totalCompanies);

                    initFlatpickr();

                    bsPopoverTooltip();

                    multipleModal();

                }else{
                    toastAlert('Não foi possível carregar o conteúdo', 'danger', 10000);
                }

            } else {
                console.log("Fetching modal content:", xhr.statusText);
            }
        };
        xhr.send();
    }

    // Attach event listeners for the modal form
    function attachModalEventListeners() {

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

                // Validate ID
                const surveyId = form.querySelector('input[name="id"]').value;

                const formData = new FormData(form);
                //console.log(formData);
                //return;

                // Transform data
                var data = {};
                // Iterate over formData entries
                for (let [key, value] of formData.entries()) {
                    // Check if the key includes 'delegated_to'
                    if (key.startsWith('delegated_to')) {
                        // Extract the index - assuming the format is 'delegated_to[index]'
                        let index = key.match(/\[(\d+)\]/)[1]; // Get the number inside brackets

                        // Initialize the array if it doesn't exist
                        if (!data.delegated_to) {
                            data.delegated_to = [];
                        }

                        // Push the object with company_id as index and user_id as value
                        data.delegated_to.push({ company_id: index, user_id: value });
                    }

                    if (key.startsWith('audited_by')) {
                        // Extract the index - assuming the format is 'delegated_to[index]'
                        let index = key.match(/\[(\d+)\]/)[1]; // Get the number inside brackets

                        // Initialize the array if it doesn't exist
                        if (!data.audited_by) {
                            data.audited_by = [];
                        }

                        // Push the object with company_id as index and user_id as value
                        data.audited_by.push({ company_id: index, user_id: value });
                    }
                }
                //console.log(data);
                //console.log(JSON.stringify(data, null, 2))
                //return;

                //formData.append('distributed_data', JSON.stringify(transformedData, null, 2));
                formData.append('distributed_data', JSON.stringify(data, null, 2));

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

                        document.querySelector('input[name="id"]').value = data.id;

                        sweetAlerts(data.message, surveysIndexURL);

                        // Make the preview request
                        //makeFormPreviewRequest(data.id, surveysShowURL);
                    } else {
                        toastAlert(data.message, 'danger', 60000);
                    }
                } catch (error) {
                    toastAlert('Error: ' + error, 'danger', 60000);
                    console.error('Error:', error);
                }
            });
        }

    }




    // Make the preview request
    var idInput = document.querySelector('input[name="id"]');
    var idValue = idInput ? idInput.value : null;
    makeFormPreviewRequest(idValue, surveysShowURL);


    // Call the function when the DOM is fully loaded
    initFlatpickrRange();
    initFlatpickr();
    maxLengthTextarea();
    layouRightSide();
    toggleTableRows();
   // choicesListeners(surveysTermsSearchURL, surveysStoreOrUpdateURL, choicesSelectorClass);

});
