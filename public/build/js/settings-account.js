import {ToastAlert} from './helpers.js';

window.addEventListener('load', function() {
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
                    url: '/upload/logo',
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }
            });

            // Set the initial file if it exists
            if (inputElement.dataset.defaultFile) {
                pond.addFile(inputElement.dataset.defaultFile);
            }
        } else {
            console.error('Input element not found!');
            ToastAlert('Input element not found!', 'error');
        }
    }
    // Start the FilePond instance
    attachFilePondToLogo();


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

                value = value.replace(/\D/g, ''); // Remove non-numeric characters

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

    // Start the formatPhoneNumber instance
    formatPhoneNumber();

});
