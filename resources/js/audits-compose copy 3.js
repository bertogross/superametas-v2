import {
    toastAlert,
    bsPopoverTooltip
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
                    group: 'shared'
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
                });
            });
        }
    }
    attachNestedListeners();

    // Add new step block
    document.getElementById('btn-add-block').addEventListener('click', function(event) {
        event.preventDefault();

        addNewStep();

        attachRemoveButtonListeners();
    });

    // Function to add a new step block
    function addNewStep() {
        const blocksContainer = document.querySelector('.nested-receiver');
        if (blocksContainer) {
            const newBlockIndex = blocksContainer.children.length;
            const newBlock = document.createElement('div');
            newBlock.id = newBlockIndex;
            newBlock.className = 'accordion-item block-item mt-0 mb-3 border-dark p-0';

            newBlock.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control" name="audit_compose['step'][${newBlockIndex}]" placeholder="Informe o Título/Setor/Departamento/Etapa" autocomplete="off" maxlength="100" required>

                    <button type="button" class="btn btn-outline-light cursor-n-resize handle-receiver" title="Reordenar" tabindex="0"><i class="ri-arrow-up-down-line text-body"></i></button>

                    <button type="button" class="btn btn-outline-light btn-accordion-toggle" tabindex="0"><i class="ri-arrow-up-s-line"></i></button>
                </div>
                <div class="accordion-collapse collapse show">
                    <div class="nested-receiver-block border-1 border-dashed border-dark mt-0 p-1 rounded-0"></div>

                    <div class="clearfix">
                        <button type="button" class="btn btn-outline-light btn-remove-block float-start" data-target="${newBlockIndex}" title="Remover Bloco" tabindex="0"><i class="ri-delete-bin-line text-danger text-opacity-50"></i></button>

                        <button type="button" class="btn btn-outline-light btn-add-topic float-end cursor-copy text-theme" data-block-index="${newBlockIndex}" title="Adicionar Tópico" tabindex="0"><i class="ri-menu-add-line"></i></button>
                    </div>
                </div>
            `;
            blocksContainer.appendChild(newBlock);

            attachElementAccordionToggleButtonListeners();

            attachNestedListeners();

            // Attach event listener to the new "Add Topic" button
            newBlock.querySelector('.btn-add-topic').addEventListener('click', function(event) {
                event.preventDefault();

                const stepIndex = parseInt(this.getAttribute('data-block-index'));

                addNewTopic(stepIndex);
            });
        }
    }

    // Function to add a new topic to a step block
    function addNewTopic(stepIndex) {
        const blockContainer = document.querySelector(`.nested-receiver .block-item[id="${stepIndex}"] .nested-receiver-block`);

        if (blockContainer) {
            const newTopicIndex = blockContainer.children.length;
            const newTopic = document.createElement('div');
            newTopic.className = 'input-group mt-1 mb-1';
            newTopic.id = `${stepIndex}${newTopicIndex}`;

            newTopic.innerHTML = `
                <button type="button" class="btn btn-outline-light btn-remove-topic" data-target="${stepIndex}${newTopicIndex}" title="Remover Bloco" tabindex="0"><i class="ri-delete-bin-line text-danger text-opacity-50"></i></button>

                <input type="text" class="form-control" name="audit_compose['step'][${stepIndex}]['topic'][${newTopicIndex}]" placeholder="Informe o tópico" title="Exemplo: Este setor/departamento está organizado?... O abastecimento de produtos/insumos está em dia?" maxlength="100" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" required>

                <button type="button" class="btn btn-outline-light cursor-n-resize handle-receiver-block" title="Reordenar" tabindex="0"><i class="ri-arrow-up-down-line text-body"></i></button>
            `;
            blockContainer.appendChild(newTopic);

            bsPopoverTooltip();

            attachRemoveButtonListeners();

            attachNestedListeners();
        }
    }

    // Function to attach event listeners to remove step block and topic buttons
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
        }, 100);
    }
    attachRemoveButtonListeners();

    // Function to attach event listeners to accordion toggle buttons
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


    if(document.getElementById('btn-audits-compose-store')){
        document.getElementById('btn-audits-compose-store').addEventListener('click', function(event) {
            event.preventDefault();

            // Validate form
            var form = document.getElementById('auditsComposeForm');
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            // Collect form data
            var formData = new FormData(form);
            var auditComposeSteps = {};
            var isValid = true;
            document.querySelectorAll('.block-item').forEach(function(block) {
                var stepInput = block.querySelector('[name^="audit_compose[\'step\']"]');
                var step = stepInput.value;
                var index = stepInput.getAttribute('name').match(/\['step'\]\[(\d+)\]/)[1];
                var topics = {};
                block.querySelectorAll('[name^="audit_compose[\'step\'][' + parseInt(index) + '][\'topic\']"]').forEach(function(topicInput) {
                    if (topicInput.value.trim() !== '') {
                        var topicIndex = topicInput.getAttribute('name').match(/\['topic'\]\[(\d+)\]/)[1];
                        topics[topicIndex] = topicInput.value;
                    }
                });
                if (Object.keys(topics).length === 0) {
                    isValid = false;
                    console.log('Error: Each step must have at least one topic');
                } else {
                    auditComposeSteps[parseInt(index)] = { step: step, topics: topics };
                }
            });

            if (!isValid || Object.keys(auditComposeSteps).length === 0) {
                return;
            }

            console.log('audit_compose_steps:', JSON.stringify(auditComposeSteps));  // Debugging log

            formData.append('audit_compose_steps', JSON.stringify(auditComposeSteps));

            // Send Ajax request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', auditsComposeStoreURL, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        // Handle success
                        console.log('Data saved successfully');
                        toastAlert('Data saved successfully', 'success', 10000);
                    } else {
                        // Handle error
                        console.log('Error saving data');
                        console.log(xhr.responseText);
                        toastAlert('Error saving data', 'danger', 10000);
                    }
                }
            };
            xhr.send(formData);
        });


    }


    // Attach nested listeners on drag and drop events
    /*
    ['drop', 'dragend'].forEach(eventType => {
        document.addEventListener(eventType, function() {
            attachNestedListeners();
        });
    });
    */
});
