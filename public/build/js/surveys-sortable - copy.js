import {
    toastAlert,
    bsPopoverTooltip
} from './helpers.js';

import {
    removeInputIfTheSameExistOnTheListing,
    reloadTermsForm
} from './surveys-terms.js';

document.addEventListener('DOMContentLoaded', function() {

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


        // Initialize nested receiver blocks
        var nestedReceiverBlock = [].slice.call(document.querySelectorAll('.nested-sortable-block'));
        if (nestedReceiverBlock) {
            Array.from(nestedReceiverBlock).forEach(function(nestedSortReceiverBlock) {
                new Sortable(nestedSortReceiverBlock, {
                    handle: '.handle-block',
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
    function updateBlockAndTopicPositions() {
        // Update positions for blocks
        var nestedSortReceivers = document.querySelectorAll('.nested-receiver');
        Array.from(nestedSortReceivers).forEach(function(nestedSortReceiver) {
            updateBlockPositions(nestedSortReceiver);
        });

        // Update positions for topics
        var nestedReceiverBlocks = document.querySelectorAll('.nested-sortable-block');
        Array.from(nestedReceiverBlocks).forEach(function(nestedReceiverBlock) {
            updateTopicPositions(nestedReceiverBlock);
        });
    }

    // Attach event listener to the new 'Add new step block'
    /*
    function attachNewBlockButtonListeners() {
        var btnAddBlock = document.getElementById('btn-add-block');
        if(btnAddBlock){
            btnAddBlock.addEventListener('click', function(event) {
                event.preventDefault();

                addNewBlock();

                attachElementAccordionToggleButtonListeners();
                attachNestedListeners();
                updateBlockAndTopicPositions();
                attachRemoveButtonListeners();
            });
        }
    }
    */
    function attachNewBlockButtonListeners() {
        document.addEventListener('click', function(event) {
            if (event.target && event.target.id === 'btn-add-block') {
                event.preventDefault();
                addNewBlock();
                attachElementAccordionToggleButtonListeners();
                attachNestedListeners();
                updateBlockAndTopicPositions();
                attachRemoveButtonListeners();
            }
        });
    }

    // Add a new step block to the form
    function addNewBlock(value = '', id = '') {
        const blocksContainer = document.querySelector('.nested-receiver');
        if (blocksContainer) {
            const newBlockIndex = blocksContainer.children.length;
            const newBlock = document.createElement('div');
            newBlock.id = newBlockIndex;
            newBlock.className = 'accordion-item block-item mt-3 mb-0 border-dark border-1 rounded rounded-2 p-0';

            newBlock.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control text-theme" name="steps[${newBlockIndex}]['stepData']['step_name']" placeholder="Informe o Setor/Etapa" autocomplete="off" maxlength="100" value="${value}" readonly required>

                    <input type="hidden" name="steps[${newBlockIndex}]['stepData']['term_id']" value="${id}" tabindex="-1" value="custom">
                    <input type="hidden" name="steps[${newBlockIndex}]['stepData']['type']" tabindex="-1" value="custom">
                    <input type="hidden" name="steps[${newBlockIndex}]['stepData']['new_position']" tabindex="-1">
                    <input type="hidden" name="steps[${newBlockIndex}]['stepData']['original_position']" tabindex="-1" value="${newBlockIndex}">

                    <span class="btn btn-ghost-dark btn-icon rounded-pill cursor-n-resize handle-receiver ri-arrow-up-down-line text-body" title="Reordenar"></span>

                    <span class="btn btn-ghost-dark btn-icon rounded-pill btn-accordion-toggle ri-arrow-up-s-line"></span>
                </div>
                <div class="accordion-collapse collapse show">
                    <div class="nested-sortable-block mt-0 p-1"></div>

                    <div class="clearfix">
                        <span class="btn btn-ghost-dark btn-icon btn-add-topic rounded-pill float-end cursor-copy text-theme" data-block-index="${newBlockIndex}" title="Adicionar Tópico"><i class="ri-menu-add-line"></i></span>

                        <span class="btn btn-ghost-danger btn-icon rounded-pill btn-remove-block float-start" data-target="${newBlockIndex}" title="Remover Bloco"><i class="ri-delete-bin-7-fill"></i></span>
                    </div>
                </div>
            `;
            blocksContainer.appendChild(newBlock);

            attachNewTopicButtonListeners();

            attachRemoveButtonListeners();

            /*
            setTimeout(function() {
                var selector = `.btn-add-topic[data-block-index="${newBlockIndex}"]`;
                document.querySelector(selector).click();
            }, 100);
            */
        }
    }


    function attachbtnAddMultipleBlocksListeners() {
        // Attach the event listener to a parent element
        // This should be an element that is not being replaced by the AJAX call
        document.addEventListener('click', async function(event) {
            // Check if the clicked element is the one you're interested in
            if (event.target && event.target.id === 'btn-add-multiple-blocks') {
                event.preventDefault();

                const form = document.getElementById('surveysPopulateTermForm');
                if (!form) {
                    console.error('Form not found');
                    return;
                }

                removeInputIfTheSameExistOnTheListing();

                var checkboxes = document.querySelectorAll('input[name="step_terms[]"]:checked');
                var checkedItems = Array.from(checkboxes).map(function(checkbox) {
                    var label = document.querySelector('label[for="' + checkbox.id + '"]');
                    let term_label = label ? label.textContent.trim() : '';
                    let term_id = checkbox.value;

                    setTimeout(function() {
                        addNewBlock(term_label, term_id);
                    }, 500);
                });

                if (checkedItems) {
                    toastAlert('Termos adicionados', 'success', 5000);
                    document.querySelector('[data-bs-dismiss="modal"]').click();
                }
            }
        });
    }




    // Function to observe DOM changes
    function observeDOMChanges() {
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    removeInputIfTheSameExistOnTheListing();
                }
            });
        });

        var config = { childList: true, subtree: true };

        // Start observing the target node for configured mutations
        observer.observe(document.body, config);
    }


    /**
     * Add a new topic to a step block
     * @param {number} stepIndex - The index of the step block
     */
    function addNewTopic(stepIndex) {
        const blockContainer = document.querySelector(`.block-item[id="${stepIndex}"] .nested-sortable-block`);

        if (blockContainer) {
            const newTopicIndex = blockContainer.children.length;
            const newTopic = document.createElement('div');
            newTopic.className = 'step-topic mt-1 mb-1';
            newTopic.id = `${stepIndex}${newTopicIndex}`;

            newTopic.innerHTML = `
                <div class="row">
                    <div class="col-auto">
                        <span class="btn btn-ghost-danger btn-icon rounded-pill btn-remove-topic" data-target="${stepIndex}${newTopicIndex}" title="Remover Bloco"><i class="ri-delete-bin-3-line"></i></span>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" title="Exemplo: Organização do setor?... Abastecimento de produtos/insumos está em dia?" data-placeholder="Tópico..." name="steps[${stepIndex}]['topics']['question']" placeholder="Exemplo: Organização do Departamento" maxlength="150" required></input>
                    </div>
                    <div class="col-auto">
                        <span class="btn btn-ghost-dark btn-icon rounded-pill cursor-n-resize handle-block" title="Reordenar"><i class="ri-arrow-up-down-line"></i></span>
                    </div>
                </div>
                <input type="hidden" name="steps[${stepIndex}]['topics']['original_position']" value="${newTopicIndex}" tabindex="-1">
                <input type="hidden" name="steps[${stepIndex}]['topics']['new_position']" tabindex="-1">
            `;
            blockContainer.appendChild(newTopic);
        }
    }

    // Attach event listener to the new "Add Topic" button
    /*
    function attachNewTopicButtonListeners() {
        var newTopicButton = document.querySelectorAll('.btn-add-topic');
        if (newTopicButton) {
            newTopicButton.forEach(function(button) {
                if (button.hasAttribute('data-block-index')) {
                    button.addEventListener('click', function(event) {
                        event.preventDefault();

                        const stepIndex = parseInt(this.getAttribute('data-block-index'));

                        addNewTopic(stepIndex);

                        //bsPopoverTooltip();
                        attachNestedListeners();
                        updateBlockAndTopicPositions();
                        attachRemoveButtonListeners();
                        //choicesListeners(surveysTermsSearchURL, surveysTermsStoreOrUpdateURL, choicesSelectorClass);
                    });
                }
            });
        }
    }
    */
    function attachNewTopicButtonListeners() {
        document.addEventListener('click', function(event) {
            if (event.target && event.target.classList.contains('btn-add-topic')) {
                event.preventDefault();

                const stepIndex = parseInt(event.target.getAttribute('data-block-index'));
                addNewTopic(stepIndex);

                attachNestedListeners();
                updateBlockAndTopicPositions();
                attachRemoveButtonListeners();
                //choicesListeners(surveysTermsSearchURL, surveysTermsStoreOrUpdateURL, choicesSelectorClass);
            }
        });
    }


    // Attach event listeners to remove step block and topic buttons
    /*
    function attachRemoveButtonListeners() {
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
                var topicElement = document.querySelector('.nested-sortable-block .step-topic[id="' + target + '"]');
                if (topicElement) {
                    topicElement.remove();

                    updateBlockAndTopicPositions();

                    event.stopPropagation();

                    return;
                }
            }
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

        return;
    }
    */
    function attachRemoveButtonListeners() {
        document.addEventListener('click', function(event) {
            // Check if the clicked element is a remove block button
            if (event.target && event.target.classList.contains('btn-remove-block')) {
                event.preventDefault();

                const targetId = event.target.getAttribute("data-target");
                const isConfirmed = confirm('Certeza que deseja deletar este Bloco?');
                if (isConfirmed) {
                    const blockElement = document.querySelector('.block-item[id="' + targetId + '"]');
                    if (blockElement) {
                        blockElement.remove();
                        updateBlockAndTopicPositions();
                        reloadTermsForm();

                        event.stopPropagation();

                        return;
                    }
                }
            }

            // Check if the clicked element is a remove topic button
            if (event.target && event.target.classList.contains('btn-remove-topic')) {
                event.preventDefault();

                const targetId = event.target.getAttribute("data-target");
                const isConfirmed = confirm('Certeza que deseja deletar este Tópico?');
                if (isConfirmed) {
                    const topicElement = document.querySelector('.nested-sortable-block .step-topic[id="' + targetId + '"]');
                    if (topicElement) {
                        topicElement.remove();
                        updateBlockAndTopicPositions();

                        event.stopPropagation();

                        return;
                    }
                }
            }
        });
    }




    // Attach event listeners to accordion toggle buttons
    function attachElementAccordionToggleButtonListeners() {
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
    }



    // Call the function when the DOM is fully loaded
    attachNestedListeners();
    attachNewBlockButtonListeners();
    attachNewTopicButtonListeners();
    attachRemoveButtonListeners();
    attachElementAccordionToggleButtonListeners();
    updateBlockAndTopicPositions();
    observeDOMChanges();
    attachbtnAddMultipleBlocksListeners();

});
