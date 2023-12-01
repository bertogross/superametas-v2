import {
    toastAlert,
    lightbox
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

    // initialize GLightbox
    lightbox();

});
