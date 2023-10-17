import {ToastAlert} from './helpers.js';

document.addEventListener("DOMContentLoaded", function() {

    function loadModalContent(userId = null, userName = '') {
        var xhr = new XMLHttpRequest();
        var url = '/get-user-modal-form';
        if (userId) {
            url += '/' + userId;
        }
        xhr.open('GET', url, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById('modalContainer').innerHTML = xhr.responseText;

                var modalElement = document.getElementById('userModal');
                var modal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false  // Prevent closing the modal with the escape key
                });
                modal.show();

                if (userId) {
                    document.getElementById("modalUserTitle").innerHTML = userName ? '<span class="text-theme">'+ userName + '</span>' : 'Editar Usuário';
                    document.getElementById("btn-save-user").innerHTML = 'Atualizar Usuário';

                    injectScript("/build/js/pages/password-addon.init.js");
                } else {
                    document.getElementById("modalUserTitle").innerHTML = 'Adicionar Usuário';
                    document.getElementById("btn-save-user").innerHTML = 'Salvar Usuário';
                }

                attachModalEventListeners();  // Attach the event listeners after content is loaded

            } else {
                console.log("Error fetching modal content:", xhr.statusText);
            }
        };
        xhr.send();
    }

    document.getElementById('btn-add-user').addEventListener('click', function() {
        loadModalContent();
    });

    var editButtons = document.querySelectorAll('.btn-edit-user');
    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var userId = this.getAttribute('data-user-id');
            var userName = this.getAttribute('data-user-name');
            loadModalContent(userId, userName);
        });
    });

    function injectScript(src) {
        var script = document.createElement('script');
        script.src = src;
        document.body.appendChild(script);
    }



    // Search functionality
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


    function attachModalEventListeners() {
        // Update/Save user from modal form
        const form = document.getElementById('memberlist-form');
        const btn = document.getElementById('btn-save-user');

        if (btn) {
            btn.addEventListener('click', function(event) {
                event.preventDefault();

                //console.log("Button clicked!");

                // form.dataset.id get value from <form data-id
                let formData = new FormData(form);
                //formData.append('_method', 'PUT');

                let url = form.dataset.id ? `/settings-users/update/${form.dataset.id}` : '/settings-users/store';

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
                        ToastAlert(response.message, 'success', 10000);
                        setTimeout(() => {
                            location.reload();
                        }, form.dataset.id ? 5000 : 120000);

                        document.getElementById('btn-save-user').remove();
                    } else {
                        ToastAlert(response.message, 'danger', 60000);
                    }
                })
                .catch(error => {
                    ToastAlert('Error: ' + error, 'danger', 60000);
                    console.error('Error:', error);
                });
            });
        }
    }


    // avatar image
    if( document.querySelector("#member-image-input") ){
        document.querySelector("#member-image-input").addEventListener("change", function () {
            var preview = document.querySelector("#member-img");
            var file = document.querySelector("#member-image-input").files[0];
            var reader = new FileReader();
            reader.addEventListener("load", function () {
                preview.src = reader.result;
            }, false);
            if (file) {
                reader.readAsDataURL(file);
            }
        });
    }

    // cover image
    if( document.querySelector("#cover-image-input") ){
        document.querySelector("#cover-image-input").addEventListener("change", function () {
            var preview = document.querySelector("#cover-img");
            var file = document.querySelector("#cover-image-input").files[0];
            var reader = new FileReader();
            reader.addEventListener("load", function () {
                preview.src = reader.result;
            }, false);
            if (file) {
                reader.readAsDataURL(file);
            }
        });
    }



    //Fiter Js
    var list = document.querySelectorAll(".team-list");
    if (list) {
        var buttonGroups = document.querySelectorAll('.filter-button');
        if (buttonGroups) {
            Array.from(buttonGroups).forEach(function (btnGroup) {
                btnGroup.addEventListener('click', onButtonGroupClick);
            });
        }
    }

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


});

