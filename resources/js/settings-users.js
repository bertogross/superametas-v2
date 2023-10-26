import {toastAlert} from './helpers.js';

window.addEventListener('load', function() {

    // Load the content for the user modal
    function loadUserSettingsModal(userId = null, userName = '') {
        var xhr = new XMLHttpRequest();
        var url = '/settings/users/modal-form';
        if (userId) {
            url += '/' + userId;
        }
        xhr.open('GET', url, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if(xhr.responseText){
                    document.getElementById('modalContainer').innerHTML = xhr.responseText;

                    var modalElement = document.getElementById('userModal');
                    var modal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    modal.show();

                    if (userId) {
                        document.getElementById("modalUserTitle").innerHTML = userName ? '<span class="text-theme">'+ userName + '</span>' : 'Editar Usuário';
                        document.getElementById("btn-save-user").innerHTML = 'Atualizar';

                        injectScript("/build/js/pages/password-addon.init.js");
                    } else {
                        document.getElementById("modalUserTitle").innerHTML = 'Adicionar Usuário';
                        document.getElementById("btn-save-user").innerHTML = 'Salvar';
                    }

                    attachModalEventListeners();
                    attachImageEventListeners("#member-image-input", "#avatar-img", "/upload/avatar");
                    attachImageEventListeners("#cover-image-input", "#cover-img", "/upload/cover");

                }else{
                    toastAlert('Não foi possível carregar o conteúdo', 'error', 10000);
                }

            } else {
                console.log("Error fetching modal content:", xhr.statusText);
            }
        };
        xhr.send();
    }

    // Event listener for the 'Add User' button
    if(document.getElementById('btn-add-user')){
        document.getElementById('btn-add-user').addEventListener('click', function() {
            loadUserSettingsModal();
        });
    }

    // Event listeners for each 'Edit User' button
    var editButtons = document.querySelectorAll('.btn-edit-user');
    if(editButtons){
        editButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                var userId = this.getAttribute('data-user-id');
                var userName = this.getAttribute('data-user-name');

                loadUserSettingsModal(userId, userName);
            });
        });
    }

    // Function to inject a script into the page
    function injectScript(src) {
        var script = document.createElement('script');
        script.src = src;
        document.body.appendChild(script);
    }



    // Search functionality for the user list
    var searchInput = document.getElementById('searchMemberList');
    searchInput.addEventListener('keyup', function() {
        var searchTerm = this.value.toLowerCase();
        var users = document.querySelectorAll('[data-search-user-id]');

        users.forEach(function(user) {
            var userName = user.getAttribute('data-search-user-name').toLowerCase();
            // var userRole = user.getAttribute('data-search-user-role').toLowerCase();

            //if (userName.includes(searchTerm) || userRole.includes(searchTerm)) {
            if (userName.includes(searchTerm)) {
                user.style.display = ''; // Show the user
            } else {
                user.style.display = 'none'; // Hide the user
            }
        });
    });

    // Attach event listeners for the modal form
    function attachModalEventListeners() {
        // Update/Save user from modal form
        const form = document.getElementById('userForm');
        const btn = document.getElementById('btn-save-user');

        if (btn) {
            btn.addEventListener('click', function(event) {
                event.preventDefault();

                let formData = new FormData(form);

                let url = form.dataset.id ? `/settings/users/update/${form.dataset.id}` : '/settings/users/store';

                fetch(url, {
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
                        toastAlert(response.message, 'success', 10000);
                        setTimeout(() => {
                            location.reload();
                        }, form.dataset.id ? 5000 : 120000);

                        document.getElementById('btn-save-user').remove();
                    } else {
                        toastAlert(response.message, 'danger', 60000);
                    }
                })
                .catch(error => {
                    toastAlert('Error: ' + error, 'danger', 60000);
                    console.error('Error:', error);
                });
            });
        }
    }

    // Attach event listeners for Avatar and Cover image upload
    function attachImageEventListeners(inputSelector, imageSelector, uploadUrl) {
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
                        } else if (imageSelector == '#cover-img') {
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
                                        preview.src = '/storage/' + response.path;
                                        previewCard.src = '/storage/' + response.path;
                                    }
                                } else {
                                    toastAlert(response.message, 'danger');
                                }
                            })
                            .catch(error => {
                                toastAlert('Upload failed: ' + error, 'danger');
                                console.error('Error:', error);
                            });
                        }, 'image/jpeg', 0.7);
                    };
                }, false);

                if (file) {
                    reader.readAsDataURL(file);
                }
            });
        }
    }



    // Filter functionality for switching between list and grid views
    var list = document.querySelectorAll(".team-list");
    if (list) {
        var buttonGroups = document.querySelectorAll('.filter-button');
        if (buttonGroups) {
            Array.from(buttonGroups).forEach(function (btnGroup) {
                btnGroup.addEventListener('click', onButtonGroupClick);
            });
        }
    }

    // This block handles the switch between list and grid views
    function onButtonGroupClick(event) {
        if (event.target.id === 'list-view-button' || event.target.parentElement.id === 'list-view-button') {
            document.getElementById("list-view-button").classList.add("active");
            document.getElementById("grid-view-button").classList.remove("active");
            Array.from(list).forEach(function (el) {
                el.classList.add("list-view-filter");
                el.classList.remove("grid-view-filter");
            });

        } else {
            document.getElementById("grid-view-button").classList.add("active");
            document.getElementById("list-view-button").classList.remove("active");
            Array.from(list).forEach(function (el) {
                el.classList.remove("list-view-filter");
                el.classList.add("grid-view-filter");
            });
        }
    }
    // End Filter functionality

});

