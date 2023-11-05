import {
    toastAlert,
    bsPopoverTooltip,
    enableRevalidationOnInput,
    makeFormPreviewRequest
} from './helpers.js';

window.addEventListener('load', function() {

    // Ajax to store or update data
    var btncreateOrUpdate = document.getElementById('btn-surveys-compose-store-or-update');
    if (btncreateOrUpdate) {
        btncreateOrUpdate.addEventListener('click', function (event) {
            event.preventDefault();

            // Validate form
            var form = document.getElementById('surveysComposeForm');
            if (!form) {
                console.error('Form not found');
                return;
            }

            if (!form.checkValidity()) {
                event.stopPropagation();

                form.classList.add('was-validated');

                toastAlert('Preencha os campos obrigatórios', 'danger', 5000);

                return;
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
                var topicMatch = key.match(/\['topic_name'\]\[(\d+)\]/);

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
            //console.log(JSON.stringify(object, null, 2));
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
                const topicNameKey = `[${i}]['topicData'][${j}]['topic_name']`;
                if (!data[topicNameKey]) break;

                const topic = {
                    topic_name: data[topicNameKey],
                    original_position: parseInt(data[`[${i}]['topicData'][${j}]['original_position']`], 10),
                    new_position: parseInt(data[`[${i}]['topicData'][${j}]['new_position']`], 10)
                };
                topicData.push(topic);
                }

                transformedData.push({ stepData, topicData });
            }
            console.log(JSON.stringify(transformedData, null, 2));
            //return;

            formData.append('item_steps', JSON.stringify(transformedData, null, 2));

            // Send Ajax request
            var xhr = new XMLHttpRequest();


            var url = id ? surveysComposeCreateOrUpdateURL + '/' + id : surveysComposeCreateOrUpdateURL;
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

                            btncreateOrUpdate.textContent = 'Atualizar'; // Change button text
                            btncreateOrUpdate.classList.remove('btn-theme'); // Remove old class
                            btncreateOrUpdate.classList.add('btn-outline-theme'); // Add new class

                            //localStorage.setItem('statusAlert', 'Status atualizado para ' + translatedStatus);

                            document.querySelector('input[name="id"]').value = response.id;

                            // Make the preview request
                            makeFormPreviewRequest(response.id, surveysComposeShowURL);
                        } else {
                            toastAlert(response.message, 'danger', 10000);
                        }
                    } else {
                        // Handle error
                        var errorMessage = id ? 'Error updating data' : 'Error saving data';
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
                            var translatedStatus = statusTo === 'active' ? 'ativo' : 'desabilitado';

                            toastAlert('Status atualizado para: '+translatedStatus+'', 'success', 5000);

                            if (statusTo === 'disabled') {
                                btnToggleStatus.setAttribute('data-status-to', 'active');
                                btnToggleStatus.classList.remove('btn-outline-danger');
                                btnToggleStatus.classList.add('btn-outline-success');
                                btnToggleStatus.textContent = 'Ativar'; // Change the text inside the button
                            } else {
                                btnToggleStatus.setAttribute('data-status-to', 'disabled');
                                btnToggleStatus.classList.remove('btn-outline-success');
                                btnToggleStatus.classList.add('btn-outline-danger');
                                btnToggleStatus.textContent = 'Desativar'; // Change the text inside the button
                            }

                            makeFormPreviewRequest(composeId, surveysComposeShowURL);

                            //location.reload();
                        } else {
                            // Handle error
                            toastAlert('Erro ao tentar atualizar o status: ' + response.message, 'error', 5000);
                        }
                    } else {
                        // Handle error
                        console.log('Error updating status: ' + response.message);

                        toastAlert('Erro ao tentar atualizar o status: ' + response.message, 'error', 5000);
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
    enableRevalidationOnInput();
});
