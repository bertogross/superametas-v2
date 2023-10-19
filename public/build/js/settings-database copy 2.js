import { ToastAlert } from './helpers.js';

window.addEventListener('load', function() {

    // A flag to track whether the execution is currently in progress or not.
    let isExecutionInProgress = false;

    // Add an event listener for the 'beforeunload' event.
    // This event is fired when the window, the document and its resources are about to be unloaded.
    window.addEventListener('beforeunload', function (e) {
        if (isExecutionInProgress) {
            const message = 'A execução está em andamento. Tem certeza de que quer sair?';
            e.returnValue = message;

            // Display the confirmation dialog
            return message;
        }
    });


    /**
     * Sends a request to the specified URL and returns the JSON response.
     * @param {string} url - The URL to send the request to.
     * @returns {Promise<Object>} - A promise that resolves with the JSON response.
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
     * @param {string} meantime - The current month and year being processed in "YYYY-mm" format.
     * @param {string} initialMeantime - The initial month and year selected by the user in "YYYY-mm" format.
     * @param {number} completedIterations - The number of completed iterations.
     */
    async function makeRequests(meantime, initialMeantime, completedIterations) {
        // Show synchronization progress and hide the start button
        document.getElementById('synchronization-progress').classList.remove('d-none');
        document.getElementById('btn-start-synchronization').classList.add('d-none');
        document.getElementById('custom-backdrop').style.display = 'block'; // Show the backdrop
        isExecutionInProgress = true;
        document.addEventListener('contextmenu', preventRightClick);

        // Construct the API endpoint URL
        const url = `/api/process-sysmo-api/${meantime}`;

        try {
            const response = await sendRequest(url);

            if (!response || response.success === false) {
                ToastAlert('error', response.message);
                return;
            }

            // Calculate the progress percentage
            const totalMonths = monthDifference(initialMeantime, new Date().toISOString().slice(0, 7));
            const elapsedMonths = monthDifference(initialMeantime, meantime);
            const percent = (elapsedMonths / totalMonths) * 100;

            // Update the progress bar and percentage text
            document.querySelector('.progress-bar').style.width = `${percent.toFixed(0)}%`;
            document.querySelector('.synchronization-percent').innerHTML = `${percent.toFixed(0)}%`;
            document.querySelector('.synchronization-percent-text').innerHTML = `Sincronização em andamento... [${meantime}] <span class="spinner-grow spinner-grow-sm text-theme ms-2"></span>`;

            // Update the progress bar color based on the percentage
            updateProgressBarColor(percent);

            // Prepare for the next iteration
            const nextMeantime = incrementMonth(meantime);
            console.log(`Making next request for ${nextMeantime}`);

            // If there are more months to process, continue. Otherwise, mark as completed.
            //if (nextMeantime <= new Date().toISOString().slice(0, 7)) {
            if (new Date(nextMeantime) <= new Date()) {
                completedIterations++;
                makeRequests(nextMeantime, initialMeantime, completedIterations);
            } else {
                document.querySelector('.synchronization-percent-text').innerHTML = 'Concluído';
                document.querySelector('#synchronization-progress .synchronization-time').innerHTML = '';
                document.querySelector('.progress-bar').style.width = '100%';
                document.getElementById('custom-backdrop').style.display = 'none'; // Hide the backdrop
                isExecutionInProgress = false;
                document.removeEventListener('contextmenu', preventRightClick);
            }
        } catch (error) {
            ToastAlert('error', `Error: ${error.message}`);
            document.getElementById('custom-backdrop').style.display = 'none'; // Hide the backdrop
            isExecutionInProgress = false;
            document.removeEventListener('contextmenu', preventRightClick);
        }
    }

    /**
     * Update the progress bar's color based on the given percentage.
     * @param {number} percent - The current progress percentage.
     */
    function updateProgressBarColor(percent) {
        const progressBar = document.querySelector('.progress-bar');
        progressBar.classList.remove('bg-danger', 'bg-warning', 'bg-info', 'bg-theme');

        if (percent < 30) {
            progressBar.classList.add('bg-danger');
        } else if (percent >= 20 && percent < 50) {
            progressBar.classList.add('bg-warning');
        } else if (percent >= 50 && percent < 100) {
            progressBar.classList.add('bg-info');
        } else if (percent === 100) {
            progressBar.classList.add('bg-theme');
        }

        document.querySelector('#synchronization-progress .synchronization-time').innerHTML = '<span class="blink">' + percent.toFixed(0) + '%</span>';
    }

    /**
     * Increment the month of a given date string in "YYYY-mm" format.
     * @param {string} dateStr - The date string to increment.
     * @returns {string} - The incremented date string.
     */
    function incrementMonth(dateStr) {
        const [year, month] = dateStr.split('-').map(Number);
        if (month === 12) {
            return `${year + 1}-01`;
        } else {
            return `${year}-${String(month + 1).padStart(2, '0')}`;
        }
    }

    /**
     * Calculate the month difference between two dates in "YYYY-mm" format.
     * @param {string} startDate - The start date.
     * @param {string} endDate - The end date.
     * @returns {number} - The month difference.
     */
    function monthDifference(startDate, endDate) {
        const [startYear, startMonth] = startDate.split('-').map(Number);
        const [endYear, endMonth] = endDate.split('-').map(Number);
        return (endYear - startYear) * 12 + endMonth - startMonth;
    }


    // Event listener for the start synchronization button
    document.getElementById('btn-start-synchronization').addEventListener('click', function(e) {
        e.preventDefault();

        // Display initial status messages with delays
        const estimatedElement = document.querySelector('#synchronization-progress .synchronization-time');
        ['Iniciando...', 'Conectando...', 'Recebendo dados...'].forEach((message, index) => {
            setTimeout(() => {
                estimatedElement.innerHTML = `<span class="blink">${message}</span>`;
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
                const fromMeantime = `${year}-${month.padStart(2, '0')}`;
                makeRequests(fromMeantime, fromMeantime, 0);
            }
        });
    });
});
