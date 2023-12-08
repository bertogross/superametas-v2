import {
    toastAlert,
    sweetWizardAlert,
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


document.addEventListener('DOMContentLoaded', function() {

    const btnCreate = document.getElementById('btn-surveys-create');
    if(btnCreate){
        btnCreate.addEventListener('click', async function(event) {
            event.preventDefault;

            loadFormModal();
        });
    }

    // Event listeners for each 'Edit' buttonS
    const editButtons = document.querySelectorAll('.btn-surveys-edit');
    if(editButtons){
        editButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                var surveyId = this.getAttribute("data-survey-id");

                loadFormModal(surveyId);
            });
        });
    }

    const changeStatusButtons = document.querySelectorAll('.btn-surveys-change-status');
    if(changeStatusButtons){
        changeStatusButtons.forEach(function(button) {
            button.addEventListener('click', async function(event) {
                event.preventDefault;

                var currentStatus = this.getAttribute("data-current-status");

                var surveyId = this.getAttribute("data-survey-id");
                surveyId = parseInt(surveyId);

                if( currentStatus && currentStatus == 'started' ){
                    var isConfirmed = confirm('Certeza que deseja Interromper esta Tarefa?');
                    if (!isConfirmed) {
                        event.stopPropagation();

                        return;
                    }
                }

                fetch(surveysChangeStatusURL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Laravel CSRF token
                    },
                    body: JSON.stringify({ id: surveyId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastAlert(data.message, 'success');

                        location.reload(true);
                    } else {
                        // Handle error
                        console.error('Error start/stop survey:', data.message);

                        toastAlert(data.message, 'danger', 5000);
                    }
                })
                .catch(error => console.error('Error:', error));

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

                        sweetWizardAlert(data.message, surveysIndexURL);

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

    if( document.getElementById('load-surveys-activities') && getRecentActivitiesURL ){
        function getRecentActivities() {
            fetch(getRecentActivitiesURL)
                .then(response => response.json())
                .then(data => {
                    //console.log(JSON.stringify(activities, null, 2));

                    const container = document.getElementById('load-surveys-activities');
                    container.innerHTML = '';

                    if(data.success && data.activities){
                        data.activities.forEach(activity => {
                            const activityElement = document.createElement('div');
                            activityElement.className = 'card';
                            activityElement.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs flex-shrink-0 me-1">
                                        <a href="${activity.designatedUserProfileURL}" class="text-body d-block" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar todas as Tarefas delegadas a ${activity.designatedUserName}">
                                            <img src="${activity.designatedUserAvatar}" alt="avatar" class="img-fluid rounded-circle">
                                        </a>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fs-11 mb-0 fw-bold">
                                            ${activity.designatedUserName}
                                        </div>
                                        <div class="fs-11 mb-0 text-muted">${activity.templateName}</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        ${activity.label}
                                        <div class="fs-11 mb-0 text-muted">${activity.companyName}</div>
                                        <div class="fs-10 mb-0 text-muted d-none">${activity.createddAt}</div>
                                        <div class="fs-10 mb-0 text-muted d-none">${activity.updatedAt}</div>
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-1 custom-progress" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="${activity.percentage}%">
                                    <div class="progress-bar bg-${activity.progressBarClass}" role="progressbar" style="width: ${activity.percentage}%" aria-valuenow="${activity.percentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            `;
                            container.appendChild(activityElement);
                        });

                        bsPopoverTooltip();
                    }else{
                        container.innerHTML = '<div class="text-center text-muted">'+ data.message +'</div>';
                    }
                })
                .catch(error => console.error('Error:', error)
            );
        }
        getRecentActivities();
        setInterval(function () {
            getRecentActivities();
        }, 60000);// 60000 = 1 minute
    }


    // Make the preview request
    var idInput = document.querySelector('input[name="id"]');
    if(idInput){
        var idValue = idInput ? idInput.value : null;
        makeFormPreviewRequest(idValue, surveysShowURL);
    }

    // Call the function when the DOM is fully loaded
    initFlatpickrRange();
    initFlatpickr();
    maxLengthTextarea();
    layouRightSide();
    toggleTableRows();
   // choicesListeners(surveysTermsSearchURL, surveysStoreOrUpdateURL, choicesSelectorClass);

});
