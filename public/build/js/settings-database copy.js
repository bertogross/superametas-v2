import { ToastAlert } from './helpers.js';

window.addEventListener('load', function() {

    /**
     * Sends an XMLHttpRequest to the specified URL.
     * @param {string} url - The URL to send the request to.
     * @returns {Promise} - A promise that resolves with the JSON response.
     */
    async function sendRequest(url) {
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(response.statusText);
        }

        return response.json();
    }

    /**
     * Recursively makes requests to the API and updates the UI accordingly.
     * @param {Date} meantime - The current month and year being processed.
     * @param {Date} initialMeantime - The initial month and year selected by the user.
     * @param {number} completedIterations - The number of completed iterations.
     */
    async function makeRequests(meantime, initialMeantime, completedIterations) {

        document.getElementById('synchronization-progress').classList.remove('d-none');
        document.getElementById('btn-start-synchronization').classList.add('d-none');

        var meantime = !meantime ? new Date().toISOString().slice(0, 7) : meantime;

        const url = `/api/process-sysmo-api/${meantime.toISOString().slice(0, 7)}`;

        try {
            const response = await sendRequest(url);

            if (!response || !response.success === false) {
                ToastAlert('error', response.message);
                return;
            }

            // Calculate the progress percentage
            const currentDate = new Date();
            const totalDuration = currentDate.getTime() - initialMeantime.getTime();
            const currentDuration = meantime.getTime() - initialMeantime.getTime();
            const percent = (currentDuration / totalDuration) * 100;

            // Update the progress bar and percentage text
            document.querySelector('.progress-bar').style.width = `${percent.toFixed(0)}%`;
            document.querySelector('.synchronization-percent').innerHTML = `${percent.toFixed(0)}%`;
            document.querySelector('.synchronization-percent-text').innerHTML = `Sincronização em andamento... [${meantime.toISOString().slice(0, 7)}] <span class="spinner-grow spinner-grow-sm text-theme ms-2"></span>`;


            // Prepare for the next iteration
            meantime.setMonth(meantime.getMonth() + 1);

            if (meantime <= new Date()) {
                completedIterations++;
                makeRequests(meantime, initialMeantime, completedIterations);
            } else {
                // If all iterations completed
                document.querySelector('.synchronization-percent-text').innerHTML = 'Concluído';
                document.querySelector('.progress-bar').style.width = '100%';
            }
        } catch (error) {
            ToastAlert('error', `Error: ${error.message}`);
        }
    }

    document.getElementById('btn-start-synchronization').addEventListener('click', function(e) {
        e.preventDefault();

        // Display initial status messages with delays
        const estimatedElement = document.querySelector('#synchronization-progress .synchronization-time');
        ['Iniciando...', 'Conectando...', 'Recebendo dados...'].forEach((message, index) => {
            setTimeout(() => {
                estimatedElement.innerHTML = `<span class="${index !== 2 ? 'blink' : ''}">${message}</span>`;
            }, index * 10000 + 5000);
        });

        // Prepare year and month options for user selection
        const currentYear = new Date().getFullYear();
        const monthNames = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];

        const yearOptions = Array.from({ length: 6 }, (_, i) => currentYear - 5 + i)
            .map(year => `<option value="${year}" ${year === currentYear ? 'selected' : ''}>${year}</option>`)
            .join('');

        const monthOptions = monthNames.map((month, index) =>
            `<option value="${index + 1}" ${index + 1 === new Date().getMonth() + 1 ? 'selected' : ''}>${month}</option>`)
            .join('');

        // Display the month and year selection dialog using SweetAlert2
        Swal.fire({
            title: 'Defina o Mês e Ano de início',
            html: `
                <select id="month" class="form-select mb-2">
                    ${monthOptions}
                </select>
                <select id="year" class="form-select">
                    ${yearOptions}
                </select>
            `,
            focusConfirm: false,
            buttonsStyling: false,
            showCloseButton: true,
            showCancelButton: true,
            cancelButtonText: 'Voltar',
            confirmButtonText: 'Prosseguir',
            customClass: {
                cancelButton: 'btn btn-sm btn-outline-warning ms-1',
                closeButton: 'btn btn-dark ms-1',
                confirmButton: 'btn btn-theme me-1'
            },
            preConfirm: function() {
                const year = document.getElementById('year').value;
                const month = document.getElementById('month').value;
                const fromMeantime = new Date(year, month - 1);

                makeRequests(fromMeantime, fromMeantime, 0);

             }
        });
    });

});
