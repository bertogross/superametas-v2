import {toastAlert} from './helpers.js';

window.addEventListener('load', function() {
    document.getElementById('btn-login').addEventListener('click', function(event) {
        let database = document.getElementById('database').value;
        let email = document.getElementById('username').value;
        let password = document.getElementById('password-input').value;

        if(!email){
            toastAlert('Informe o e-mail', 'error', 5000);
            return;
        }
        if(!password){
            toastAlert('Informe a senha', 'error', 5000);
            return;
        }

        // Make an AJAX call to check databases
        if(!database){
            event.preventDefault();

            fetch(checkDatabasesURL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.databases.length > 1) {
                    // Show Swal to let user choose database
                    Swal.fire({
                        title: 'Selecione uma Conexão',
                        input: 'select',
                        inputOptions: data.databases,
                        inputPlaceholder: '- Selecione -',
                        html: 'Você possui acesso a mais de uma conta',
                        showCancelButton: true,
                        focusConfirm: false,
                        customClass : {
                            closeButton : 'btn btn-dark',
                            cancelButton : 'btn btn-dark btn-sm ms-1',
                            confirmButton : 'btn btn-theme me-1'
                        },
                        confirmButtonText: 'Prosseguir',
                        cancelButtonText: 'fechar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            const selectElement = document.querySelector('.swal2-select');
                            if (selectElement) {
                                selectElement.classList.add('form-select', 'w-auto', 'm-3');
                                //selectElement.classList.remove('swal2-select');
                            }

                            const confirmElement = document.querySelector('.swal2-confirm');
                            if (confirmElement) {
                                confirmElement.classList.remove('swal2-styled');
                            }

                            const cancelElement = document.querySelector('.swal2-cancel');
                            if (cancelElement) {
                                cancelElement.classList.remove('swal2-styled');
                            }
                        },
                        preConfirm: (value) => {
                            if (!value) {
                                Swal.showValidationMessage('Necessário selecionar uma conexão');
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let databaseName = data.databases[result.value];
                            document.getElementById('database').value = databaseName;

                            setTimeout(function() {
                                document.getElementById('loginForm').submit();
                            }, 500);
                        }
                    });
                } else {
                    let databaseName = data.databases[0];
                    document.getElementById('database').value = databaseName;

                    setTimeout(function() {
                        document.getElementById('loginForm').submit();
                    }, 500);
                }
                return;
            });
        }
    });

});
