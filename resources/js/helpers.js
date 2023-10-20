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
