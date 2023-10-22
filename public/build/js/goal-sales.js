import { ToastAlert, MultipleModal, formatNumberInput, helperSumValues } from './helpers.js';

window.addEventListener('load', function () {

    /**
     * Toggle the display of the custom meantime input field based on the selected option in the meantime select dropdown.
     */
    const meantimeSelect = document.querySelector('select[name="meantime"]');
    const customMeantimeDiv = document.querySelector('.custom_meantime_is_selected');
    const customMeantimeInput = document.querySelector('.custom_meantime_is_selected input');

    function toggleCustomMeantimeInput() {
        const selectedOption = meantimeSelect.value;
        if (selectedOption === 'custom') {
            customMeantimeDiv.style.display = 'block';
        } else {
            customMeantimeDiv.style.display = 'none';

            if (customMeantimeInput) {
                customMeantimeInput.value = '';
            }
        }
    }
    toggleCustomMeantimeInput();

    meantimeSelect.addEventListener('change', toggleCustomMeantimeInput);

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
     * Hide the load listing element slowly on form change.
     */
    function hideLoadListingOnFormChange() {
        // Get the form element
        var filterForm = document.getElementById("filterForm");

        // Get the load listing element
        var loadListing = document.getElementById("load-listing");

        // Add a change event listener to the form
        filterForm.addEventListener("change", function () {
            // Hide the load listing element slowly
            loadListing.classList.add("hide-slowly");
        });
    }
    hideLoadListingOnFormChange();


    /**
     * Load the content for the Goal Sales Settings
     */
    async function loadGoalSalesSettingsModal() {
        try {
            const response = await fetch('/goal-sales/settings', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
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
            ToastAlert('Não foi possível carregar o conteúdo', 'error', 10000);
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

            MultipleModal();

            formatNumberInput();

            // Call helperSumValues after the modal content has been loaded
            helperSumValues('.o-sum-fields-previous', 'sum-result-previous', 0);
            helperSumValues('.o-sum-fields-current', 'sum-result-current', 0);
            helperSumValues('.o-sum-fields', 'sum-result', 0);

            makeGoalSalesUpdate(meantime, companyId);

        } catch (error) {
            console.error('Error fetching modal content:', error);
            ToastAlert('Não foi possível carregar o conteúdo', 'error', 10000);
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
    function makeGoalSalesUpdate(meantime, companyId) {

        // store/update goalSalesForm
        document.getElementById('btn-goal-sales-update').addEventListener('click', async function(event) {
            event.preventDefault();

            const form = document.getElementById('goalSalesForm');

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

                    ToastAlert(data.message, 'success', 10000);
                } else {
                    ToastAlert(data.message, 'danger', 60000);
                }
            } catch (error) {
                ToastAlert('Error: ' + error, 'danger', 60000);
                console.error('Error:', error);
            }
        });
    }


    function emoticonChart() {
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
    }
    emoticonChart();




});
