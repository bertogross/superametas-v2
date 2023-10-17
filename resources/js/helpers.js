export function ToastAlert(message, type = 'success', duration = 3000) {
    // Template for the toast
    var ToastHtml = `
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div class="toast fade show toast-border-${type} overflow-hidden mt-3" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="ri-checkbox-circle-fill align-middle"></i>
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

    // Inject the toast into the DOM
    document.body.insertAdjacentHTML('beforeend', ToastHtml);

    // Get the toast element
    const toast = document.querySelector('.toast-container .toast');

    // Use Bootstrap's Toast API to show the toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Automatically hide the toast after the specified duration and remove it from the DOM
    setTimeout(() => {
        bsToast.hide();
        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toast.parentElement);
        });
    }, duration);



}
