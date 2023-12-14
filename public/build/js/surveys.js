import {
    toastAlert,
    sweetWizardAlert,
    initFlatpickr,
    maxLengthTextarea,
    makeFormPreviewRequest,
    revalidationOnInput,
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

                function attachSurveysChangeStatus(surveyId){
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
                }

                if( currentStatus && currentStatus == 'started' ){
                    Swal.fire({
                        icon: 'warning',
                        title: "Tem certeza que deseja Interromper esta Tarefa?",
                        html: 'Tarefas em andamento terão suas respectivas atividades não completadas removidas. <br><br><span class="text-warning">Não será possível reverter remoções.</span>',
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: "Sim, interromper",
                        denyButtonText: `Deixar como está`,
                        confirmButtonClass: 'btn btn-outline-warning w-xs me-2',
                        cancelButtonClass: 'btn btn-sm btn-outline-info w-xs',
                        denyButtonClass: 'btn btn-sm btn-outline-danger w-xs me-2',
                        buttonsStyling: false,
                        showCloseButton: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            attachSurveysChangeStatus(surveyId);
                        } else if (result.isDenied) {
                            return false;
                        }
                    });
                }else{
                    attachSurveysChangeStatus(surveyId);
                }
            });
        });
    }

    function recurringOnce(){
        const recurringSelect = document.getElementById('date-recurring-field');
        const startDateInput = document.getElementById('date-recurring-start');
        const endDateInput = document.getElementById('date-recurring-end');

        if(recurringSelect){
            // Event listener for the recurring select dropdown
            recurringSelect.addEventListener('change', function() {
                if (this.value === 'once') {
                    // If 'once' is selected, disable the end date and set its value to the start date
                    endDateInput.value = startDateInput.value;
                    endDateInput.disabled = true;
                } else {
                    // For other options, enable the end date input
                    endDateInput.disabled = false;
                }
            });

            // Optional: Event listener for the start date to update the end date if 'once' is selected
            startDateInput.addEventListener('input', function() {
                if (recurringSelect.value === 'once') {
                    endDateInput.value = startDateInput.value;
                }
            });
        }
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

                    recurringOnce();

                    wizardFormSteps();

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

    function wizardFormSteps(){
        var formSteps = document.querySelectorAll(".form-steps");
        if (formSteps){
            Array.from(formSteps).forEach(function (form) {

                function checkAllFormCheckInputs() {
                    // Select all elements with the .form-check-input class
                    var checkboxes = document.querySelectorAll('.form-check-input');

                    // Iterate over them and add a change event listener to each one
                    checkboxes.forEach(function(checkbox) {
                        // Add a change listener to the current checkbox
                        checkbox.addEventListener('change', function() {
                            // This function is called whenever a checkbox is checked or unchecked
                            // You can add your logic here for what happens when the state changes
                            if (this.checked) {
                                this.setAttribute('checked', '');
                                //console.log(this.id + ' is checked');
                            } else {
                                this.removeAttribute('checked');
                                //console.log(this.id + ' is unchecked');
                            }

                            if (this.classList.contains('form-check-input-companies')) {
                                var checkbox = this;
                                let companyId = checkbox.value;
                                let column = document.getElementById(`distributed-tab-company-${companyId}`);

                                column.style.display = 'none';
                                column.querySelectorAll('input').forEach(input => input.required = false);

                                if (checkbox.checked) {
                                    column.style.display = '';
                                    column.querySelectorAll('input').forEach(input => input.required = true);
                                }
                            }
                        });
                    });
                }
                checkAllFormCheckInputs();

                /*function checkRequiredFields(inputControlRequired) {
                    let filledRequiredFields = Array.from(inputControlRequired).reduce((count, elem) => {
                        return count + (elem.value.trim() !== '' ? 1 : 0);
                    }, 0);

                    return filledRequiredFields === inputControlRequired.length;
                }*/

                function navigateToTab(nextTabId) {
                    form.classList.remove('was-validated');

                    const nextTab = document.getElementById(nextTabId);

                    nextTab.removeAttribute('disabled');
                    nextTab.click();
                    nextTab.setAttribute('disabled', 'disabled');

                    //form.classList.add('was-validated');
                }

                function checkRequiredFields(inputControls, switchControls) {
                    let emptyInputCount = Array.from(inputControls).filter(input => !input.value.trim()).length;
                    let isSwitchChecked = Array.from(switchControls).filter(input => input.checked).length;

                    let switchRequirementCount = 0;

                    // If no switch is checked, count it as one requirement not met
                    if(isSwitchChecked){
                        switchRequirementCount = isSwitchChecked > 0 ? 0 : 1;
                    }

                    return emptyInputCount + switchRequirementCount;
                }

                // next tab
                if (form.querySelector(".nexttab")) {
                    const tabButtons = form.querySelectorAll('button[data-bs-toggle="pill"]');
                    Array.from(tabButtons).forEach(item => {
                        item.addEventListener('show.bs.tab', event => event.target.classList.add('done'));
                    });

                    Array.from(form.querySelectorAll(".nexttab")).forEach(nextButton => {
                        nextButton.addEventListener("click", () => {
                            form.classList.add('was-validated');

                            const activeTab = form.querySelector(".tab-pane.show");

                            const nextTab = nextButton.getAttribute('data-nexttab');

                            const inputControlRequired = activeTab.querySelectorAll(".wizard-input-control[required]");
                            //console.log('inputControlRequired', inputControlRequired.length);

                            const switchControlRequired = activeTab.querySelectorAll(".wizard-switch-control[required]");
                            //console.log('switchControlRequired', switchControlRequired.length);

                            const totalUnfilledRequired = checkRequiredFields(inputControlRequired, switchControlRequired);
                            //console.log('totalUnfilledRequired', totalUnfilledRequired);

                            if (totalUnfilledRequired > 0) {
                                toastAlert('Necessário preencher os campos obrigatórios', 'danger', 10000);
                                return;
                            }

                            // Additional logic for specific tabs
                            if (nextTab === 'steparrow-recurring-info-tab') {
                                const checkedControlCompanies = form.querySelectorAll(".tab-pane.show .form-check-input-companies:checked");
                                if(form.querySelectorAll(".tab-pane.show .form-check-input-companies").length){
                                    if (checkedControlCompanies.length === 0) {
                                        toastAlert('Necessário selecionar ao menos uma unidade', 'danger', 10000);
                                        return;
                                    }
                                }
                                navigateToTab(nextTab);
                            } else if (nextTab === 'steparrow-success-tab') {
                                const checkedControlUsers = form.querySelectorAll(".tab-pane.show .form-check-input-users:checked");

                                const selectedCompanies = form.querySelectorAll(".tab-pane .form-check-input-companies:checked");

                                if (checkedControlUsers.length < selectedCompanies.length) {
                                    toastAlert('Necessário delegar para cada Unidade as respectivas Atribuições', 'danger', 10000);
                                    return;
                                }

                                navigateToTab(nextTab);
                            } else {
                                navigateToTab(nextTab);
                            }
                        });
                    });
                }

                //Pervies tab
                if (form.querySelectorAll(".previestab")){
                    Array.from(form.querySelectorAll(".previestab")).forEach(function (prevButton) {

                        prevButton.addEventListener("click", function () {
                            var prevTab = prevButton.getAttribute('data-previous');

                            document.getElementById(prevTab).removeAttribute('disabled');

                            var totalDone = prevButton.closest("form").querySelectorAll(".custom-nav .done").length;
                            for (var i = totalDone - 1; i < totalDone; i++) {
                                (prevButton.closest("form").querySelectorAll(".custom-nav .done")[i]) ? prevButton.closest("form").querySelectorAll(".custom-nav .done")[i].classList.remove('done'): '';
                            }
                            document.getElementById(prevTab).click();

                            document.getElementById(prevTab).setAttribute('disabled', 'disabled');
                        });
                    });
                }

                // Step number click
                var tabButtons = form.querySelectorAll('button[data-bs-toggle="pill"]');
                if (tabButtons){
                    Array.from(tabButtons).forEach(function (button, i) {
                        button.setAttribute("data-position", i);
                        button.addEventListener("click", function () {
                            form.classList.remove('was-validated');

                            var getProgressBar = button.getAttribute("data-progressbar");
                            if (getProgressBar) {
                                var totalLength = document.getElementById("custom-progress-bar").querySelectorAll("li").length - 1;
                                var current = i;
                                var percent = (current / totalLength) * 100;
                                document.getElementById("custom-progress-bar").querySelector('.progress-bar').style.width = percent + "%";
                            }
                            (form.querySelectorAll(".custom-nav .done").length > 0) ?
                            Array.from(form.querySelectorAll(".custom-nav .done")).forEach(function (doneTab) {
                                doneTab.classList.remove('done');
                            }): '';
                            for (var j = 0; j <= i; j++) {
                                tabButtons[j].classList.contains('active') ? tabButtons[j].classList.remove('done') : tabButtons[j].classList.add('done');
                            }
                        });
                    });
                }
            });
        }
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
                    container.innerHTML = '<h6 class="text-muted m-0 text-uppercase fw-semibold mb-4">Atividades Recentes</h6>';

                    if(data.success && data.activities){
                        data.activities.forEach(activity => {
                            const activityElement = document.createElement('div');
                            activityElement.className = 'card border border-dashed shadow-none mb-3';
                            activityElement.innerHTML = `
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs flex-shrink-0 me-2">
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
                                            <div class="fs-11 mb-0 text-muted text-end">${activity.companyName}</div>
                                            <div class="fs-10 mb-0 text-muted d-none">${activity.createddAt}</div>
                                            <div class="fs-10 mb-0 text-muted d-none">${activity.updatedAt}</div>
                                        </div>
                                    </div>
                                    <div class="progress progress-sm mt-1 custom-progress" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="${activity.percentage}%">
                                        <div class="progress-bar bg-${activity.progressBarClass}" role="progressbar" style="width: ${activity.percentage}%" aria-valuenow="${activity.percentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            `;
                            container.appendChild(activityElement);
                        });

                        bsPopoverTooltip();
                    }else{
                        container.innerHTML = '<h6 class="text-muted m-0 text-uppercase fw-semibold mb-4">Atividades Recentes</h6><div class="text-center text-muted">'+ data.message +'</div>';
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
    initFlatpickr();
    maxLengthTextarea();
    layouRightSide();
    toggleTableRows();
   // choicesListeners(surveysTermsSearchURL, surveysStoreOrUpdateURL, choicesSelectorClass);

});
