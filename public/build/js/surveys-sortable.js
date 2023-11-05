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

    // Attach event listener to the new 'Add new step block'
    function attachNewStepButtonListeners() {
        var btnAddBlock = document.getElementById('btn-add-block');
        if(btnAddBlock){
            btnAddBlock.addEventListener('click', function(event) {
                event.preventDefault();

                addNewBlock();

                attachRemoveButtonListeners();

                attachNewTopicButtonListeners();
            });
        }
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

            attachNewTopicButtonListeners();

            attachRemoveButtonListeners();
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

    // Attach event listener to the new "Add Topic" button
    function attachNewTopicButtonListeners() {
        var newTopicButton = document.querySelectorAll('.btn-add-topic');
        if (newTopicButton) {
            newTopicButton.forEach(function(button) {
                if (button.hasAttribute('data-block-index')) {
                    button.addEventListener('click', function(event) {
                        event.preventDefault();

                        const stepIndex = parseInt(this.getAttribute('data-block-index'));

                        addNewTopic(stepIndex);

                        attachRemoveButtonListeners();
                    });

                }
            });
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



    // Attach nested listeners on drag and drop events
    /*
    ['drop', 'dragend'].forEach(eventType => {
        document.addEventListener(eventType, function() {
            attachNestedListeners();
        });
    });
    */

    // Call the function when the DOM is fully loaded
    attachNestedListeners();
    updatePositionsOnLoad();
    attachNewStepButtonListeners();
    attachNewTopicButtonListeners();
    attachRemoveButtonListeners();
    attachElementAccordionToggleButtonListeners();
});
