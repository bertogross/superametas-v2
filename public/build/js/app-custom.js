import {
    toastAlert,
    lightbox,
    showButtonWhenInputChange
} from './helpers.js';

document.addEventListener('DOMContentLoaded', function() {
    //Overwrite the native app initModeSetting function
    const lightDarkModeBtn = document.getElementById("btn-light-dark-mode");
    if (lightDarkModeBtn) {
        lightDarkModeBtn.addEventListener('click', async function(event) {
            event.preventDefault();

            try {
                // Sleep for X miliseconds
                let ms = 10;
                await new Promise(resolve => setTimeout(resolve, ms));

                const html = document.getElementsByTagName("HTML")[0];

                if(html.hasAttribute("data-bs-theme") && html.getAttribute("data-bs-theme") == "dark"){
                    html.setAttribute("data-bs-theme", "light", "layout-mode-light", html);

                    var dataTheme = 'light';
                }else{
                    html.setAttribute("data-bs-theme", "dark", "layout-mode-dark", html);

                    var dataTheme = 'dark';
                }

                const url = profileChangeLayoutModeURL;
                const response = await fetch(url, {
                    method: 'POST',
                    body: JSON.stringify({ theme: dataTheme }),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (!data.success) {
                    toastAlert(data.message, 'danger');
                }
            } catch (error) {
                toastAlert('Error: ' + error, 'danger');
                console.error('Error:', error);
            }
        });
    }

});

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

// To prevent users from entering HTML tags in various types of input fields
function sanitizeInputValue(inputElement) {
    let sanitizedValue = inputElement.value;

    // Define a regular expression pattern to match unwanted characters
    const unwantedCharsPattern = /[<>'"\\`]/g;

    // Store the original value before sanitization
    const originalValue = sanitizedValue;

    // Replace unwanted characters with an empty string
    sanitizedValue = sanitizedValue.replace(unwantedCharsPattern, '');

    // Update the input field's value with the sanitized text
    inputElement.value = sanitizedValue;

    // Check if any invalid characters were removed
    if (originalValue !== sanitizedValue) {
        setTimeout(() => {

            // Get the characters that were removed
            const removedChars = originalValue.match(unwantedCharsPattern).join('');

            // Invalid characters were removed, show an alert
            toastAlert(`O caractere <span class="text-danger fw-bold fs-14">${removedChars}</span> é inaceitável e foi removido`, 'info', 5000);
        }, 500);
    }
}
function sanitizeInputOnInput(event) {
    const target = event.target;

        // Check if the event target is an input element (e.g., INPUT, TEXTAREA etc)
        if (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.tagName === 'SELECT' || target.tagName === 'option') {
            setTimeout(() => {
                sanitizeInputValue(target);
            }, 100);
        }
}
document.addEventListener('input', sanitizeInputOnInput);
document.addEventListener('blur', sanitizeInputOnInput);
document.addEventListener('change', sanitizeInputOnInput);
document.addEventListener('keyup', sanitizeInputOnInput);


// Prevent right-click context menu.
function preventRightClick(event) {
    event.preventDefault();
}
document.addEventListener("DOMContentLoaded", function () {
    if (document.body.classList.contains("production")) {
        document.addEventListener('contextmenu', preventRightClick);
    }
});


function destroyModal() {
    if(document.querySelectorAll('.modal .btn-destroy').length){
        document.querySelectorAll('.modal .btn-destroy').forEach(function (btnClose) {
            btnClose.addEventListener('click', function () {
                var modalElement = this.closest('.modal');
                if (modalElement) {
                    modalElement.remove();
                }
            });
        });
    }
}

// Alert users when they try to type in an input or textarea element that is marked as readonly
/*
function handleReadonlyInputs() {
    const readonlyInputs = document.querySelectorAll("input[readonly], textarea[readonly]");

    readonlyInputs.forEach(function (input) {
        input.addEventListener("input", function () {
            toastAlert(`O campo <span class="text-danger fw-bold">${input.value}</span> é somente leitura e não pode ser editado`, 'info', 5000);
        });

        input.addEventListener("click", function () {
            toastAlert(`O campo <span class="text-danger fw-bold">${input.value}</span> é somente leitura e não pode ser editado`, 'info', 5000);
        });
    });
}
document.addEventListener('DOMContentLoaded', handleReadonlyInputs);
*/


// Call the functions when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', lightbox);
document.addEventListener('DOMContentLoaded', destroyModal);
document.addEventListener('DOMContentLoaded', checkInternetConnection);
document.addEventListener('DOMContentLoaded', showButtonWhenInputChange);
