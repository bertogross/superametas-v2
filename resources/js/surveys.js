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

                var purpose = this.getAttribute("data-purpose");

                var surveyId = this.getAttribute("data-survey-id");
                surveyId = parseInt(surveyId);

                if( purpose && purpose == 'stop' ){
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

    // Event listeners for each 'btn-assignment-surveyor-action' to change task status from current to the next
    // This Button are in resources\views\surveys\layouts\profile-surveyors-box.blade.php
    const assignmentActionSurveyorButtons = document.querySelectorAll('.btn-assignment-surveyor-action');
    if(assignmentActionSurveyorButtons){
        assignmentActionSurveyorButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                var surveyId = this.getAttribute("data-survey-id");
                surveyId = parseInt(surveyId);

                var assignmentId = this.getAttribute("data-assignment-id");
                assignmentId = parseInt(assignmentId);

                var currentStatus = this.getAttribute("data-current-status"); // new  |  pending  |  in_progress  |  auditing

                var url = changeAssignmentSurveyorStatusURL

                if(currentStatus == 'new'){
                    // Use only to change status to pending
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Laravel CSRF token
                        },
                        body: JSON.stringify({ assignment_id: assignmentId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            /*
                            toastAlert(data.message, 'success');

                            setTimeout(function () {
                                location.reload(true);
                            }, 1000);
                            */
                            toastAlert('Redirecionando ao formulário...', 'success');

                            setTimeout(function () {
                                window.location.href = formSurveyorAssignmentURL + '/' +assignmentId;
                            }, 1000);
                        } else {
                            // Handle error
                            console.error('Error:', data.message);

                            toastAlert(data.message, 'danger', 5000);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                 }else{
                    toastAlert('Redirecionando ao formulário...', 'warning');

                    setTimeout(function () {
                        window.location.href = formSurveyorAssignmentURL + '/' +assignmentId;
                    }, 1000);
                 }
            });
        });
    }


    // Event listeners for each 'btn-assignment-auditor-action' to change task status from current to the next
    // This Button are in resources\views\surveys\layouts\profile-surveyors-box.blade.php
    const assignmentActionAuditorButtons = document.querySelectorAll('.btn-assignment-auditor-action');
    if(assignmentActionAuditorButtons){
        assignmentActionAuditorButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                var surveyId = this.getAttribute("data-survey-id");
                surveyId = parseInt(surveyId);

                var assignmentId = this.getAttribute("data-assignment-id");
                assignmentId = parseInt(assignmentId);

                var currentStatus = this.getAttribute("data-current-status"); // new  |  pending  |  in_progress  |  auditing

                var url = changeAssignmentAuditorStatusURL

                if(currentStatus == 'new'){
                    // Use only to change status to pending
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Laravel CSRF token
                        },
                        body: JSON.stringify({ assignment_id: assignmentId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastAlert(data.message, 'success');

                            setTimeout(function () {
                                location.reload(true);
                            }, 1000);
                        } else {
                            // Handle error
                            console.error('Error:', data.message);

                            toastAlert(data.message, 'danger', 5000);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                 }else{
                    toastAlert('Redirecionando ao formulário...', 'warning');

                    setTimeout(function () {
                        window.location.href = formAuditorAssignmentURL + '/' +assignmentId;
                    }, 1000);
                 }
            });
        });
    }

    // Event listeners for each 'btn-response-survey-update' to update/store form data to the 'survey_responses' table
    const responseSurveyUpdateButtons = document.querySelectorAll('.btn-response-survey-update');
    if(responseSurveyUpdateButtons){
        responseSurveyUpdateButtons.forEach(button => {
            button.addEventListener('click', event => {
                event.preventDefault();

                const container = document.getElementById('survey-assignment-container');
                if (!container) {
                    console.error('Container not found');
                    return;
                }

                const responsesData = button.closest('.responses-data-container');
                if (!responsesData) {
                    console.error('Responses data container not found');
                    return;
                }

                const countTopics = document.querySelectorAll('.btn-response-survey-update').length;
                //console.log('countTopics', countTopics);

                const surveyId = parseInt(container.querySelector('input[name="survey_id"]')?.value || 0);
                const companyId = parseInt(container.querySelector('input[name="company_id"]')?.value || 0);
                const assignmentId = parseInt(button.getAttribute('data-assignment-id'));
                const stepId = parseInt(button.getAttribute('data-step-id'));
                const topicId = parseInt(button.getAttribute('data-topic-id'));

                var responseId = responsesData.querySelector('input[name="response_id"]').value;
                responseId = responseId ? parseInt(responseId) : null;

                const compliance = responsesData.querySelector('input[name="compliance_survey"]:checked')?.value || '';
                const comment = responsesData.querySelector('textarea[name="comment_survey"]')?.value || '';
                const attachmentId = responsesData.querySelector('input[name="attachment_id_survey"]')?.value || '';

                const formData = {
                    assignment_id: assignmentId,
                    company_id: companyId,
                    survey_id: surveyId,
                    step_id: stepId,
                    topic_id: topicId,
                    compliance_survey: compliance,
                    comment_survey: comment,
                    attachment_id_survey: attachmentId
                };
                //console.log(JSON.stringify(formData, null, 2));
                //return;

                if(responseId){
                    var url = responsesSurveyorStoreOrUpdateURL + '/' + responseId
                }else{
                    var url = responsesSurveyorStoreOrUpdateURL
                }

                // AJAX call to store or update the 'survey_responses' table where step_id, topic_id, survey_id
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Laravel CSRF token
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastAlert(data.message, 'success');

                        const responseId = data.id;
                        const countFinishedTopics = parseInt(data.count || 0);
                        //console.log('countFinishedTopics', countFinishedTopics);

                        responsesData.querySelector('input[name="response_id"]').value = responseId;

                        var pendingIcon = responsesData.querySelector('.ri-time-line');
                        var completedIcon = responsesData.querySelector('.ri-check-double-fill');

                        if (responseId) {
                            // If responseId is set, show the completed icon and hide the pending icon
                            if (pendingIcon) pendingIcon.classList.add('d-none');
                            if (completedIcon) completedIcon.classList.remove('d-none');
                        } else {
                            // If responseId is not set, show the pending icon and hide the completed icon
                            if (pendingIcon) pendingIcon.classList.remove('d-none');
                            if (completedIcon) completedIcon.classList.add('d-none');
                        }

                        if (responseId) {
                            button.querySelector('i').classList.add('ri-refresh-line');
                            button.querySelector('i').classList.remove('ri-save-3-line');
                            button.setAttribute('title', 'Atualizar');
                            button.setAttribute('data-bs-original-title', 'Atualizar');
                        }

                        if( countTopics === countFinishedTopics ){
                            // TODO:
                            // enable button to finish
                            document.querySelector('#btn-response-surveyor-assignment-finalize').classList.remove('d-none');

                        }
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

    // When Surveyor finish your taks, transfer to Auditor make revision
    var responseSurveyorAssignmentFinalizedButton = document.getElementById('btn-response-surveyor-assignment-finalize');
    if(responseSurveyorAssignmentFinalizedButton){
        responseSurveyorAssignmentFinalizedButton.addEventListener('click', async function(event) {
            event.preventDefault();

            const container = document.getElementById('survey-assignment-container');
            if (!container) {
                console.error('Container not found');
                return;
            }
            const surveyId = parseInt(container.querySelector('input[name="survey_id"]')?.value || 0);

            const assignmentId = parseInt(this.getAttribute('data-assignment-id'));

            Swal.fire({
                title: 'A tarefa foi concluída!',
                icon: 'success',
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: 'Enviar para Auditoria',
                confirmButtonClass: 'btn btn-outline-success w-xs me-2',
                cancelButtonClass: 'btn btn-sm btn-outline-info w-xs',
                denyButtonClass: 'btn btn-danger w-xs me-2',
                buttonsStyling: false,
                denyButtonText: 'Não',
                cancelButtonText: 'Continuar Editando',
                showCloseButton: false,
                allowOutsideClick: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    //Ajax to change 'surveys' table column status to 'auditing' and if the response is success call Swal.fire to redirect
                    fetch(changeAssignmentSurveyorStatusURL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Laravel CSRF token
                        },
                        body: JSON.stringify({ assignment_id: assignmentId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastAlert(data.message, 'success');

                            var timerInterval;

                            Swal.fire({
                                title: 'Redirecionando...',
                                html: '',
                                timer: 3000,
                                timerProgressBar: true,
                                showCloseButton: false,
                                didOpen: function () {
                                    Swal.showLoading()
                                    timerInterval = setInterval(function () {
                                        var content = Swal.getHtmlContainer()
                                        if (content) {
                                            var b = content.querySelector('b')
                                            if (b) {
                                                b.textContent = Swal.getTimerLeft()
                                            }
                                        }
                                    }, 100)
                                },
                                onClose: function () {
                                    clearInterval(timerInterval)
                                }
                            }).then(function (result) {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    //console.log('I was closed by the timer')
                                    window.location.href = profileShowURL;
                                }
                            });
                        } else {
                            // Handle error
                            console.error('Survey status error:', data.message);

                            toastAlert(data.message, 'danger', 5000);
                        }
                    })
                    .catch(error => console.error('Error:', error));

                }
            })
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
