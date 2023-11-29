
// Toast Notifications
// Display a toast notification using Bootstrap's Toast API with a backdrop
export function toastAlert(message, type = 'success', duration = 10000) {
    // Remove existing toast containers
    document.querySelectorAll('.toast-container').forEach(element => element.remove());

    // Define the HTML template for the toast
    const icon = type === 'success' ? 'ri-checkbox-circle-fill text-success' : 'ri-alert-fill text-' + type;
    type = type === 'error' ? 'danger' : type;

    const ToastHtml = `
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div class="toast fade show toast-border-${type} overflow-hidden mt-3" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="${icon} align-middle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <button type="button" class="btn-close btn-close-white me-2 m-auto float-end fs-10" data-bs-dismiss="toast" aria-label="Close"></button>
                            <h6 class="mb-0">${message}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Add the toast and backdrop to the end of the document body
    document.body.insertAdjacentHTML('beforeend', ToastHtml);

    // Initialize and show the toast using Bootstrap's API
    const toastElement = document.querySelector('.toast-container .toast');
    const toast = new bootstrap.Toast(toastElement, { autohide: false });
    toast.show();

    // Add event listener to the close button
    const closeButton = document.querySelector('.btn-close');
    closeButton.addEventListener('click', () => {
        toast.hide();
    });

    // If a duration is provided, hide the toast after the duration
    if (duration > 0) {
        setTimeout(() => {
            toast.hide();
        }, duration);
    }

    // Remove the toast container once the toast is completely hidden
    toastElement.addEventListener('hidden.bs.toast', () => {
        document.querySelectorAll('.toast-container').forEach(element => element.remove());
    });
}



export function sweetAlerts(message, urlToRedirect = false, icon = 'success'){
    Swal.fire({
        title: message,
        icon: icon,
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: 'Prosseguir',
        confirmButtonClass: 'btn btn-outline-success w-xs me-2',
        cancelButtonClass: 'btn btn-sm btn-outline-info w-xs',
        denyButtonClass: 'btn btn-danger w-xs me-2',
        buttonsStyling: false,
        denyButtonText: 'Não',
        cancelButtonText: 'Continuar Editando',
        showCloseButton: false,
        allowOutsideClick: false
    }).then(function (result) {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
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
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                    //console.log('I was closed by the timer')
                    if(urlToRedirect){
                        window.location.href = urlToRedirect;
                    }
                }
            });
        }
    })
}

// Multiple Modals
// Maintain modal-open when close another modal
export function multipleModal() {
    setTimeout(function () {
        document.querySelectorAll('.modal').forEach(function (modal) {
            modal.addEventListener('show', function () {
                document.body.classList.add('modal-open');
            });

            modal.addEventListener('hidden', function () {
                document.body.classList.remove('modal-open');
            });
        });

        // Multiple modals overlay
        document.addEventListener('show.bs.modal', function (event) {
            var modal = event.target;
            var modals = Array.from(document.querySelectorAll('.modal')).filter(function (modal) {
                return window.getComputedStyle(modal).display !== 'none';
            });
            var zIndex = 1050 + 10 * modals.length;
            modal.style.zIndex = zIndex;

            var backdrops = document.querySelectorAll('.modal-backdrop:not(.modal-stack)');
            backdrops.forEach(function (backdrop) {
                backdrop.style.zIndex = zIndex - 1;
                backdrop.classList.add('modal-stack');
            });
        });

        destroyModal();
    }, 500);
}

/*
export function preventWizardNavigation(){
    // Using plain JavaScript to disable click on nav items
    document.querySelectorAll('.nav-item .nav-link').forEach(function(tab) {
        tab.addEventListener('click', function(event) {
            if (!this.classList.contains('active')) {
                event.preventDefault();
                event.stopPropagation();
                return;
            }
        });
    });
}
*/

export function formatNumberInput(selector = '.format-numbers', decimals = 0) {
    const numberInputs = document.querySelectorAll(selector);

    function formatValue(value, decimals) {
        value = value.replace(/[^\d,]/g, ''); // Remove non-numeric characters except comma

        if (!value) {
            return '';
        }

        value = value.replace(',', '.'); // Replace comma with dot for parseFloat

        let number = parseFloat(value);

        if (isNaN(number)) {
            return '';
        }

        if (decimals > 0) {
            return number.toLocaleString('pt-BR', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        } else {
            return number.toLocaleString('pt-BR');
        }
    }

    numberInputs.forEach(input => {
        // Format value when typing
        input.addEventListener('input', function(event) {
            if (event.inputType === "deleteContentBackward") {
                return; // If backspace was pressed, just return
            }

            var target = event.target;
            target.value = formatValue(target.value, decimals);
        });

        // Format value on page load
        input.value = formatValue(input.value, decimals);
    });
}

export function getChartColorsArray(chartId) {
    if (document.getElementById(chartId) !== null) {
      var colors = document.getElementById(chartId).getAttribute("data-colors");

      if (colors) {
        colors = JSON.parse(colors);
        return colors.map(function (value) {
          var newValue = value.replace(" ", "");
          if (newValue.indexOf(",") === -1) {
            var color = getComputedStyle(document.documentElement).getPropertyValue(newValue);
            if (color) return color;
            else return newValue;
          } else {
            var val = value.split(',');
            if (val.length == 2) {
              var rgbaColor = getComputedStyle(document.documentElement).getPropertyValue(val[0]);
              rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
              return rgbaColor;
            } else {
              return newValue;
            }
          }
        });
      } else {
        console.warn('data-colors Attribute not found on:', chartId);
      }
    }
}

export function onlyNumbers(number){
    if (number === null || number === undefined) {
        return 0;
    }
    var result = number.toString().replace(/\D/g, '');
    return parseInt(result);
}

export function formatNumber(number, decimalPlaces = 0){
    number = parseFloat(number.replace(',', '.'));

    return Number(number).toLocaleString('pt-BR', { minimumFractionDigits: decimalPlaces, maximumFractionDigits: decimalPlaces });
}

export function sumInputNumbers(from, to, decimal = 0) {
    const inputs = document.querySelectorAll(from);
    const resultDiv = document.querySelector(to);

    if (!inputs) {
        console.error(`Element with Selector "${from}" not found`);
        return;
    }

    if (!resultDiv) {
        console.error(`Element with Selector "${to}" not found`);
        return;
    }

    function updateSum() {
        let sum = 0;
        inputs.forEach(input => {
            const value = onlyNumbers(input.value) || 0;

            sum += value;
        });
        const formatter = new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: decimal,
            maximumFractionDigits: decimal,
        });
        resultDiv.textContent = formatter.format(sum);

    }

    inputs.forEach(input => {
        input.addEventListener('input', updateSum);
    });

    updateSum();
}

export function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();

    // Add SameSite=None and Secure attributes
    var cookieString = cname + "=" + cvalue + ";" + expires + ";path=/;SameSite=Strict;Secure";

    // Set the cookie
    document.cookie = cookieString;
}

export function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
};

// Format file size
export function formatSize(bytes) {
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes === 0) return '0 Byte';
    const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

// Set a value in the session storage
export function setSessionStorage(storageName, value = true) {
    if(value){
        sessionStorage.setItem(storageName, value);
    }else{
        sessionStorage.removeItem(storageName);
    }
}

// Get a value from the session storage.
export function getSessionStorage(storageName) {
    return sessionStorage.getItem(storageName);
}

export function toggleZoomInOut() {
    var zoomTarget = document.querySelector('.toogle_zoomInOut');

    if (zoomTarget) {
        // Set initial zoom level
        var zoomLevel = getSessionStorage('toggle-zoom') || 100;
        zoomLevel = Number(zoomLevel);

        if (zoomLevel < 100 || zoomLevel > 100) {
            zoomTarget.style.transform = 'scale(' + (zoomLevel / 100) + ')';
            zoomTarget.style.transformOrigin = '50% 0px 0px';
            zoomTarget.style.width = '100%';
        }

        // Click events
        document.addEventListener('click', function (event) {
            if (event.target.id === 'zoom_in') {
                event.preventDefault();
                zoomPage(10, event.target, zoomTarget);
            } else if (event.target.id === 'zoom_out') {
                event.preventDefault();
                zoomPage(-10, event.target, zoomTarget);
            } else if (event.target.id === 'zoom_reset') {
                event.preventDefault();
                zoomPage(0, event.target, zoomTarget);
            }
        });

        // Zoom function
        function zoomPage(step, trigger, target) {
            // Zoom just to steps in or out
            if (zoomLevel >= 120 && step > 0 || zoomLevel <= 80 && step < 0) return;

            // Set / reset zoom
            if (step === 0) zoomLevel = 100;
            else zoomLevel = zoomLevel + step;

            // Set page zoom via CSS
            target.style.transform = 'scale(' + (zoomLevel / 100) + ')';
            target.style.transformOrigin = '50% 0';

            // Adjust page to zoom width
            if (zoomLevel > 100) target.style.width = (zoomLevel * 1.2) + '%';
            else target.style.width = '100%';

            document.getElementById('zoom_reset').value = zoomLevel + '%';

            setSessionStorage('toggle-zoom', zoomLevel);

            // Activate / deactivate trigger (use CSS to make them look different)
            if (zoomLevel >= 120 || zoomLevel <= 80) trigger.classList.add('disabled');
            else Array.from(document.querySelectorAll('ul .disabled')).forEach(function (el) {
                el.classList.remove('disabled');
            });

            if (zoomLevel !== 100) document.getElementById('zoom_reset').classList.remove('disabled');
            else document.getElementById('zoom_reset').classList.add('disabled');
        }
    }
}

// Adds event listeners to show a button when an input field in a form changes.
export function showButtonWhenInputChange() {
    // Helper function to handle showing the button and hiding other elements.
    function handleInputChange(form) {
        // Show the button
        var wrapFormBtn = form.querySelector('.wrap-form-btn');
        if (wrapFormBtn) {
            wrapFormBtn.classList.remove('d-none');
        }

        // Hide the listing and footer if not in a modal
        if (!document.body.classList.contains('modal-open')) {
            var loadListing = document.getElementById('load-listing');
            //Hide the load listing element slowly on form change.
            if (loadListing) {
                loadListing.classList.add("hide-slowly");
            }
        }
    }

    // Add event listener for change events
    document.addEventListener('change', function (event) {
        if (event.target.closest('form')) {
            handleInputChange(event.target.closest('form'));
        }
    });

    // Add event listener for keyup events
    document.addEventListener('keyup', function (event) {
        if (event.target.closest('form')) {
            handleInputChange(event.target.closest('form'));
        }
    });
}

// Anchor
// https://developer.mozilla.org/pt-BR/docs/Web/API/Element/scrollIntoView
export function goTo(id, top = 150, block = 'start') {//start, end
    let element = document.getElementById(id);

    if (element !== null) {
        element.scrollIntoView({ behavior: 'smooth', top: top, block: block, inline: 'nearest' });
    }
}

// Add percent
export function percentageResult(price, percentage, decimal = 0){
    var result = '';

    var percent = percentage ? Number((percentage/100)) : '';
    //console.log(percent);

    var value = price ? Number((price/100)) : '';
    //console.log(value);

    result = value && percent ? ( value + ( Number(percent/100) * value ) ).toFixed(decimal) : '';
    //console.log(result);

    return result;
}



export function bsPopoverTooltip() {
    setTimeout(() => {
        // Arrays to keep track of all tooltips and popovers
        let allTooltips = [];
        let allPopovers = [];

        // Function to hide all tooltips
        function hideAllTooltips() {
            allTooltips.forEach(tooltip => tooltip.hide());
            allTooltips = []; // Clear the array after hiding all tooltips
        }

        // Function to hide all popovers
        function hideAllPopovers() {
            allPopovers.forEach(popover => popover.hide());
            allPopovers = []; // Clear the array after hiding all popovers
        }

        // Hide existing tooltips and popovers
        hideAllTooltips();
        hideAllPopovers();

        // Find all elements with data-bs-toggle
        var toggles = document.querySelectorAll('[data-bs-toggle]');

        toggles.forEach(function(toggle) {
            var toggleType = toggle.getAttribute('data-bs-toggle');
            if (toggleType === 'tooltip') {
                // Initialize tooltip and store the instance
                allTooltips.push(new bootstrap.Tooltip(toggle));
            } else if (toggleType === 'popover') {
                // Initialize popover and store the instance
                allPopovers.push(new bootstrap.Popover(toggle));
            }
        });
    }, 1000);
}



export function initFlatpickr() {
    const elements = document.querySelectorAll('.flatpickr-default');
    elements.forEach(element => {
        const defaultValue = element.value ? element.value : null;

        flatpickr(element, {
            dateFormat: "d/m/Y",
            locale: "pt",
            allowInput: true,
            clear: true,
            minDate: "today",
            defaultDate: defaultValue,
            maxDate: new Date().fp_incr(360)// Set the maximum date to 360 days from today
        });
    });
}

export function initFlatpickrRange() {
    const elements = document.querySelectorAll('.flatpickr-range');
    elements.forEach(element => {
        var getMinDate = element.getAttribute("data-min-date");
        getMinDate = !getMinDate ? 'today' : getMinDate;

        var getMaxDate = element.getAttribute("data-max-date");
        getMaxDate = !getMaxDate ? 'today' : getMaxDate;

        flatpickr(element, {
            dateFormat: "d/m/Y",
            locale: "pt",
            clear: true,
            mode: "range",
            minDate: getMinDate,
            maxDate: getMaxDate
        });
    });
}

export function initFlatpickrRangeMonths(){
    const elements = document.querySelectorAll('.flatpickr-range-month');
    if (elements) {
        elements.forEach(function (element) {
            var getMinDate = element.getAttribute("data-min-date");
            getMinDate = !getMinDate ? 'today' : getMinDate;

            var getMaxDate = element.getAttribute("data-max-date");
            getMaxDate = !getMaxDate ? 'today' : getMaxDate;

            flatpickr(element, {
                locale: 'pt',
                mode: "range",
                allowInput: false,
                static: true,
                altInput: true,
                minDate: getMinDate,
                maxDate: getMaxDate,
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

}

function destroyModal() {
    document.querySelectorAll('.modal .btn-destroy').forEach(function (btnClose) {
        btnClose.addEventListener('click', function () {
            var modalElement = this.closest('.modal');
            if (modalElement) {
                modalElement.remove();
            }
        });
    });
}



// Check the internet connection status and display a toast notification if offline
function checkInternetConnection() {
    function updateConnectionStatus() {
        if (navigator.onLine) {
            //console.log('Online');
        } else {
            //console.log('Offline');
            toastAlert('A conexão foi perdida. Por favor, verifique sua rede de internet.', 'error');
        }
    }

    // Initial check
    updateConnectionStatus();

    // Set up event listeners for online and offline events
    window.addEventListener('online', function () {
        //console.log('Back online');
        toastAlert('A conexão foi reestabelecida.', 'success', 5000);
        updateConnectionStatus();
    });

    window.addEventListener('offline', function () {
        //console.log('Lost connection');
        toastAlert('A conexão foi perdida. Por favor, verifique sua rede de internet.', 'error');
        updateConnectionStatus();
    });

    // Set up an interval to check the connection status periodically
    setInterval(updateConnectionStatus, 10000); // Check every 10 seconds
}

export function maxLengthTextarea() {
    const textareas = document.querySelectorAll('.maxlength'); // Select all textareas with a maxlength attribute
    textareas.forEach(formComponent => {
        const maxLength = formComponent.getAttribute('maxlength');
        const counter = document.createElement('div');
        counter.className = 'counter badge bg-warning-subtle text-warning float-end';
        formComponent.parentNode.insertBefore(counter, formComponent.nextSibling);

        formComponent.addEventListener('input', function () {
            const currentLength = formComponent.value.length;
            counter.textContent = `${currentLength} / ${maxLength}`;
        });

        // Trigger the input event to set the initial counter value
        formComponent.dispatchEvent(new Event('input'));
    });
}

/**
 * Removes the 'was-validated' class from a form when any input changes.
 * @param {string} formSelector - The selector for the form.
 */
export function revalidationOnInput(formSelector = '.needs-validation') {
    if(formSelector.length){
        const form = document.querySelector(formSelector);

        if (!form) {
            console.warn('Form not found:', formSelector);

            return;
        }

        function removeValidationClass() {
            form.classList.remove('was-validated');
        }

        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(function(input) {
            input.addEventListener('input', removeValidationClass);
            input.addEventListener('select', removeValidationClass);
            input.addEventListener('textarea', removeValidationClass);
        });
    }
}

export function wizardFormSteps(totalCompanies = 1){
    var formSteps = document.querySelectorAll(".form-steps");
    if (formSteps){
        Array.from(formSteps).forEach(function (form) {
            checkAllFormCheckInputs();

            // next tab
            if (form.querySelectorAll(".nexttab")){

                Array.from(form.querySelectorAll(".nexttab")).forEach(function (nextButton) {
                    var tabEl = form.querySelectorAll('button[data-bs-toggle="pill"]');

                    Array.from(tabEl).forEach(function (item) {
                        item.addEventListener('show.bs.tab', function (event) {
                            event.target.classList.add('done');
                        });
                    });

                    nextButton.addEventListener("click", function () {

                        form.classList.add('was-validated');

                        var nextTab = nextButton.getAttribute('data-nexttab');

                        document.getElementById(nextTab).removeAttribute('disabled');

                        var inputControl = form.querySelectorAll(".tab-pane.show .form-control");
                        if(inputControl){
                            inputControl.forEach(function(elem){

                                //console.log('nextTab ID: ', nextTab);

                                /*var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
                                if(elem.value.length > 0 && elem.value.match(validRegex)){*/
                                if(elem.value.length > 0){
                                    form.classList.remove('was-validated');

                                    document.getElementById(nextTab).click();
                                }
                            });
                        }

                        var checkedControl = form.querySelectorAll(".tab-pane.show .form-check-input:checked");
                        if(checkedControl){
                            var checkedControllemght = checkedControl.length
                            //console.log('checked count', checkedControllemght);

                            checkedControl.forEach(function(elem){
                                //console.log('totalCompanies:', totalCompanies);
                                //console.log('nextTab ID: ', nextTab);

                                if(checkedControllemght == (parseInt(totalCompanies) * 2)){
                                    form.classList.remove('was-validated');

                                    document.getElementById(nextTab).click();

                                }
                            });
                        }

                        document.getElementById(nextTab).setAttribute('disabled', 'disabled');
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
        });
    });
}

export function toggleTableRows() {
    // Get all the expand/collapse buttons
    var expandCollapseButtons = document.querySelectorAll('.btn-toggle-row-detail');

    if(expandCollapseButtons){
        // Function to close all detail rows
        function closeAllDetailRows() {
            document.querySelectorAll('.details-row').forEach(function(detailRow) {
                detailRow.classList.add('d-none'); // Hide all detail rows
            });
            document.querySelectorAll('.ri-folder-open-line').forEach(function(icon) {
                icon.classList.add('d-none'); // Hide all collapse icons
            });
            document.querySelectorAll('.ri-folder-line').forEach(function(icon) {
                icon.classList.remove('d-none'); // Show all expand icons
            });
        }

        // Add click event listener to each button
        expandCollapseButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var rowId = this.dataset.id;
                var detailsRow = document.querySelector('tr.details-row[data-details-for="' + rowId + '"]');
                var isCurrentlyOpen = !detailsRow.classList.contains('d-none');

                // Close all detail rows first
                closeAllDetailRows();

                // If the clicked row was not already open, toggle it
                if (!isCurrentlyOpen) {
                    detailsRow.classList.remove('d-none'); // Show the clicked detail row

                    // Toggle the icons for the clicked row
                    var iconExpand = this.querySelector('.ri-folder-line');
                    var iconCollapse = this.querySelector('.ri-folder-open-line');
                    iconExpand.classList.add('d-none');
                    iconCollapse.classList.remove('d-none');
                }

                this.blur();
            });
        });
    }
}

// Function to allow unchecking of radio buttons
export function allowUncheckRadioButtons(radioSelector = '.form-check-input') {
    document.addEventListener('click', function(event) {
        // Check if the clicked element is a radio button and if it's part of the selection we want to control
        if (event.target.matches(radioSelector)) {
            var radio = event.target;
            // If the radio button was already checked, uncheck it
            if (radio.dataset.checked) {
                radio.checked = false;
                radio.dataset.checked = ''; // Clear the custom data attribute
            } else {
                // Mark all radios with the same name as unchecked
                var allRadios = document.querySelectorAll('input[type="radio"][name="' + radio.name + '"]');
                allRadios.forEach(function(otherRadio) {
                    otherRadio.dataset.checked = '';
                });
                // Set the clicked one as checked
                radio.dataset.checked = 'true';
            }
        }
    }, true); // Use capturing to ensure we get the event first
}


export function layouRightSide(){
    var layoutRightSideBtn = document.querySelector('.layout-rightside-btn');
    if (layoutRightSideBtn) {
        Array.from(document.querySelectorAll(".layout-rightside-btn")).forEach(function (item) {
            var userProfileSidebar = document.querySelector(".layout-rightside-col");
            item.addEventListener("click", function () {
                if (userProfileSidebar.classList.contains("d-block")) {
                    userProfileSidebar.classList.remove("d-block");
                    userProfileSidebar.classList.add("d-none");
                } else {
                    userProfileSidebar.classList.remove("d-none");
                    userProfileSidebar.classList.add("d-block");
                }
            });
        });
        window.addEventListener("resize", function () {
            var userProfileSidebar = document.querySelector(".layout-rightside-col");
            if (userProfileSidebar) {
                Array.from(document.querySelectorAll(".layout-rightside-btn")).forEach(function () {
                    if (window.outerWidth < 1699 || window.outerWidth > 3440) {
                        userProfileSidebar.classList.remove("d-block");
                    } else if (window.outerWidth > 1699) {
                        userProfileSidebar.classList.add("d-block");
                    }
                });
            }

            var htmlAttr = document.documentElement;
            if (htmlAttr.getAttribute("data-layout") == "semibox") {
                userProfileSidebar.classList.remove("d-block");
                userProfileSidebar.classList.add("d-none");
            }
        });
        var overlay = document.querySelector('.overlay');
        if (overlay) {
            document.querySelector(".overlay").addEventListener("click", function () {
                if (document.querySelector(".layout-rightside-col").classList.contains('d-block') == true) {
                    document.querySelector(".layout-rightside-col").classList.remove("d-block");
                }
            });
        }
    }

    window.addEventListener("load", function () {
        var userProfileSidebar = document.querySelector(".layout-rightside-col");
        if (userProfileSidebar) {
            Array.from(document.querySelectorAll(".layout-rightside-btn")).forEach(function () {
                if (window.outerWidth < 1699 || window.outerWidth > 3440) {
                    userProfileSidebar.classList.remove("d-block");
                } else if (window.outerWidth > 1699) {
                    userProfileSidebar.classList.add("d-block");
                }
            });
        }

        var htmlAttr = document.documentElement

        if (htmlAttr.getAttribute("data-layout") == "semibox") {
            if (window.outerWidth > 1699) {
                userProfileSidebar.classList.remove("d-block");
                userProfileSidebar.classList.add("d-none");
            }
        }
    });
}




// Make the preview URL request
export function makeFormPreviewRequest(idValue, url, target = 'load-preview', param = 'preview=true') {
    if (idValue) {
        var xhrPreview = new XMLHttpRequest();

        //xhrPreview.open('GET', url + '/' + idValue + '&preview=ture', true);
        xhrPreview.open('GET', url + '/' + encodeURIComponent(idValue) + '?' + param, true);
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
                        document.getElementById(target).innerHTML = contentHtml;

                        bsPopoverTooltip();
                    }
                } else {
                    // Handle error
                    toastAlert('Não foi possível carregar a pré-visualização', 'danger', 10000);
                }
            }
        };
        xhrPreview.send();
    }
}



// GLightbox Popup
// https://github.com/biati-digital/glightbox
export function lightbox(){
    var lightbox = GLightbox({
        selector: '.image-popup',
        title: false,
    });
}



export const monthsInPortuguese = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
];

// Call the functions when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', checkInternetConnection);
document.addEventListener('DOMContentLoaded', showButtonWhenInputChange);
