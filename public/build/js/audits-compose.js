import {
    toastAlert,
    bsPopoverTooltip,
    enableRevalidationOnInput
} from './helpers.js';

window.addEventListener('load', function() {

    // Initialize nested sortable elements using Sortable.js
    function attachNestedListeners() {
        // Initialize nested receivers
        var nestedReceiver = [].slice.call(document.querySelectorAll('.nested-receiver'));
        if (nestedReceiver) {
            Array.from(nestedReceiver).forEach(function(nestedSortReceiver) {
                new Sortable(nestedSortReceiver, {
                    handle: '.handle-receiver',
                    swap: true,
                    swapClass: 'bg-warning-subtle',
                    animation: 150,
                    fallbackOnBody: true,
                    invertSwap: true,
                    swapThreshold: 0.65,
                    sort: true,
                    group: 'shared',
                    onUpdate: function () {
                        updateBlockPositions(nestedSortReceiver);
                    }
                });
            });
        }

        // Initialize nested elements
        /*
        var nestedElements = [].slice.call(document.querySelectorAll('.nested-element'));
        if (nestedElements) {
            Array.from(nestedElements).forEach(function(nestedSortElements) {
                new Sortable(nestedSortElements, {
                    handle: '.handle',
                    swap: true,
                    swapClass: 'bg-warning-subtle',
                    group: {
                        name: 'shared',
                        pull: 'clone',
                        put: false
                    },
                    sort: false,
                    animation: 150,
                    fallbackOnBody: true,
                    invertSwap: true,
                    swapThreshold: 0.65,
                });
            });
        }
        */
        // Initialize nested receiver blocks
        var nestedReceiverBlock = [].slice.call(document.querySelectorAll('.nested-receiver-block'));
        if (nestedReceiverBlock) {
            Array.from(nestedReceiverBlock).forEach(function(nestedSortReceiverBlock) {
                new Sortable(nestedSortReceiverBlock, {
                    handle: '.handle-receiver-block',
                    swap: true,
                    swapClass: 'bg-warning-subtle',
                    animation: 150,
                    fallbackOnBody: true,
                    invertSwap: true,
                    swapThreshold: 0.65,
                    sort: true,
                    onUpdate: function () {
                        updateTopicPositions(nestedSortReceiverBlock);
                    }
                });
            });
        }
    }
    attachNestedListeners();

    /**
     * Update the positions of topics within a block
     * @param {HTMLElement} nestedSortReceiverBlock - The block containing the topics
     */
    function updateTopicPositions(nestedSortReceiverBlock) {
        var topics = nestedSortReceiverBlock.children;
        Array.from(topics).forEach(function(topic, idx) {
            var newPositionInput = topic.querySelector('[name$="[\'new_position\']"]');
            if (newPositionInput) {
                newPositionInput.value = idx;
            }
        });
    }

    /**
     * Update the positions of blocks within a receiver
     * @param {HTMLElement} nestedSortReceiver - The receiver containing the blocks
     */
    function updateBlockPositions(nestedSortReceiver) {
        var blocks = nestedSortReceiver.children;
        Array.from(blocks).forEach(function(block, idx) {
            var newPositionInput = block.querySelector('[name$="[\'new_position\']"]');
            if (newPositionInput) {
                newPositionInput.value = idx;
            }
        });
    }

    //Populate the new_position input fields after the page loads
    function updatePositionsOnLoad() {
        // Update positions for blocks
        var nestedSortReceivers = document.querySelectorAll('.nested-receiver');
        Array.from(nestedSortReceivers).forEach(function(nestedSortReceiver) {
            updateBlockPositions(nestedSortReceiver);
        });

        // Update positions for topics
        var nestedReceiverBlocks = document.querySelectorAll('.nested-receiver-block');
        Array.from(nestedReceiverBlocks).forEach(function(nestedReceiverBlock) {
            updateTopicPositions(nestedReceiverBlock);
        });
    }
    updatePositionsOnLoad();


    // Add new step block
    var btnAddBlock = document.getElementById('btn-add-block');
    if(btnAddBlock){
        btnAddBlock.addEventListener('click', function(event) {
            event.preventDefault();

            addNewBlock();

            attachRemoveButtonListeners();
        });
    }

    // Add a new step block to the form
    function addNewBlock() {
        const blocksContainer = document.querySelector('.nested-receiver');
        if (blocksContainer) {
            const newBlockIndex = blocksContainer.children.length;
            const newBlock = document.createElement('div');
            newBlock.id = newBlockIndex;
            newBlock.className = 'accordion-item block-item mt-0 mb-3 border-dark p-0';

            newBlock.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control" name="[${newBlockIndex}]['stepData']['step_name']" placeholder="Informe o Título/Setor/Departamento/Etapa" autocomplete="off" maxlength="100" required>

                    <input type="hidden" name="[${newBlockIndex}]['stepData']['original_position']" value="${newBlockIndex}" tabindex="-1">
                    <input type="hidden" name="[${newBlockIndex}]['stepData']['new_position']" tabindex="-1">

                    <span class="btn btn-outline-light cursor-n-resize handle-receiver" title="Reordenar"><i class="ri-arrow-up-down-line text-body"></i></span>

                    <span class="btn btn-outline-light btn-accordion-toggle"><i class="ri-arrow-up-s-line"></i></span>
                </div>
                <div class="accordion-collapse collapse show">
                    <div class="nested-receiver-block border-1 border-dashed border-dark mt-0 p-1 rounded-0"></div>

                    <div class="clearfix">
                        <span class="btn btn-outline-light btn-remove-block float-start" data-target="${newBlockIndex}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right" title="Remover Bloco"><i class="ri-delete-bin-line text-danger text-opacity-50"></i></span>

                        <span class="btn btn-outline-light btn-add-topic float-end cursor-copy text-theme" data-block-index="${newBlockIndex}" title="Adicionar Tópico"><i class="ri-menu-add-line"></i></span>
                    </div>
                </div>
            `;
            blocksContainer.appendChild(newBlock);

            attachElementAccordionToggleButtonListeners();

            attachNestedListeners();

            updatePositionsOnLoad();

            // Attach event listener to the new "Add Topic" button
            newBlock.querySelector('.btn-add-topic').addEventListener('click', function(event) {
                event.preventDefault();

                const stepIndex = parseInt(this.getAttribute('data-block-index'));

                addNewTopic(stepIndex);

                attachRemoveButtonListeners();

            });
        }
    }

    /**
     * Add a new topic to a step block
     * @param {number} stepIndex - The index of the step block
     */
    function addNewTopic(stepIndex) {
        const blockContainer = document.querySelector(`.nested-receiver .block-item[id="${stepIndex}"] .nested-receiver-block`);

        if (blockContainer) {
            const newTopicIndex = blockContainer.children.length;
            const newTopic = document.createElement('div');
            newTopic.className = 'input-group mt-1 mb-1';
            newTopic.id = `${stepIndex}${newTopicIndex}`;

            newTopic.innerHTML = `
                <span class="btn btn-outline-light btn-remove-topic" data-target="${stepIndex}${newTopicIndex}" title="Remover Bloco"><i class="ri-delete-bin-line text-danger text-opacity-50"></i></span>

                <input type="text" class="form-control" name="[${stepIndex}]['topicData'][${newTopicIndex}]['topic_name']" placeholder="Informe o tópico" title="Exemplo: Este setor/departamento está organizado?... O abastecimento de produtos/insumos está em dia?" maxlength="100" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" required>

                <input type="hidden" name="[${stepIndex}]['topicData'][${newTopicIndex}]['original_position']" value="${newTopicIndex}" tabindex="-1">
                <input type="hidden" name="[${stepIndex}]['topicData'][${newTopicIndex}]['new_position']" tabindex="-1">

                <span class="btn btn-outline-light cursor-n-resize handle-receiver-block" title="Reordenar"><i class="ri-arrow-up-down-line text-body"></i></span>
            `;
            blockContainer.appendChild(newTopic);

            bsPopoverTooltip();

            attachNestedListeners();

            updatePositionsOnLoad();
        }
    }

    // Attach event listeners to remove step block and topic buttons
    function attachRemoveButtonListeners() {
        setTimeout(function() {
            var removeBlockButtons = document.querySelectorAll('.btn-remove-block');
            var removeTopicButtons = document.querySelectorAll('.btn-remove-topic');

            function removeBlock(event) {
                event.preventDefault();

                const target = event.currentTarget.getAttribute("data-target");

                var isConfirmed = confirm('Certeza que deseja deletar este Bloco?');
                if (isConfirmed) {
                    var blockElement = document.querySelector('.block-item[id="' + target + '"]');
                    if (blockElement) {
                        blockElement.remove();

                        event.stopPropagation();

                        return;
                    }
                }
                return;
            }

            function removeTopic(event) {
                event.preventDefault();

                const target = event.currentTarget.getAttribute("data-target");

                var isConfirmed = confirm('Certeza que deseja deletar este Tópico?');
                if (isConfirmed) {
                    var topicElement = document.querySelector('.nested-receiver-block .input-group[id="' + target + '"]');
                    if (topicElement) {
                        topicElement.remove();

                        event.stopPropagation();

                        return;
                    }
                }
                return;
            }

            if (removeBlockButtons) {
                removeBlockButtons.forEach(function(button) {
                    if (!button.hasAttribute('data-listener-attached')) {
                        button.setAttribute('data-listener-attached', 'true');
                        button.addEventListener('click', removeBlock);
                    }
                });
            }

            if (removeTopicButtons) {
                removeTopicButtons.forEach(function(button) {
                    if (!button.hasAttribute('data-listener-attached')) {
                        button.setAttribute('data-listener-attached', 'true');
                        button.addEventListener('click', removeTopic);
                    }
                });
            }

            updatePositionsOnLoad();
        }, 100);
    }
    attachRemoveButtonListeners();

    // Attach event listeners to accordion toggle buttons
    function attachElementAccordionToggleButtonListeners() {
        setTimeout(function() {
            var accordionToggleButtons = document.querySelectorAll('.btn-accordion-toggle');
            if (accordionToggleButtons) {
                accordionToggleButtons.forEach(function(button) {
                    if (!button.hasAttribute('data-listener-attached')) {
                        button.setAttribute('data-listener-attached', 'true');
                        button.addEventListener('click', function(event) {
                            event.preventDefault();

                            const accordionCollapse = button.closest('.accordion-item').querySelector('.accordion-collapse');
                            const icon = button.querySelector('i');

                            if (accordionCollapse.classList.contains('show')) {
                                accordionCollapse.classList.remove('show');
                                icon.classList.remove('ri-arrow-up-s-line');
                                icon.classList.add('ri-arrow-down-s-line');
                            } else {
                                accordionCollapse.classList.add('show');
                                icon.classList.remove('ri-arrow-down-s-line');
                                icon.classList.add('ri-arrow-up-s-line');
                            }

                            return;
                        });
                    }
                });
            }
        }, 100);
    }
    attachElementAccordionToggleButtonListeners();


    // Ajax to store or update data
    var btnStoreOrUpdate = document.getElementById('btn-audits-compose-store-or-update');
    if (btnStoreOrUpdate) {
        btnStoreOrUpdate.addEventListener('click', function (event) {
            event.preventDefault();

            // Validate form
            var form = document.getElementById('auditsComposeForm');
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


            var url = id ? auditsComposeUpdateURL + '/' + id : auditsComposeStoreURL;
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
                            makeFormPreviewRequest(response.id);
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
    var btnToggleStatus = document.getElementById('btn-audits-compose-toggle-status');
    if (btnToggleStatus) {
        btnToggleStatus.addEventListener('click', function(event) {
            event.preventDefault();

            var statusTo = btnToggleStatus.getAttribute('data-status-to');
            var composeId = btnToggleStatus.getAttribute('data-compose-id');

            var xhr = new XMLHttpRequest();

            xhr.open('POST', '/audit-compose/toggle-status/' + composeId + '/' + statusTo, true);
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

                            // Store status message in session storage and reload page
                            setTimeout(function() {
                                //localStorage.setItem('statusAlert', 'Status atualizado para ' + translatedStatus);

                                location.reload();
                            }, 2000);
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
    function makeFormPreviewRequest(idValue) {
        if (idValue) {
            var xhrPreview = new XMLHttpRequest();

            //xhrPreview.open('GET', auditsComposeShowURL + '/' + idValue + '&preview=ture', true);
            xhrPreview.open('GET', auditsComposeShowURL + '/' + encodeURIComponent(idValue) + '?preview=true', true);
            xhrPreview.setRequestHeader('Cache-Control', 'no-cache, no-store, must-revalidate'); // Prevents caching
            xhrPreview.setRequestHeader('Pragma', 'no-cache'); // For legacy HTTP 1.0 servers
            xhrPreview.setRequestHeader('Expires', '0'); // Proxies
            xhrPreview.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhrPreview.onreadystatechange = function () {
                if (xhrPreview.readyState === 4) {
                    if (xhrPreview.status === 200) {
                        // Parse the response HTML
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(xhrPreview.responseText, 'text/html');

                        // Extract the content of the div with the ID 'content'
                        var contentDiv = doc.getElementById('content');
                        var contentHtml = contentDiv ? contentDiv.innerHTML : '';

                        // Update the preview div with the extracted content
                        if(contentHtml){
                            document.getElementById('load-preview').innerHTML = contentHtml;

                            bsPopoverTooltip();
                        }
                    } else {
                        // Handle error
                        toastAlert('Error loading preview', 'danger', 10000);
                    }
                }
            };
            xhrPreview.send();
        }
    }
    makeFormPreviewRequest(idValue);


    enableRevalidationOnInput();

    // Attach nested listeners on drag and drop events
    /*
    ['drop', 'dragend'].forEach(eventType => {
        document.addEventListener(eventType, function() {
            attachNestedListeners();
        });
    });
    */
});
