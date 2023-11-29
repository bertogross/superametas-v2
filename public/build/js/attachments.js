
import {
    toastAlert,
    sweetAlerts,
    lightbox
} from './helpers.js';

document.addEventListener('DOMContentLoaded', function() {

    /*
    const uploadSurveyPhotoInputs = document.querySelectorAll('.input-upload-photo');
    if(uploadSurveyPhotoInputs){
        uploadSurveyPhotoInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const uploadUrl = uploadPhotoURL;
                    attachPhoto(this, uploadUrl, function(inputFile, data) {
                        const parentForm = inputFile.closest('.responses-data-container');

                        // Update the input value with the attachment ID
                        //parentForm.querySelector('input[name="attachment_id[]"]').value = data.id;

                        // Handle successful upload
                        const galleryWrapper = parentForm.querySelector('.gallery-wrapper');
                        if (galleryWrapper) {
                            const galleryItemHtml = `
                                <div id="attachment-${data.id}" class="element-item col-auto">
                                    <div class="gallery-box card p-0">
                                        <div class="gallery-container">
                                            <a href="/storage/${data.path}" class="image-popup">
                                                <img class="rounded gallery-img" alt="image" height="70" src="/storage/${data.path}">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="position-absolute translate-middle mt-n3">
                                        <div class="avatar-xs">
                                            <button type="button" class="avatar-title bg-light border-0 rounded-circle text-danger cursor-pointer btn-delete-photo" data-attachment-id="${data.id}" title="Deletar Arquivo">
                                                <i class="ri-delete-bin-2-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="attachment_id[]" value="${data.id}">
                                </div>`;
                            galleryWrapper.insertAdjacentHTML('beforeend', galleryItemHtml);

                            lightbox();
                            deletePhotoButtonsListener();
                        }
                    });
                }
            });
        });
    }
    */

    // Attach event listeners for Photos image upload
    function attachPhoto(inputFile, uploadUrl, onSuccess) {
        if (inputFile) {
            const file = inputFile.files[0];
            if (!file.type.startsWith('image/')) {
                console.error('File is not an image.');
                toastAlert('File is not an image.', 'danger', 5000);
                return;
            }

            const reader = new FileReader();

            reader.onload = function(event) {
                const img = new Image();
                img.src = event.target.result;

                img.onload = function() {
                    if (!img.complete || img.naturalWidth === 0) {
                        console.error('Failed to load image.');
                        toastAlert('Failed to load image.', 'danger', 5000);
                        return;
                    }

                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    let targetWidth = img.width;
                    let targetHeight = img.height;

                    // Resize logic
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

                    canvas.toBlob(function(blob) {
                        if (!blob) {
                            console.error('Failed to create blob.');
                            toastAlert('Failed to create blob.', 'danger', 5000);
                            return;
                        }

                        const formData = new FormData();
                        formData.append('file', blob, file.name);

                        fetch(uploadUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('File uploaded successfully');

                                toastAlert(data.message, 'success', 5000);

                                onSuccess(inputFile, data); // Call the callback function

                                // Trigger a click event on the closest update button
                                const closestContainer = document.querySelector(`#attachment-${data.id}`).closest('.responses-data-container');
                                if (closestContainer) {
                                    const updateButton = closestContainer.querySelector('.btn-response-surveyor-update');
                                    if (updateButton) {
                                        setTimeout(() => {
                                            updateButton.click();
                                        }, 3000);
                                    }
                                }


                            } else {
                                console.error('Upload failed:', data.message);

                                toastAlert(data.message, 'danger', 5000);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastAlert('Upload failed: ' + error, 'danger');
                        });
                    }, file.type === 'image/png' ? 'image/png' : 'image/jpeg', file.type === 'image/png' ? 1 : 0.7);
                };

                img.onerror = function() {
                    console.error('Error in loading image.');
                    toastAlert('Error in loading image.', 'danger');
                };
            };

            reader.onerror = function() {
                console.error('Error reading file.');
                toastAlert('Error reading file.', 'danger');
            };

            reader.readAsDataURL(file);
        }
    }

    // Function to handle the deletion of a file
    function deletePhoto(fileId) {
        fetch(deletePhotoURL + '/' + fileId, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {

                // Trigger a click event on the closest update button
                const closestContainer = document.querySelector(`#attachment-${fileId}`).closest('.responses-data-container');
                if (closestContainer) {
                    const updateButton = closestContainer.querySelector('.btn-response-surveyor-update');
                    if (updateButton) {
                        setTimeout(() => {
                            updateButton.click();
                        }, 3000);
                    }
                }

                console.log('File deleted successfully');
                // Remove the element from the UI
                document.querySelector(`#attachment-${fileId}`).remove();

                toastAlert(data.message, 'warning', 5000);
            } else {
                console.error('Failed to delete file:', data.message);

                toastAlert(data.message, 'danger', 5000);
            }
        })
        .catch(error => {
            console.error('Error:', error);

            toastAlert(error, 'danger', 5000);
        });
    }

    // Attach event listeners to all delete buttons
    function deletePhotoButtonsListener() {
        const deletePhotoButtons = document.querySelectorAll('.btn-delete-photo');
        if (deletePhotoButtons) {
            deletePhotoButtons.forEach(button => {
                button.removeEventListener('click', onDeleteClick); // Remove any existing listeners to avoid duplicates
                button.addEventListener('click', onDeleteClick);
            });
        }
    }

    function onDeleteClick(event) {
        event.preventDefault();
        const fileId = this.getAttribute('data-attachment-id');
        if (confirm('Tem certeza de que deseja excluir este arquivo?')) {
            deletePhoto(fileId);
        }
    }

    // Call this function initially to attach listeners to existing delete buttons
    deletePhotoButtonsListener();

    // Your existing upload logic
    const uploadSurveyPhotoInputs = document.querySelectorAll('.input-upload-photo');
    if (uploadSurveyPhotoInputs) {
        uploadSurveyPhotoInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const uploadUrl = uploadPhotoURL;
                    attachPhoto(this, uploadUrl, function(inputFile, data) {
                        const parentForm = inputFile.closest('.responses-data-container');

                        // Handle successful upload
                        const galleryWrapper = parentForm.querySelector('.gallery-wrapper');
                        if (galleryWrapper) {
                            const galleryItemHtml = `
                                <div id="attachment-${data.id}" class="element-item col-auto">
                                    <div class="gallery-box card p-0">
                                        <div class="gallery-container">
                                            <a href="/storage/${data.path}" class="image-popup">
                                                <img class="rounded gallery-img" alt="image" height="70" src="/storage/${data.path}">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="position-absolute translate-middle mt-n3">
                                        <div class="avatar-xs">
                                            <button type="button" class="avatar-title bg-light border-0 rounded-circle text-danger cursor-pointer btn-delete-photo" data-attachment-id="${data.id}" title="Deletar Arquivo">
                                                <i class="ri-delete-bin-2-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="attachment_id[]" value="${data.id}">
                                </div>`;
                            galleryWrapper.insertAdjacentHTML('beforeend', galleryItemHtml);

                            lightbox();
                        }

                        // Call the listener initialization function after appending new HTML
                        deletePhotoButtonsListener();
                    });
                }
            });
        });
    }


});



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
                        .then(data => {
                            if (data.success) {
                                toastAlert(data.message, 'success');
                                if (data.path) {
                                    if(preview){
                                        preview.src = '/storage/' + data.path;
                                    }
                                    if(previewCard){
                                        previewCard.src = '/storage/' + data.path;
                                    }
                                }
                            } else {
                                toastAlert(data.message, 'danger');
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
