import {
    toastAlert,
    attachImage
} from './helpers.js';

document.addEventListener('DOMContentLoaded', function() {

    /*
    // Register the plugins
    FilePond.registerPlugin(
        FilePondPluginFileEncode,
        FilePondPluginFileValidateSize,
        FilePondPluginImageExifOrientation,
        FilePondPluginImagePreview
    );

    // Function to attach the FilePond instance to the input element
    function attachFilePondToLogo() {
        const inputElement = document.querySelector('.filepond-input-logo');

        if (inputElement) {
            const pond = FilePond.create(inputElement, {
                labelIdle: 'Arraste e Solte sua imagem ou <span class="filepond--label-action">buscar...</span>',
                imagePreviewHeight: 170,
                imageCropAspectRatio: '1:1',
                imageResizeTargetWidth: 200,
                imageResizeTargetHeight: 200,
                stylePanelLayout: 'compact circle',
                styleLoadIndicatorPosition: 'center bottom',
                styleProgressIndicatorPosition: 'right bottom',
                styleButtonRemoveItemPosition: 'left bottom',
                styleButtonProcessItemPosition: 'right bottom',
                allowImagePreview: true,
                allowRevert: true,
                server: {
                    url: uploadLogoURL,
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    onload: (response) => {
                        try {
                            const res = JSON.parse(response);
                            toastAlert(res.message, res.success ? 'success' : 'error');
                        } catch (e) {
                            toastAlert('Failed to parse server response', 'error');
                        }
                    },
                    onerror: (response) => {
                        try {
                            const res = JSON.parse(response);
                            toastAlert(res.message, 'error');
                        } catch (e) {
                            toastAlert('Failed to parse error response', 'error');
                        }
                    }
                }
            });

            // Set the initial file if it exists
                pond.addFile(inputElement.dataset.defaultFile);

                // Set up FilePond event listeners
                pond.on('processfile', (error, file) => {
                    if (error) {
                        toastAlert('Error during upload: ' + error, 'error');
                    } else {
                        toastAlert('File uploaded: ' + file.filename, 'success');
                    }
                });

                pond.on('error', (error) => {
                    toastAlert('FilePond error: ' + error.description, 'error');
                });


        } else {
            // Handle the case when the input element is not found
            toastAlert('Input element not found!', 'error');
        }
    }
    */

    attachImage("#logo-image-input", "#logo-img", uploadLogoURL);

    // Mask for input phone
    function formatPhoneNumber() {
        const phoneInputs = document.querySelectorAll('.phone-mask');

        phoneInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                if (e.inputType === "deleteContentBackward") {
                    return; // If backspace was pressed, just return
                }

                var target = e.target,
                    value = target.value;

                value = onlyNumbers(value); // Remove non-numeric characters

                // If value is empty after removing non-numeric characters, clear the field
                if (!value) {
                    target.value = '';
                    return;
                }

                value = value.replace(/^(\d{0,2})(\d{0,1})(\d{0,4})(\d{0,4}).*/, '($1) $2 $3-$4'); // Format the input

                target.value = value;
            });
        });
    }

    // Call the functions when the DOM is fully loaded
    formatPhoneNumber();
    //attachFilePondToLogo();
});


