import {
    toastAlert,
    lightbox,
    debounce,
    updateProgressBar,
    updateLabelClasses
} from './helpers.js';

document.addEventListener('DOMContentLoaded', function() {

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
                    toastAlert('Redirecionando ao formulário...', 'success');

                    setTimeout(function () {
                        window.location.href = formSurveyorAssignmentURL + '/' +assignmentId;
                    }, 1000);
                 }
            });
        });
    }


    // Event listeners for each 'btn-response-update' to update/store form data to the 'survey_responses' table
    const responseSurveyorUpdateButtons = document.querySelectorAll('.btn-response-update');
    if(responseSurveyorUpdateButtons){
        responseSurveyorUpdateButtons.forEach(button => {
            button.addEventListener('click', event => {
                event.preventDefault();

                const container = document.getElementById('assignment-container');
                if (!container) {
                    console.error('Container not found');
                    return;
                }

                const responsesData = button.closest('.responses-data-container');
                if (!responsesData) {
                    console.error('Responses data container not found');
                    return;
                }

                const textArea = responsesData.querySelector('textarea');
                const btnPhoto = responsesData.querySelector('.btn-add-photo');

                //const countTopics = document.querySelectorAll('.btn-response-update').length;
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
                const attachmentInputs = responsesData.querySelectorAll('input[name="attachment_id[]"]');
                const attachmentIds = Array.from(attachmentInputs).map(input => input.value);

                if (attachmentIds.length === 0) {
                    // Select all radio buttons with the name 'compliance_survey'
                    const complianceSurveyRadios = responsesData.querySelectorAll('input[name="compliance_survey"]');

                    // Uncheck each radio button
                    complianceSurveyRadios.forEach(radio => {
                        radio.checked = false;
                    });

                    btnPhoto.classList.add('blink', 'bg-warning');
                    setTimeout(() => {
                        btnPhoto.classList.remove('blink', 'bg-warning');
                    }, 5000);

                    toastAlert('Primeiro envie uma foto', 'warning', 7000);

                    return;
                }

                // Select the radio buttons
                const radios = responsesData.querySelectorAll('input[type="radio"][name="compliance_survey"]');
                radios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        // When a radio button changes, update the label classes
                        updateLabelClasses(radios);
                    });
                });

                var pendingIcon = responsesData.querySelector('.ri-time-line');
                var completedIcon = responsesData.querySelector('.ri-check-double-fill');

                const formData = {
                    assignment_id: assignmentId,
                    company_id: companyId,
                    survey_id: surveyId,
                    step_id: stepId,
                    topic_id: topicId,
                    compliance_survey: compliance,
                    comment_survey: comment,
                    attachment_ids: attachmentIds
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
                        //toastAlert(data.message, 'success', 5000);

                        const responseId = data.id;
                        //const countFinishedTopics = parseInt(data.count || 0);
                        //console.log('countFinishedTopics', countFinishedTopics);

                        const countResponses = parseInt(data.countResponses || 0);
                        const countTopics = parseInt(data.countTopics || 0);
                        updateProgressBar(countResponses, countTopics, 'survey-progress-bar');

                        responsesData.querySelector('input[name="response_id"]').value = responseId;

                        if (responseId) {
                            // If responseId is set, show the completed icon and hide the pending icon
                            if (pendingIcon) pendingIcon.classList.add('d-none');
                            if (completedIcon) completedIcon.classList.remove('d-none');

                            button.querySelector('i').classList.add('ri-refresh-line');
                            button.querySelector('i').classList.remove('ri-save-3-line');
                            button.setAttribute('title', 'Atualizar');
                            button.setAttribute('data-bs-original-title', 'Atualizar');
                        } else {
                            // If responseId is not set, show the pending icon and hide the completed icon
                            if (pendingIcon) pendingIcon.classList.remove('d-none');
                            if (completedIcon) completedIcon.classList.add('d-none');
                        }

                        /*if( countFinishedTopics >= countTopics ){
                            // enable button to finish
                            document.querySelector('#btn-response-finalize').classList.remove('d-none');
                        }*/
                    } else {
                        //console.log('Erro:', data.message);

                        button.querySelector('i').classList.remove('ri-refresh-line');
                        button.querySelector('i').classList.add('ri-save-3-line');

                        if(data.action == 'changeToPending'){
                            if (pendingIcon) pendingIcon.classList.remove('d-none');
                            if (completedIcon) completedIcon.classList.add('d-none');

                            document.querySelector('#btn-response-finalize').classList.add('d-none');
                        }

                        if(data.action2 == 'showTextarea'){
                            textArea.style.display = "block";

                            textArea.focus();

                            textArea.classList.add('blink', 'bg-warning-subtle');
                            setTimeout(() => {
                                textArea.classList.remove('blink', 'bg-warning-subtle');
                            }, 3000);
                        }else if(data.action2 == 'blinkPhotoButton'){
                            btnPhoto.classList.add('blink', 'bg-warning');
                            setTimeout(() => {
                                btnPhoto.classList.remove('blink', 'bg-warning');
                            }, 3000);
                        }

                        toastAlert(data.message, 'danger', 7000);
                    }

                    if(data.showFinalizeButton){
                        setTimeout(() => {
                            document.querySelector('#btn-response-finalize').classList.remove('d-none');

                            //document.querySelector('#btn-response-finalize').click();

                            if(document.querySelector('#survey-progress-bar')){
                                document.querySelector('#survey-progress-bar').remove();
                            }
                        }, 1000);

                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    }


    // Attach event listeners to compliance survey radio buttons
    const complianceSurveyRadios = document.querySelectorAll('input[name="compliance_survey"]');
    if(complianceSurveyRadios){
        complianceSurveyRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const container = this.closest('.responses-data-container');

                const updateButton = container.querySelector('.btn-response-update');
                if (updateButton) {
                    updateButton.click();
                }
            }, 500);
        });
    }

    // Attach event listener to the comment textarea
    const commentSurveyorTextareas = document.querySelectorAll('textarea[name="comment_survey"]');
    if(commentSurveyorTextareas){
        commentSurveyorTextareas.forEach(textarea => {
            textarea.addEventListener('input', debounce(function() {
                const container = this.closest('.responses-data-container');
                const updateButton = container.querySelector('.btn-response-update');
                if (updateButton) {
                    updateButton.click();
                }
            }, 1000)); // 1000 milliseconds = 1 second
        });
    }


    // When Surveyor finish your taks, transfer to Auditor make revision
    const responseSurveyorAssignmentFinalizedButton = document.getElementById('btn-response-finalize');
    if(responseSurveyorAssignmentFinalizedButton){
        responseSurveyorAssignmentFinalizedButton.addEventListener('click', async function(event) {
            event.preventDefault();

            const container = document.getElementById('assignment-container');
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
                confirmButtonText: 'Finalizar',
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


    lightbox();

});
