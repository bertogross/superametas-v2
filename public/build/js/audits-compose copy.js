import {
    toastAlert
} from './helpers.js';



window.addEventListener('load', function() {


    // Nested sortable
    // https://sortablejs.github.io/Sortable/
    function attachNestedListeners(){
        var nestedReceiver = [].slice.call(document.querySelectorAll('.nested-receiver'));
        if (nestedReceiver){
            // Loop through each nested sortable element
            Array.from(nestedReceiver).forEach(function (nestedSortReceiver){
                new Sortable(nestedSortReceiver, {
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    sort: true,
                    group: 'shared'
                });
            });
        }

        var nestedElements = [].slice.call(document.querySelectorAll('.nested-element'));
        if (nestedElements){
            // Loop through each nested sortable element
            Array.from(nestedElements).forEach(function (nestedSortElements){
                new Sortable(nestedSortElements, {
                    group: {
                        name: 'shared',
                        pull: 'clone',
                        put: false // Do not allow items to be put into this list
                    },
                    sort: false,
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                });
            });
        }

        var nestedReceiverBlock = [].slice.call(document.querySelectorAll('.nested-receiver-block'));
        if (nestedReceiverBlock){
            // Loop through each nested sortable element
            Array.from(nestedReceiverBlock).forEach(function (nestedSortReceiverBlock){
                new Sortable(nestedSortReceiverBlock, {
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    sort: true,
                    group: 'shared2',
                    /*
                    onAdd: function (evt) {
                        var item = evt.item;
                        if (!item.classList.contains('nested-this-can-repeat')) {
                            var items = nestedSortReceiverBlock.children;
                            var duplicates = Array.from(items).filter(function (child) {
                                return child.textContent.trim() === item.textContent.trim() && child !== item;
                            });
                            if (duplicates.length) {
                                nestedSortReceiverBlock.removeChild(item);
                                toastAlert('Bloco não aceita repetição de Texto e Upload', 'warning', 10000);
                                return;
                            } else {
                                // Prepend the dropped element to be the first inside the .nested-receiver-block
                                nestedSortReceiverBlock.insertBefore(item, nestedSortReceiverBlock.firstChild);
                            }
                        }
                        // Prepend the dropped element to be the first inside the .nested-receiver-block
                        nestedSortReceiverBlock.insertBefore(item, nestedSortReceiverBlock.firstChild);

                    },
                    */
                });
            });
        }

        var nestedElementsBlock = [].slice.call(document.querySelectorAll('.nested-element-to-block'));
        if (nestedElementsBlock){
            // Loop through each nested sortable element
            Array.from(nestedElementsBlock).forEach(function (nestedSortElementsBlock){
                new Sortable(nestedSortElementsBlock, {
                    group: {
                        name: 'shared2',
                        pull: 'clone',
                        put: false // Do not allow items to be put into this list
                    },
                    sort: false,
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                });
            });
        }
    }
    attachNestedListeners();


    function attachElementRemoveButtonListeners(){
        var removeElementButtons = document.querySelector('.btn-remove-element');
        if(removeElementButtons){
            //removeElementButtons.forEach(function (button) {
                removeElementButtons.addEventListener('click', function (event) {
                    event.preventDefault();

                    const target = this.getAttribute("data-target");
                    const button = this;

                    var isConfirmed = confirm('Are you sure you want to delete?');
                    if (isConfirmed) {
                        var parentElement = button.closest(target);
                        if (parentElement) {
                            parentElement.remove();

                            return;
                        }
                    }
                });
            //});
        }
    }
    attachElementRemoveButtonListeners();


    function attachElementaccordionToggleButtonListeners() {
        var accordionToggleButtons = document.querySelector('.btn-accordion-toggle');
        if(accordionToggleButtons){
            //accordionToggleButtons.forEach(function (button) {
                accordionToggleButtons.addEventListener('click', function (event) {
                    event.preventDefault();

                    const button = this;

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
            //});
        }
    }
    attachElementaccordionToggleButtonListeners();

    function updateInputNames() {
        const auditComposeSteps = document.querySelectorAll('.accordion-item');

        auditComposeSteps.forEach((step, stepIndex) => {
            console.log('stepIndex', stepIndex);

            const stepInput = step.querySelector('input[name="audit_compose[\'step\'][]"]');
            if (stepInput) {
                stepInput.name = `audit_compose['step'][${stepIndex}]`;

                const topicInputs = step.querySelectorAll('input[name="audit_compose[\'step\'][][\'topic\'][]"]');
                topicInputs.forEach((topicInput, topicIndex) => {
                    console.log('topicIndex', topicIndex);

                    topicInput.name = `audit_compose['step'][${stepIndex}]['topic'][${topicIndex}]`;
                });
            }
        });
    }
    updateInputNames();


    document.getElementById('btn-audits-compose-update').addEventListener('click', async function(event) {
        event.preventDefault();

        updateInputNames();

        const form = document.getElementById('auditsComposeForm');

        if (!form.checkValidity()) {
            event.stopPropagation();

            form.classList.add('was-validated');

            toastAlert('Preencha os campos obrigatórios', 'danger', 5000);

            return;
        }

        let formData = new FormData(form);

        let object = {};
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

        return;

        try {
            let url = auditsComposeUpdateURL;

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

                setTimeout(function () {
                    //location.reload(true);
                }, 5000);
            } else {
                toastAlert(data.message, 'danger', 60000);
            }
        } catch (error) {
            toastAlert('Error: ' + error, 'danger', 60000);
            console.error('Error:', error);
        }
    });


    ['drop', 'dragend'].forEach(eventType => { // 'dragstart', 'dragover', 'drop', 'dragend'
        document.addEventListener(eventType, function () {

            attachNestedListeners();

            attachElementRemoveButtonListeners();

            attachElementaccordionToggleButtonListeners();

            updateInputNames();

        });
    });


});
