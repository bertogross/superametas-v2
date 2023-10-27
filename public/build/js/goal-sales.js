import {
    toastAlert,
    multipleModal,
    formatNumberInput,
    sumInputNumbers,
    toggleZoomInOut,
    setSessionStorage,
    getSessionStorage,
    showButtonWhenInputChange,
    goTo,
    onlyNumbers,
    bsPopoverTooltip,
    formatNumber,
    percentageResult,
    getChartColorsArray
} from './helpers.js';

window.addEventListener('load', function () {

    /**
     * Toggle the display of the custom meantime input field based on the selected option in the meantime select dropdown.
     */
    function toggleCustomMeantimeInput() {
        const meantimeSelect = document.querySelector('select[name="meantime"]');
        const customMeantimeDiv = document.querySelector('.custom_meantime_is_selected');
        const customMeantimeInput = document.querySelector('.custom_meantime_is_selected input');

        const selectedOption = meantimeSelect.value;
        if (selectedOption === 'custom') {
            customMeantimeDiv.style.display = 'block';
        } else {
            customMeantimeDiv.style.display = 'none';

            if (customMeantimeInput) {
                customMeantimeInput.value = '';
            }
        }
        meantimeSelect.addEventListener('change', toggleCustomMeantimeInput);
    }
    toggleCustomMeantimeInput();

    /**
     * Initialize flatpickr with specific options for elements with the class 'flatpickr-range-month'.
     */
    const flatpickrRangeMonthElements = document.querySelectorAll('.flatpickr-range-month');

    if (flatpickrRangeMonthElements) {
        flatpickrRangeMonthElements.forEach(function (element) {
            flatpickr(element, {
                locale: 'pt',
                mode: "range",
                allowInput: false,
                static: true,
                altInput: true,
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "Y-m",
                        altFormat: "F/Y",
                        theme: "dark"
                    })
                ]
            });
        });
    }



    /**
     * Load the content for the Goal Sales Settings
     */
    async function loadGoalSalesSettingsModal() {
        try {
            const response = await fetch('/goal-sales/settings', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const content = await response.text();
            document.getElementById('modalContainer').innerHTML = content;

            const modalElement = document.getElementById('goalSalesSettingsModal');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false,
            });
            modal.show();

            attachModalEventListeners();
        } catch (error) {
            console.error('Error fetching modal content:', error);
            toastAlert('Não foi possível carregar o conteúdo', 'error', 10000);
        }
    }

    // Event listener for the 'btn-goal-sales-settings' button
    if(document.getElementById('btn-goal-sales-settings')){
        document.getElementById('btn-goal-sales-settings').addEventListener('click', function(event) {
            event.preventDefault();

            loadGoalSalesSettingsModal();
        });
    }

    /**
     * Load the content for the Goal Sales Edit Modal
     */
    async function loadGoalSalesEditModal(meantime, companyId, companyName, purpose) {
        try {
            let url = `/goal-sales/form/${meantime}/${companyId}/${purpose}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const content = await response.text();
            document.getElementById('modalContainer').insertAdjacentHTML('beforeend', content);

            const modalElement = document.getElementById('goalSalesEditModal');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false,
            });
            modal.show();

            const modalTitle = purpose === 'update' ? 'Editar' : 'Adicionar';
            document.querySelector("#goalSalesEditModal .modal-title").innerHTML = `<u>${modalTitle}</u> Meta de Vendas :: <span class="text-theme">${companyName}</span>`;

            const btnText = purpose === 'update' ? 'Atualizar' : 'Salvar';
            document.querySelector("#goalSalesEditModal #btn-goal-sales-update").innerHTML = btnText;

            //sumInputNumbers('.o-sum-fields-previous-year', '.sum-result-previous-year');
            //sumInputNumbers('.o-sum-fields-previous-month', '.sum-result-previous-month');
            sumInputNumbers('.o-sum-fields-current', '.sum-result-current');

            formatNumberInput();

            showButtonWhenInputChange();

            multipleModal();

            attachGoalSalesUpdateListeners(meantime, companyId);

        } catch (error) {
            console.error('Error fetching modal content:', error);
            toastAlert('Não foi possível carregar o conteúdo', 'error', 10000);
        }
    }

    // Event listeners for each 'Edit Goal Sales' button
    function attachModalEventListeners(){
        var editButtons = document.querySelectorAll('.btn-goal-sales-edit');
        if(editButtons){
            editButtons.forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();

                    const meantime = this.getAttribute("data-meantime");
                    const companyId = this.getAttribute("data-company-id");
                    const companyName = this.getAttribute("data-company-name");
                    const purpose = this.getAttribute("data-purpose");

                    loadGoalSalesEditModal(meantime, companyId, companyName, purpose);
                });
            });
        }
    }

    // Event listeners for 'Update' button
    function attachGoalSalesUpdateListeners(meantime, companyId) {

        // store/update goalSalesForm
        document.getElementById('btn-goal-sales-update').addEventListener('click', async function(event) {
            event.preventDefault();

            const form = document.getElementById('goalSalesForm');

            if (!form.checkValidity()) {
                event.stopPropagation();
                form.classList.add('was-validated');

                toastAlert('Preencha os campos obrigatórios', 'danger', 5000);

                return;
            }

            let formData = new FormData(form);

            let url = `/goal-sales/post/${meantime}/${companyId}`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Close current modal
                    document.getElementById('btn-goal-sales-update').closest('.modal').querySelector('.btn-close').click();

                    // Change primary modal tr/button
                    var tr = document.querySelector('#goalSalesSettingsModal tr[data-meantime="'+meantime+'"][data-company="'+companyId+'"]');
                    var button = tr.querySelector('button');
                        button.setAttribute('data-purpose', 'update');
                        button.setAttribute('title', 'Editar Meta');
                        button.classList.remove('btn-outline-theme');
                        button.classList.add('btn-theme');
                        button.innerHTML = 'Editar';

                    tr.querySelector('.meantime').classList.add('text-theme');
                    tr.classList.add('blink');
                    setTimeout(function () {
                        tr.classList.remove('blink');
                    }, 10000);

                    toastAlert(data.message, 'success', 10000);
                } else {
                    toastAlert(data.message, 'danger', 60000);
                }
            } catch (error) {
                toastAlert('Error: ' + error, 'danger', 60000);
                console.error('Error:', error);
            }
        });
    }

    // Check if the element with class 'analytic-mode' exists
    if (document.querySelector('.analytic-mode')) {
        document.querySelector('.analytic-mode').addEventListener('click', function () {

            // Send AJAX request to toggle analytics mode in the database
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/goal-sales/analytic-mode', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    var response = JSON.parse(xhr.responseText);

                    setSessionStorage('analytic-mode', response.analyticMode);

                    setSessionStorage('slide-mode', false)

                    setTimeout(function () {
                        location.reload(true);
                    }, 300);
                }
            };
            xhr.send('_token=' + encodeURIComponent(document.querySelector('meta[name="csrf-token"]').content));
        });
    }

    // Check if the element with class 'slide-mode' exists
    if (document.querySelector('.slide-mode')) {
        document.querySelector('.slide-mode').addEventListener('click', function () {

            // Send AJAX request to toggle analytics mode in the database
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/goal-sales/slide-mode', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    var response = JSON.parse(xhr.responseText);

                    setSessionStorage('slide-mode', response.slideMode);

                    setSessionStorage('analytic-mode', false);

                    setTimeout(function () {
                        location.reload(true);
                    }, 300);
                }
            };
            xhr.send('_token=' + encodeURIComponent(document.querySelector('meta[name="csrf-token"]').content));
        });
    }

    // Check if the element with ID 'restore-session' exists
    if (document.querySelector('#restore-session')) {
        document.querySelector('#restore-session').addEventListener('click', function (event) {
            event.preventDefault();

            setSessionStorage('filter-toggle', false);

            // Send AJAX request to remove analytics and slide mode from database
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/goal-sales/default-mode', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    setSessionStorage('slide-mode', false);

                    setSessionStorage('analytic-mode', false);

                    setTimeout(function () {
                        location.reload(true);
                    }, 300);
                }
            };
            xhr.send('_token=' + encodeURIComponent(document.querySelector('meta[name="csrf-token"]').content));
        });
    }

    // Check if the element with class 'filter-toggle' exists
    if (document.querySelector('.filter-toggle')) {
        document.addEventListener('click', function (event) {
            if (event.target.classList.contains('filter-toggle')) {
                var toggleFilter = event.target;

                // Remove focus from the clicked button
                toggleFilter.blur();

                // Check if the toggle button is checked
                if (toggleFilter.checked) {
                    document.getElementById('filter').style.display = 'block'; // Show the filter

                    setSessionStorage('filter-toggle', false); // Remove the session

                    toggleFilter.checked = true; // Set the toggle button to checked

                    document.querySelector('label[for="filter-toggle"] span').textContent = 'Filtro'; // Change the label text

                } else {
                    document.getElementById('filter').style.display = 'none'; // Hide the filter

                    setSessionStorage('filter-toggle'); // Set the session

                    toggleFilter.checked = false; // Set the toggle button to unchecked

                    document.querySelector('label[for="filter-toggle"] span').textContent = 'Exibir Filtro'; // Change the label text
                }

            }
        });

        // Set the initial state of the filter based on the session value
        setTimeout(function () {
            if (getSessionStorage('filter-toggle')) {
                document.getElementById('filter').style.display = 'none'; // Hide the filter

                document.querySelector('label[for="filter-toggle"] span').textContent = 'Exibir Filtro'; // Change the label text

                document.querySelector('input.filter-toggle').checked = false; // Set the toggle button to unchecked
            } else {
                document.getElementById('filter').style.display = 'block'; // Show the filter

                document.querySelector('label[for="filter-toggle"] span').textContent = 'Filtro'; // Change the label text

                document.querySelector('input.filter-toggle').checked = true; // Set the toggle button to checked
            }
        }, 300);
    }

    /**
     * Initialize flatpickr
     */
    toggleZoomInOut();



    // Check if there are any elements with the class 'goal-chart' inside the element with class 'listing-chart'
    if (document.querySelectorAll('.listing-chart .goal-chart')) {
        // Loop through each element with the class 'goal-chart' inside the element with class 'listing-chart'
        document.querySelectorAll('.listing-chart .goal-chart').forEach(function (element) {
            // Get attributes from the element
            var chartId = element.getAttribute('id');
            var chartStyle = element.getAttribute('data-style');
            var companyCount = element.getAttribute('data-company-count');
            var departmentName = element.getAttribute('data-department-name');

            // Parse percent values or default to 0
            var chartPercent = parseInt(element.getAttribute('data-percent')) || 0;
            var chartPercentAccrued = parseInt(element.getAttribute('data-percent-from-metric')) || 0;

            // Define emoji icons
            var iconSleep = App.url + 'build/images/svg/sleep.png';
            var iconCrying = App.url + 'build/images/svg/crying.png';
            var iconSad = App.url + 'build/images/svg/sad.png';
            var iconWow = App.url + 'build/images/svg/wow.png';
            var iconSmile = App.url + 'build/images/svg/smile.png';
            var iconHappy = App.url + 'build/images/svg/happy.png';
            var iconBoss = App.url + 'build/images/svg/boss.png';
            var iconCongratulations = App.url + 'build/images/svg/congratulations.png';

            // Define chart dimensions and styles based on chartStyle attribute
            var chartWidth, chartHeight, imageWH, vOffsetY, cFontSize;
            switch (chartStyle) {
                case 'general':
                case 'global':
                    chartWidth = 160;
                    chartHeight = 160;
                    imageWH = 90;
                    vOffsetY = 60;
                    cFontSize = '18px';
                    break;
                case 'small':
                    chartWidth = 55;
                    chartHeight = 55;
                    imageWH = 35;
                    vOffsetY = 28;
                    cFontSize = '12px';
                    break;
                case 'medium':
                    chartWidth = 70;
                    chartHeight = 70;
                    imageWH = 50;
                    vOffsetY = 35;
                    cFontSize = '14px';
                    break;
                default:
                    chartWidth = 130;
                    chartHeight = 130;
                    imageWH = 72;
                    vOffsetY = 51;
                    cFontSize = '16px';
                    break;
            }

            // Initialize hollowIcon
            var hollowIcon = iconSleep;

            // Define gradient colors based on chartPercent
            var gradientFromColor = '#87DF01';
            var gradientToColor = '#FF4E1E';
            if (chartPercent < 25 || !chartPercent) {
                gradientFromColor = '#262a2f';
                gradientToColor = '#FF4E1E';
            } else if (chartPercent >= 25 && chartPercent < 50) {
                gradientFromColor = '#262a2f';
                gradientToColor = '#FF4E1E';
            } else if (chartPercent >= 50 && chartPercent < 90) {
                gradientFromColor = '#262a2f';
                gradientToColor = '#FF9101';
            } else if (chartPercent >= 90 && chartPercent < 100) {
                gradientFromColor = '#262a2f';
                gradientToColor = '#FCD828';
            } else if (chartPercent >= 100 && chartPercent < 125) {
                gradientFromColor = '#262a2f';
                gradientToColor = '#87DF01';
            } else if (chartPercent >= 125 && chartPercent < 150) {
                gradientFromColor = '#87DF01';
                gradientToColor = '#87DF01';
            } else if (chartPercent >= 150 && chartPercent < 175) {
                gradientFromColor = '#87DF01';
                gradientToColor = '#87DF01';
            } else if (chartPercent >= 175) {
                gradientFromColor = '#87DF01';
                gradientToColor = '#87DF01';
            }

            // Define hollowIcon based on chartPercentAccrued
            if (chartPercentAccrued < 25 || !chartPercentAccrued) {
                hollowIcon = iconSleep;
            } else if (chartPercentAccrued >= 25 && chartPercentAccrued < 50) {
                hollowIcon = iconCrying;
            } else if (chartPercentAccrued >= 50 && chartPercentAccrued < 90) {
                hollowIcon = iconSad;
            } else if (chartPercentAccrued >= 90 && chartPercentAccrued < 100) {
                hollowIcon = iconSmile;
            } else if (chartPercentAccrued >= 100 && chartPercentAccrued < 125) {
                hollowIcon = iconWow;
            } else if (chartPercentAccrued >= 125 && chartPercentAccrued < 150) {
                hollowIcon = iconHappy;
            } else if (chartPercentAccrued >= 150 && chartPercentAccrued < 175) {
                hollowIcon = iconBoss;
            } else if (chartPercentAccrued >= 175) {
                hollowIcon = iconCongratulations;
            }

            // Define chart options
            var options = {
                series: [chartPercent],
                colors: [gradientFromColor, gradientFromColor],
                chart: {
                    width: chartWidth,
                    height: chartHeight,
                    type: 'radialBar',
                    toolbar: {
                        show: false
                    },
                    events: {
                        mounted: function (chart) {
                            chart.windowResizeHandler();
                        }
                    }
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -135,
                        endAngle: 135,
                        hollow: {
                            margin: 0,
                            size: '60%',
                            image: hollowIcon,
                            imageWidth: imageWH,
                            imageHeight: imageWH,
                            imageClipped: false
                        },
                        track: {
                            background: '#32383E',
                            strokeWidth: '98%',
                            margin: 2,
                        },
                        dataLabels: {
                            show: true,
                            name: {
                                show: false,
                                offsetY: 40,
                                color: '#ced4da',
                                fontSize: '15px'
                            },
                            value: {
                                show: true,
                                formatter: function (val) {
                                    return parseInt(val) + '%';
                                },
                                color: '#ced4da',
                                fontSize: cFontSize,
                                offsetY: vOffsetY,
                            }
                        }
                    }
                },
                grid: {
                    padding: {
                        top: -10,
                        right: -10,
                        bottom: -10,
                        left: -10
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'vertical',
                        gradientToColors: [gradientToColor],
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100]
                    }
                },
                stroke: {
                    lineCap: 'butt'
                }
            };

            // Create and render the chart
            var EmoticonChart = new ApexCharts(document.querySelector("#" + chartId), options);
            setTimeout(function () {
                EmoticonChart.render();
            }, 10);
        });
    }


    var chartElement = document.getElementById('goal-sales-chart-area');
    if (chartElement) {
        var chartID = chartElement.id;
        var chartMin = 0;
        var chartMax = parseInt(chartElement.getAttribute('data-max'));
        var chartTick = parseInt(chartElement.getAttribute('data-tick'));
        var chartFilename = chartElement.getAttribute('data-meantime');
        var chartMeantime = chartElement.getAttribute('data-meantime') ? chartElement.getAttribute('data-meantime').split(',') : '';

        var chartGoal = chartElement.getAttribute('data-goal')
        ? chartElement.getAttribute('data-goal').split(',').map(function(num) {
            return isNaN(Number(num)) ? 0 : Number(num);
        })
        : [];

        var chartSale = chartElement.getAttribute('data-sale')
        ? chartElement.getAttribute('data-sale').split(',').map(function(num) {
            return isNaN(Number(num)) ? 0 : Number(num);
        })
        : [];

        var allValues = chartGoal.concat(chartSale);
        var min = Math.min(...allValues);
        var max = Math.max(...allValues);
        var average = (min + max) / 2;
        var interval = (max - min) / 5;

        var yaxisLabels = [];
        for (var i = 0; i <= 5; i++) {
            yaxisLabels.push(min + (interval * i));
        }

        var options = {
            series: [
                {
                    name: 'Vendas',
                    data: chartSale
                }, {
                    name: 'Meta',
                    data: chartGoal
                }
            ],
            chart: {
                height: 600,
                type: 'area',//bar
                toolbar: {
                    show: true,
                    offsetX: 0,
                    offsetY: -33,
                    tools: {
                        download: false,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false,
                        customIcons: []
                    },
                    export: {
                        csv: {
                            filename: chartFilename,
                            columnDelimiter: ',',
                            headerCategory: 'Período',
                            headerValue: 'value',
                            dateFormatter: function (timestamp) {
                                return new Date(timestamp).toDateString();
                            }
                        },
                        svg: {
                            filename: chartFilename,
                        },
                        png: {
                            filename: chartFilename,
                        }
                    },
                    autoSelected: 'zoom'
                }
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: 'smooth',
                width: 2,
            },
            xaxis: {
                categories: chartMeantime
            },
            yaxis: {
                labels: {
                    show: true,
                    //maxLabels: 5, // This will limit the number of y-axis labels (in this case, limite of months)
                    formatter: function (value) {
                        var brFormat = new Intl.NumberFormat(undefined, {
                            style: 'currency',
                            currency: 'BRL',
                            maximumFractionDigits: 0,
                            minimumFractionDigits: 0,
                        }).format(value);

                        return brFormat;
                    }
                },
                tickAmount: chartTick,
                min: chartMin,
                max: max,
                tickAmount: 5
            },
            colors: getChartColorsArray(chartID),
            fill: {
                opacity: 0.06,
                colors: getChartColorsArray(chartID),
                type: 'solid'
            }
        };
        var areaChart = new ApexCharts(document.getElementById(chartID), options);
        areaChart.render();

    }


    showButtonWhenInputChange();


    document.addEventListener('click', function (event) {
        // Check if the clicked element has the ID 'btn-ipca-self-fill'
        if (event.target && event.target.id === 'btn-ipca-self-fill') {
            event.preventDefault();

            const button = event.target;

            // Get the from and to selectors
            const from = button.getAttribute('data-from');
            const to = button.getAttribute('data-to');

            // Remove focus from the button
            button.blur();

            // Get the original title of the button
            var buttonSwalText = button.getAttribute('data-swal-title');

            // Get the previous meantime
            var previousMeantime = button.getAttribute('data-previous-meantime');

            // Get the IPCA percentage value
            var ipcaPercentValue = button.getAttribute('data-ipca-value');

            // Extract the percentage value from the IPCA percentage value
            var inputValue = formatNumber(ipcaPercentValue, 2);

            // Show the Swal modal
            Swal.fire({
                title: 'Variação IPCA',
                html: '<div class="text-center small mb-1">Aplique o valor proposto ou ajuste ao percentual desejado</div><div class="small fs-11 mt-2 text-start">' + buttonSwalText + '</div>',
                input: 'text',
                inputValue: inputValue,
                inputValidator: (value) => {
                    if (!value || value <= 0) {
                        toastAlert('Necessário informar o percentual', 'error', 10000);

                        return 'Necessário informar o percentual'
                    }
                },
                focusConfirm: false,
                //allowEscapeKey: false,
                //allowEnterKey: false,
                //stopKeydownPropagation: true,
                //inputAutoTrim: true,
                allowOutsideClick: false,
                width: '397px',
                buttonsStyling: false,
                showCloseButton: true,
                showCancelButton: false,
                cancelButtonText: 'Voltar',
                showConfirmButton: true,
                confirmButtonText: 'Aplicar',
                inputAutoFocus: true,
                inputAttributes: {
                    minlength: 1,
                    maxlength: 5
                },
                customClass: {
                    closeButton: 'btn btn-dark ms-1',
                    cancelButton: 'btn btn-sm btn-outline-danger ms-1',
                    confirmButton: 'btn btn-theme ms-1'
                },
                didOpen: function () {
                    setTimeout(function () {
                        showButtonWhenInputChange();

                        formatNumberInput('.swal2-input', 2);
                    }, 100);
                }
            }).then(result => {
                // Check if the confirm button was clicked
                if (result.isConfirmed) {
                    //console.log(result.value);

                    // Get the new IPCA percentage value
                    var newPercentage = onlyNumbers(result.value);
                    console.log('newPercentage' + newPercentage);

                    // Initialize the sum of prices
                    var sumPrice = 0;

                    if (!newPercentage || newPercentage <= 0) {
                        toastAlert('Necessário informar o percentual', 'error', 10000);

                        return;
                    }
                    setTimeout(function () {

                        // Get all the elements that match the 'to' selector
                        var toElements = document.querySelectorAll(to);

                        // Loop through each element
                        toElements.forEach(function (element) {
                            // Get the price element and its value
                            var priceElement = element.closest('tr').querySelector(from).textContent;

                            var price = onlyNumbers(priceElement);

                            // Add the price to the sum of prices
                            sumPrice += price;

                            // Check if the price and percentage values are not empty
                            if (price) {
                                // Calculate the result and format it
                                var percentage = percentageResult(price, newPercentage, 2);

                                // Check if the result is not empty
                                if (percentage) {
                                    // Get the 'to' element and set its value
                                    var toElement = element.closest('tr').querySelector(to);

                                    toElement.value = onlyNumbers(percentage);

                                    // Get the 'wrap-form-btn' element and remove the 'd-none' class
                                    var wrapFormBtn = element.closest('form').querySelector('.wrap-form-btn');
                                    if (wrapFormBtn) {
                                        wrapFormBtn.classList.remove('d-none');
                                    }
                                }
                            }
                        });

                        // Check if the sum of prices is zero
                        if (sumPrice === 0) {
                            toastAlert('Não há dados de vendas relativa ao periodo ' + previousMeantime + ' e por isso não será possível executar o autopreenchimento', 'warning', 20000);

                            return false;
                        }

                        // Call the sumInputNumbers function (you need to define this function)
                        sumInputNumbers('.o-sum-fields-current', '.sum-result-current');

                        formatNumberInput();

                        // Add the 'blink' class to the button
                        var blinkButton = document.querySelector('.wrap-form-btn button');
                        if (blinkButton) {
                            blinkButton.classList.add('blink');
                        }

                        // Get the target ID for scrolling
                        var goToTarget = document.querySelector('.wrap-form-btn').querySelector('button').id;

                        // Call the goTo function (you need to define this function)
                        goTo(goToTarget, 0);

                        // Remove the 'blink' class from the button after 10 seconds
                        setTimeout(function () {
                            var blinkButton = document.querySelector('.wrap-form-btn button');
                            if (blinkButton) {
                                blinkButton.classList.remove('blink');
                            }
                        }, 10000);

                    }, 100);

                    return;
                }
            });

        }
    });


    //Initialize bsPopoverTooltip
    bsPopoverTooltip();



});
