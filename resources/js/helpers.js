// Toast Notifications
// -------------------
/**
 * Display a toast notification using Bootstrap's Toast API with a backdrop.
 *
 * @param {string} message - The message to display in the toast.
 * @param {string} [type='success'] - The type of the toast (e.g., 'success', 'error'). Determines the toast's color.
 * @param {number} [duration] - The duration (in milliseconds) for which the toast should be displayed.
 */
export function ToastAlert(message, type = 'success', duration = 0) {
    //document.querySelectorAll('.toast-backdrop').forEach(element => element.remove());
    document.querySelectorAll('.toast-container').forEach(element => element.remove());

    // Define the HTML template for the toast
    const icon = type === 'success' ? 'ri-checkbox-circle-fill text-success' : 'ri-alert-fill text-danger';
    const ToastHtml = `
        <!--<div class="toast-backdrop"></div>-->
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
    const toast = new bootstrap.Toast(toastElement);
    toast.show();

    // Add event listener to the close button
    const closeButton = document.querySelector('.btn-close');
    closeButton.addEventListener('click', () => {
        //document.querySelectorAll('.toast-backdrop').forEach(element => element.remove());
        document.querySelectorAll('.toast-container').forEach(element => element.remove());
    });

    // If a duration is provided, hide the toast after the duration and then remove it and the backdrop from the DOM
    if ( duration > 0) {
        setTimeout(() => {
            toast.hide();
            toastElement.addEventListener('hidden.bs.toast', () => {
                //document.querySelectorAll('.toast-backdrop').forEach(element => element.remove());
                document.querySelectorAll('.toast-container').forEach(element => element.remove());
            });
        }, duration);
    }
}


// Multiple Modals
// -------------------
/**
 * Maintain modal-open when close another modal
 */
export function MultipleModal(){
    setTimeout(function() {
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.addEventListener('show', function() {
                document.body.classList.add('modal-open');
            });

            modal.addEventListener('hidden', function() {
                document.body.classList.remove('modal-open');
            });
        });

        // Multiple modals overlay
        document.addEventListener('show.bs.modal', function(event) {
            var modal = event.target;
            var modals = Array.from(document.querySelectorAll('.modal')).filter(function (modal) {
                return window.getComputedStyle(modal).display !== 'none';
            });
            var zIndex = 1050 + 10 * modals.length;
            modal.style.zIndex = zIndex;

            var backdrops = document.querySelectorAll('.modal-backdrop:not(.modal-stack)');
            backdrops.forEach(function(backdrop) {
                backdrop.style.zIndex = zIndex - 1;
                backdrop.classList.add('modal-stack');
            });
        });

        DestroyModal();
    }, 500);
}


function DestroyModal(){
    document.querySelectorAll('.modal .btn-destroy').forEach(function(btnClose) {
        btnClose.addEventListener('click', function() {
            var modalElement = this.closest('.modal');
            if (modalElement) {
                modalElement.remove();
            }
        });
    });
}


// Internet Connection Status
// --------------------------
/**
 * Check the internet connection status and display a toast notification if offline.
 */
function checkInternetConnection() {
    function updateConnectionStatus() {
        if (navigator.onLine) {
            //console.log('Online');
        } else {
            //console.log('Offline');
            ToastAlert('A conexão foi perdida. Por favor, verifique sua rede de internet.', 'error');
        }
    }

    // Initial check
    updateConnectionStatus();

    // Set up event listeners for online and offline events
    window.addEventListener('online', function() {
        //console.log('Back online');
        ToastAlert('A conexão foi reestabelecida.', 'success', 5000);
        updateConnectionStatus();
    });

    window.addEventListener('offline', function() {
        //console.log('Lost connection');
        ToastAlert('A conexão foi perdida. Por favor, verifique sua rede de internet.', 'error');
        updateConnectionStatus();
    });

    // Set up an interval to check the connection status periodically
    setInterval(updateConnectionStatus, 10000); // Check every 10 seconds
}

// Initialize the internet connection checker
checkInternetConnection();


export function formatNumberInput() {
    const inputs = document.querySelectorAll('.format-numbers');

    inputs.forEach(input => {
        input.addEventListener('input', function (e) {
            // Remove non-digit characters
            let value = this.value.replace(/[^\d]/g, '');

            // Convert to number
            value = parseInt(value, 10);

            // If the value is not a number, set it to 0
            if (isNaN(value)) {
                value = 0;
            }

            // Format as currency with dot as thousands separator
            const formatter = new Intl.NumberFormat('pt-BR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            });
            this.value = formatter.format(value);
        });

        // Trigger the input event to format existing values
        input.dispatchEvent(new Event('input'));
    });
}


export function helperSumValues(from, to, decimal = 0) {
    document.addEventListener('DOMContentLoaded', () => {
        const inputs = document.querySelectorAll(from);
        const resultDiv = document.getElementById(to);

        if (!resultDiv) {
            console.error(`Element with id "${to}" not found`);
            return;
        }

        function updateSum() {
            let sum = 0;
            inputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                sum += value;
            });
            resultDiv.textContent = sum.toFixed(decimal);
        }

        inputs.forEach(input => {
            input.addEventListener('input', updateSum);
        });

        updateSum();
    });
}


/*
export function helperSumValues(from, to, decimal = 0) {

  function formatCurrencyInput(number, decimalPlaces = 0, prefix = 'R$ ', thousandsSeparator = '.', centsSeparator = ',') {
    let numberString = parseFloat(number).toFixed(decimalPlaces).toString();
    let parts = numberString.split('.');
    let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);
    let decimalPart = parts[1] ? centsSeparator + parts[1] : '';
    return prefix + integerPart + decimalPart;
  }

  function sumFieldValues() {
    document.querySelectorAll('.table').forEach(table => {
      let sum = Array.from(table.querySelectorAll(from))
        .map(field => parseInt(field.value.replace(/\D/g, '')) || 0)
        .reduce((acc, val) => acc + val, 0);
      table.querySelector(to).textContent = formatCurrencyInput(sum, decimal);
    });
  }

  document.querySelectorAll(from).forEach(field => {
    field.addEventListener('input', sumFieldValues);
  });

  sumFieldValues();
}
*/






