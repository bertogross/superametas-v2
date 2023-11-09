
// Toast Notifications
// Display a toast notification using Bootstrap's Toast API with a backdrop
export function toastAlert(message, type = 'success', duration = 0) {
    // Remove existing toast containers
    document.querySelectorAll('.toast-container').forEach(element => element.remove());

    // Define the HTML template for the toast
    const icon = type == 'success' ? 'ri-checkbox-circle-fill text-success' : 'ri-alert-fill text-' + type;
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
    const toast = new bootstrap.Toast(toastElement);
    toast.show();

    // Add event listener to the close button
    const closeButton = document.querySelector('.btn-close');
    closeButton.addEventListener('click', () => {
        document.querySelectorAll('.toast-container').forEach(element => element.remove());
    });

    // If a duration is provided, hide the toast after the duration and then remove it and the backdrop from the DOM
    if (duration > 0) {
        setTimeout(() => {
            toast.hide();
            toastElement.addEventListener('hidden.bs.toast', () => {
                document.querySelectorAll('.toast-container').forEach(element => element.remove());
            });
        }, duration);
    }
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
        input.addEventListener('input', function(e) {
            if (e.inputType === "deleteContentBackward") {
                return; // If backspace was pressed, just return
            }

            var target = e.target;
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
        document.addEventListener('click', function (e) {
            if (e.target.id === 'zoom_in') {
                e.preventDefault();
                zoomPage(10, e.target, zoomTarget);
            } else if (e.target.id === 'zoom_out') {
                e.preventDefault();
                zoomPage(-10, e.target, zoomTarget);
            } else if (e.target.id === 'zoom_reset') {
                e.preventDefault();
                zoomPage(0, e.target, zoomTarget);
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

export function bsPopoverTooltip(){
    var toggles = document.querySelectorAll('[data-bs-toggle]');
    toggles.forEach(function(toggle) {
        var toggleType = toggle.getAttribute('data-bs-toggle');
        if (toggleType === 'tooltip') {
            new bootstrap.Tooltip(toggle);
        } else if (toggleType === 'popover') {
            new bootstrap.Popover(toggle);
        }

    });
}

export function initFlatpickrRange() {
    const elements = document.querySelectorAll('.flatpickr-range');
    elements.forEach(element => {
        flatpickr(element, {
            dateFormat: "d/m/Y",
            locale: "pt",
            clear: true,
            mode: "range"
        });
    });
}

export function initFlatpickr() {
    const elements = document.querySelectorAll('.flatpickr-default');
    elements.forEach(element => {
        flatpickr(element, {
            dateFormat: "d/m/Y",
            locale: "pt",
            allowInput: true,
            clear: true,
            minDate: "today",
            //maxDate: new Date().fp_incr(90)
        });
    });
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
    const textareas = document.querySelectorAll('textarea[maxlength]'); // Select all textareas with a maxlength attribute
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        const counter = document.createElement('div');
        counter.className = 'counter badge bg-warning-subtle text-warning float-end';
        textarea.parentNode.insertBefore(counter, textarea.nextSibling);

        textarea.addEventListener('input', function () {
            const currentLength = textarea.value.length;
            counter.textContent = `${currentLength} / ${maxLength}`;
        });

        // Trigger the input event to set the initial counter value
        textarea.dispatchEvent(new Event('input'));
    });
}

/**
 * Removes the 'was-validated' class from a form when any input changes.
 * @param {string} formSelector - The selector for the form.
 */
export function revalidationOnInput(formSelector = '.needs-validation') {
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
            });
        });
    }
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
                    toastAlert('não foi possível carregar a pré-visualiação', 'danger', 10000);
                }
            }
        };
        xhrPreview.send();
    }
}



// Attach event listeners for Avatar and Cover image upload
export function attachImage(inputSelector, imageSelector, uploadUrl) {
    const inputFile = document.querySelector(inputSelector);

    if (inputFile) {
        inputFile.addEventListener("change", function() {
            const preview = document.querySelector(imageSelector);
            const userID = preview.getAttribute("data-user-id");
            const previewCard = document.querySelector(`${imageSelector}-${userID}`);
            const file = inputFile.files[0];
            const reader = new FileReader();

            reader.addEventListener("load", function() {
                preview.src = reader.result;
                //console.log("Image source:", preview.src);

                const img = new Image();
                img.src = reader.result;
                //console.log("Image source:", img.src);

                img.onload = function() {
                    //console.log("Image loaded with dimensions:", img.width, "x", img.height);

                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    if (imageSelector == '#avatar-img') {
                        canvas.width = 200;
                        canvas.height = 200;
                        //console.log("Canvas dimensions:", canvas.width, "x", canvas.height);

                        const aspectRatio = img.width / img.height;
                        let sourceX, sourceY, sourceWidth, sourceHeight;

                        if (aspectRatio > 1) {
                            sourceWidth = img.height;
                            sourceHeight = img.height;
                            sourceX = (img.width - sourceWidth) / 2;
                            sourceY = 0;
                        } else if (aspectRatio < 1) {
                            sourceWidth = img.width;
                            sourceHeight = img.width;
                            sourceX = 0;
                            sourceY = (img.height - sourceHeight) / 2;
                        } else {
                            sourceWidth = img.width;
                            sourceHeight = img.height;
                            sourceX = 0;
                            sourceY = 0;
                        }
                        //console.log("Source dimensions and positions:", sourceX, sourceY, sourceWidth, sourceHeight);

                        ctx.drawImage(img, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, canvas.width, canvas.height);
                    }else if (imageSelector == '#logo-img') {
                        // Set maximum dimensions for logo
                        const maxLogoWidth = 361;
                        const maxLogoHeight = 80;
                        //console.log("Canvas dimensions:", canvas.width, "x", canvas.height);

                        // Calculate aspect ratio for scaling
                        const aspectRatio = img.width / img.height;

                        // Determine the target dimensions while maintaining the aspect ratio
                        let targetWidth = aspectRatio >= maxLogoWidth / maxLogoHeight ? maxLogoWidth : Math.min(img.width, maxLogoWidth);
                        let targetHeight = aspectRatio < maxLogoWidth / maxLogoHeight ? maxLogoHeight : Math.min(img.height, maxLogoHeight);

                        // Adjust target dimensions if the image is smaller than the max dimensions
                        if (img.width < maxLogoWidth && img.height < maxLogoHeight) {
                            targetWidth = img.width;
                            targetHeight = img.height;
                        }

                        // Set canvas dimensions
                        canvas.width = targetWidth;
                        canvas.height = targetHeight;

                        // Calculate the source dimensions
                        let sourceWidth = img.width;
                        let sourceHeight = img.height;
                        let sourceX = 0;
                        let sourceY = 0;

                        // Draw the image on the canvas
                        ctx.drawImage(img, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, targetWidth, targetHeight);
                    } else {
                        let targetWidth = img.width;
                        let targetHeight = img.height;

                        if (targetWidth > 1920 || targetHeight > 1920) {
                            const aspectRatio = targetWidth / targetHeight;
                            if (targetWidth > targetHeight) {
                                targetWidth = 1920;
                                targetHeight = targetWidth / aspectRatio;
                            } else {
                                targetHeight = 1920;
                                targetWidth = targetHeight * aspectRatio;
                            }
                        }

                        canvas.width = targetWidth;
                        canvas.height = targetHeight;
                        ctx.drawImage(img, 0, 0, targetWidth, targetHeight);
                    }

                    canvas.toBlob(function(blob) {
                        const formData = new FormData();
                        formData.append('file', blob, file.name);
                        formData.append('user_id', userID);

                        //console.log("Blob size:", blob.size);

                        fetch(uploadUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(response => {
                            if (response.success) {
                                toastAlert(response.message, 'success');
                                if (response.path) {
                                    if(preview){
                                        preview.src = '/storage/' + response.path;
                                    }
                                    if(previewCard){
                                        previewCard.src = '/storage/' + response.path;
                                    }
                                }
                            } else {
                                toastAlert(response.message, 'danger');
                            }
                        })
                        .catch(error => {
                            toastAlert('Upload failed: ' + error, 'danger');
                            console.error('Error:', error);
                        });
                    }, file.type === 'image/png' ? 'image/png' : 'image/jpeg', file.type === 'image/png' ? 1 : 0.7);
                };
            }, false);

            if (file) {
                reader.readAsDataURL(file);
            }
        });
    }
}


export const monthsInPortuguese = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
];

// Call the functions when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', checkInternetConnection);
document.addEventListener('DOMContentLoaded', showButtonWhenInputChange);
