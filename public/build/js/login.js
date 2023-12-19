import {
    toastAlert
} from './helpers.js';

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btn-login').addEventListener('click', function(event) {
        event.preventDefault();

        let database = document.getElementById('database').value;
        let email = document.getElementById('username').value;
        let password = document.getElementById('password-input').value;

        if(!email){
            toastAlert('Informe o e-mail', 'danger', 5000);
            return;
        }

        if(!password){
            toastAlert('Informe a senha', 'danger', 5000);
            return;
        }

        // Make an AJAX call to check databases
        if(!database){
            try {
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
                    //console.log(data.databases);
                    //console.log(JSON.stringify(data.databases, null, 2));
                    if (data.databases.length > 1) {
                        // Create an array of database names from the 'data' object
                        const selectElement = document.createElement('select');
                        selectElement.id = 'database';
                        selectElement.name = 'database';
                        selectElement.classList.add('form-control', 'swal-login-form-control', 'form-select', 'w-auto', 'm-3', 'me-auto', 'ms-auto');

                        const defaultOption = document.createElement('option');
                        defaultOption.value = '';
                        defaultOption.textContent = '- Selecione -';
                        selectElement.appendChild(defaultOption);

                        data.databases.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.database;
                            option.textContent = item.customer;
                            selectElement.appendChild(option);
                        });

                        // Show Swal to let user choose database
                        Swal.fire({
                            title: 'Selecione uma Conexão',
                            html: `
                                <p>Você possui acesso a mais de uma conta</p>
                                ${selectElement.outerHTML}
                            `,
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
                            preConfirm: (value) => {
                                if (!value) {
                                    Swal.showValidationMessage('Necessário selecionar uma Conexão');

                                    return false;
                                }
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let selectedElement = document.querySelector('.swal-login-form-control');
                                let selectedIndex = selectedElement.selectedIndex;
                                let selectedOption = selectedElement.options[selectedIndex];
                                let databaseName = selectedOption.value;
                                //console.log(databaseName);

                                if( !databaseName || databaseName === '' ){
                                    toastAlert('Necessário selecionar uma Conexão', 'danger', 5000);

                                    return false;
                                }

                                document.getElementById('database').value = databaseName;

                                setTimeout(function() {
                                    document.getElementById('loginForm').submit();
                                }, 100);
                            }
                        });
                    } else {
                        //console.log(data.databases);

                        if(data.databases[0]){
                            let databaseName = data.databases[0].database;
                            document.getElementById('database').value = databaseName;

                            setTimeout(function() {
                                document.getElementById('loginForm').submit();
                            }, 100);

                        }else{
                            toastAlert('Usuário não localizado em nossa base de dados', 'danger', 10000);
                        }
                     }
                    return;
                });
            } catch (error) {
                console.error('Error:', error);
                toastAlert(error, 'danger', 10000);
            }
        }else{
            toastAlert('Não foi possível estabelecer conexão com a base de dados', 'danger', 10000);

            event.stopPropagation();
        }
    });

});
