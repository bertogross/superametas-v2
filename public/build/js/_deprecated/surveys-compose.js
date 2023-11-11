import {
    toastAlert,
    bsPopoverTooltip,
    revalidationOnInput,
    makeFormPreviewRequest,
    allowUncheckRadioButtons
} from './helpers.js';

import {
    choicesListeners
} from './surveys-terms.js';

document.addEventListener('DOMContentLoaded', function() {

    // Ajax to store or update data
    var btnStoreOrUpdate = document.getElementById('btn-surveys-compose-store-or-update');
    if (btnStoreOrUpdate) {
        btnStoreOrUpdate.addEventListener('click', function (event) {
            event.preventDefault();

            // Validate form
            var form = document.getElementById('surveysComposeForm');
            if (!form) {
                console.error('Form not found');
                return;
            }

            const choiceContainers = form.querySelectorAll('.choices__inner');

            if (!form.checkValidity()) {
                event.stopPropagation();

                form.classList.add('was-validated');

                choiceContainers.forEach(container => {
                    let select = container.parentElement.querySelector('select');
                    if (select && !select.checkValidity()) {
                        container.classList.add('is-invalid');
                    }
                    if (select && select.checkValidity()) {
                        container.classList.add('is-valid');
                    }
                });

                toastAlert('Preencha os campos obrigatórios', 'danger', 5000);

                return;
            }else{
                form.classList.remove('was-validated');

                choiceContainers.forEach(container => {
                    container.classList.remove('is-invalid');
                    container.classList.remove('is-valid');
                });
            }

            var searchInput = document.querySelectorAll('.choices__input--cloned');
            if (searchInput) {
                searchInput.forEach(function (choicesSearchTermsInput) {
                    choicesSearchTermsInput.disabled = true;
                });
            }


            // Validate ID
            var id = form.querySelector('input[name="id"]').value;

            // Collect form data
            var formData = new FormData(form);

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
                const stepNameKey = `[${i}]['stepData']['step_name']`;
                if (!data[stepNameKey]) break;

                const stepData = {
                    step_name: data[stepNameKey],
                    original_position: parseInt(data[`[${i}]['stepData']['original_position']`], 10),
                    new_position: parseInt(data[`[${i}]['stepData']['new_position']`], 10)
                };

                const topicData = [];
                for (let j = 0; ; j++) {
                    const topicIdKey = `[${i}]['topicData'][${j}]['topic_id']`;
                    if (!data[topicIdKey]) break;

                    const topic = {
                        topic_id: data[topicIdKey],
                        original_position: parseInt(data[`[${i}]['topicData'][${j}]['original_position']`], 10),
                        new_position: parseInt(data[`[${i}]['topicData'][${j}]['new_position']`], 10)
                    };
                    topicData.push(topic);
                }

                transformedData.push({ stepData, topicData });
            }
            //console.log(JSON.stringify(transformedData, null, 2));
            //return;

            formData.append('item_steps', JSON.stringify(transformedData, null, 2));

            // Send Ajax request
            var xhr = new XMLHttpRequest();

            var url = id ? surveysComposeStoreOrUpdateURL + '/' + id : surveysComposeStoreOrUpdateURL;
            xhr.open('POST', url, true);
            xhr.setRequestHeader('Cache-Control', 'no-cache, no-store, must-revalidate'); // Prevents caching
            xhr.setRequestHeader('Pragma', 'no-cache'); // For legacy HTTP 1.0 servers
            xhr.setRequestHeader('Expires', '0'); // Proxies
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        // Parse the response JSON
                        var response;
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (e) {
                            console.error('Error parsing response JSON:', e);
                            toastAlert('Error parsing response', 'danger', 10000);
                            return;
                        }

                        // Check if the data was saved successfully
                        if (response.success) {
                            var message = id ? 'Data updated successfully' : 'Data saved successfully';
                            toastAlert(message, 'success', 10000);

                            btnStoreOrUpdate.textContent = 'Atualizar'; // Change button text
                            btnStoreOrUpdate.classList.remove('btn-theme'); // Remove old class
                            btnStoreOrUpdate.classList.add('btn-outline-theme'); // Add new class

                            //localStorage.setItem('statusAlert', 'Status atualizado para ' + translatedStatus);

                            document.querySelector('input[name="id"]').value = response.id;

                            // Make the preview request
                            makeFormPreviewRequest(response.id, surveysComposeShowURL);
                        } else {
                            toastAlert(response.message, 'danger', 10000);
                        }
                    } else {
                        // Handle error
                        var errorMessage = id ? 'Erro ao tentar Atualizar este formulário' : 'Não foi possível Salvar os dados deste formulário';
                        toastAlert(errorMessage, 'danger', 10000);
                    }
                }
            };
            xhr.send(formData);
        });
    }

    // Ajax to toggle status
    var btnToggleStatus = document.getElementById('btn-surveys-compose-toggle-status');
    if (btnToggleStatus) {
        btnToggleStatus.addEventListener('click', function(event) {
            event.preventDefault();

            var statusTo = btnToggleStatus.getAttribute('data-status-to');
            var composeId = btnToggleStatus.getAttribute('data-compose-id');

            var xhr = new XMLHttpRequest();

            xhr.open('POST', surveysComposeToggleStatusURL + '/' + composeId + '/' + statusTo, true);
            xhr.setRequestHeader('Cache-Control', 'no-cache, no-store, must-revalidate'); // Prevents caching
            xhr.setRequestHeader('Pragma', 'no-cache'); // For legacy HTTP 1.0 servers
            xhr.setRequestHeader('Expires', '0'); // Proxies
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            // Add CSRF token to request headers
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {

                            toastAlert(response.message, 'success', 5000);

                            if (statusTo === 'disabled') {
                                btnToggleStatus.setAttribute('data-status-to', 'active');
                                btnToggleStatus.setAttribute('title', 'Clique para Ativar');

                                btnToggleStatus.querySelector('i').classList.remove('ri-toggle-fill');
                                btnToggleStatus.querySelector('i').classList.remove('text-theme');

                                btnToggleStatus.querySelector('i').classList.add('ri-toggle-line');
                                btnToggleStatus.querySelector('i').classList.add('text-danger');
                                btnToggleStatus.querySelector('span').textContent = 'Ativar';
                            } else {
                                btnToggleStatus.setAttribute('data-status-to', 'disabled');
                                btnToggleStatus.setAttribute('title', 'Clique para Desativar');

                                btnToggleStatus.querySelector('i').classList.add('ri-toggle-fill');
                                btnToggleStatus.querySelector('i').classList.add('text-theme');

                                btnToggleStatus.querySelector('i').classList.remove('ri-toggle-line');
                                btnToggleStatus.querySelector('i').classList.remove('text-danger');

                                btnToggleStatus.querySelector('span').textContent = 'Desativar';
                            }

                            makeFormPreviewRequest(composeId, surveysComposeShowURL);

                            document.getElementById('survey-status-badge').innerHTML = response.badge

                            //location.reload();
                        } else {
                            // Handle error
                            toastAlert('Erro ao tentar atualizar o status: ' + response.message, 'danger', 5000);
                        }
                    } else {
                        // Handle error
                        console.log('Error updating status: ' + response.message);

                        toastAlert('Erro ao tentar atualizar o status: ' + response.message, 'danger', 5000);
                    }
                }
            };
            xhr.send();
        });
    }

    // Make the preview request
    var idInput = document.querySelector('input[name="id"]');
    var idValue = idInput ? idInput.value : null;
    makeFormPreviewRequest(idValue, surveysComposeShowURL);


    // Call the function when the DOM is fully loaded
    revalidationOnInput();
    allowUncheckRadioButtons();
    choicesListeners(surveysTermsSearchURL, surveysTermsStoreOrUpdateURL, choicesSelectorClass);

});
