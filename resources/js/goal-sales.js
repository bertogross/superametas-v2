import { ToastAlert, MultipleModal, formatNumberInput, helperSumValues } from './helpers.js';

window.addEventListener('load', function () {

    /**
     * Toggle the display of the custom meantime input field based on the selected option in the meantime select dropdown.
     */
    const meantimeSelect = document.querySelector('select[name="meantime"]');
    const customMeantimeDiv = document.querySelector('.custom_meantime_is_selected');
    const customMeantimeInput = document.querySelector('.custom_meantime_is_selected input');

    function toggleCustomMeantimeInput() {
        const selectedOption = meantimeSelect.value;
        if (selectedOption === 'custom') {
            customMeantimeDiv.style.display = 'block';
        } else {
            customMeantimeDiv.style.display = 'none';

            if (customMeantimeInput) {
                customMeantimeInput.value = '';
            }
        }
    }
    toggleCustomMeantimeInput();

    meantimeSelect.addEventListener('change', toggleCustomMeantimeInput);

    /**
     * Initialize flatpickr with specific options for elements with the class 'flatpickr-range-month'.
     */
    const flatpickrRangeMonthElements = document.querySelectorAll('.flatpickr-range-month');

    if (flatpickrRangeMonthElements) {
        flatpickrRangeMonthElements.forEach(function (element) {
            flatpickr(element, {
                locale: 'pt',
                mode: "range",
                allowInput: false,
                static: true,
                altInput: true,
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "Y-m",
                        altFormat: "F/Y",
                        theme: "dark"
                    })
                ]
            });
        });
    }

    /**
     * Hide the load listing element slowly on form change.
     */
    function hideLoadListingOnFormChange() {
        // Get the form element
        var filterForm = document.getElementById("filterForm");

        // Get the load listing element
        var loadListing = document.getElementById("load-listing");

        // Add a change event listener to the form
        filterForm.addEventListener("change", function () {
            // Hide the load listing element slowly
            loadListing.classList.add("hide-slowly");
        });
    }
    hideLoadListingOnFormChange();


    /**
     * Load the content for the Goal Sales Settings
     */
    async function loadGoalSalesSettingsModal() {
        try {
            const response = await fetch('/goal-sales/settings', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const content = await response.text();
            document.getElementById('modalContainer').innerHTML = content;

            const modalElement = document.getElementById('goalSalesSettingsModal');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false,
            });
            modal.show();

            attachModalEventListeners();
        } catch (error) {
            console.error('Error fetching modal content:', error);
            ToastAlert('Não foi possível carregar o conteúdo', 'error', 10000);
        }
    }

    // Event listener for the 'btn-goal-sales-settings' button
    if(document.getElementById('btn-goal-sales-settings')){
        document.getElementById('btn-goal-sales-settings').addEventListener('click', function(event) {
            event.preventDefault();

            loadGoalSalesSettingsModal();
        });
    }

    /**
     * Load the content for the Goal Sales Edit Modal
     */
    async function loadGoalSalesEditModal(meantime, companyId, companyName, purpose) {
        try {
            let url = `/goal-sales/form/${meantime}/${companyId}/${purpose}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const content = await response.text();
            document.getElementById('modalContainer').insertAdjacentHTML('beforeend', content);

            const modalElement = document.getElementById('goalSalesEditModal');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false,
            });
            modal.show();

            const modalTitle = purpose === 'update' ? 'Editar' : 'Adicionar';
            document.querySelector("#goalSalesEditModal .modal-title").innerHTML = `<u>${modalTitle}</u> Meta de Vendas :: <span class="text-theme">${companyName}</span>`;

            const btnText = purpose === 'update' ? 'Atualizar' : 'Salvar';
            document.querySelector("#goalSalesEditModal #btn-goal-sales-update").innerHTML = btnText;

            MultipleModal();

            formatNumberInput();

            // Call helperSumValues after the modal content has been loaded
            helperSumValues('.o-sum-fields-previous', 'sum-result-previous', 0);
            helperSumValues('.o-sum-fields-current', 'sum-result-current', 0);
            helperSumValues('.o-sum-fields', 'sum-result', 0);

            makeGoalSalesUpdate(meantime, companyId);

        } catch (error) {
            console.error('Error fetching modal content:', error);
            ToastAlert('Não foi possível carregar o conteúdo', 'error', 10000);
        }
    }


    // Event listeners for each 'Edit Goal Sales' button
    function attachModalEventListeners(){
        var editButtons = document.querySelectorAll('.btn-goal-sales-edit');
        if(editButtons){
            editButtons.forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();

                    const meantime = this.getAttribute("data-meantime");
                    const companyId = this.getAttribute("data-company-id");
                    const companyName = this.getAttribute("data-company-name");
                    const purpose = this.getAttribute("data-purpose");

                    loadGoalSalesEditModal(meantime, companyId, companyName, purpose);
                });

            });
        }
    }

    // Event listeners for 'Update' button
    function makeGoalSalesUpdate(meantime, companyId) {

        // store/update goalSalesForm
        document.getElementById('btn-goal-sales-update').addEventListener('click', async function(event) {
            event.preventDefault();

            const form = document.getElementById('goalSalesForm');

            let formData = new FormData(form);

            let url = `/goal-sales/post/${meantime}/${companyId}`;

            try {
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
                    // Close current modal
                    document.getElementById('btn-goal-sales-update').closest('.modal').querySelector('.btn-close').click();

                    // Change primary modal tr/button
                    var tr = document.querySelector('#goalSalesSettingsModal tr[data-meantime="'+meantime+'"][data-company="'+companyId+'"]');
                    var button = tr.querySelector('button');
                        button.setAttribute('data-purpose', 'update');
                        button.setAttribute('title', 'Editar Meta');
                        button.classList.remove('btn-outline-theme');
                        button.classList.add('btn-theme');
                        button.innerHTML = 'Editar';

                    tr.querySelector('.meantime').classList.add('text-theme');
                    tr.classList.add('blink');
                    setTimeout(function () {
                        tr.classList.remove('blink');
                    }, 10000);

                    ToastAlert(data.message, 'success', 10000);
                } else {
                    ToastAlert(data.message, 'danger', 60000);
                }
            } catch (error) {
                ToastAlert('Error: ' + error, 'danger', 60000);
                console.error('Error:', error);
            }
        });
    }
});
