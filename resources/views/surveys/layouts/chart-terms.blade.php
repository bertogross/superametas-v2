@php
    $avatars = \App\Models\Survey::extractUserIds($analyticTermsData);
    $avatarsJson = json_encode($avatars);

    //appPrintR($analyticTermsData);
    //appPrintR($terms);
    //appPrintR($avatars);
@endphp
<div class="row mt-3 mb-3">

    <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
        <div id="barTermsChart" class="rounded rounded-2 bg-light p-3 h-100"></div>
    </div>

    <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
        <div id="mixedTermsChart" class="rounded rounded-2 bg-light p-3 h-100"></div>
    </div>

    <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
        <div id="calendar" class="rounded rounded-2 bg-light p-3 h-100"></div>
    </div>

    <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
        <div id="polarTermsAreaChart" class="rounded rounded-2 bg-light p-3 h-100"></div>
    </div>

    <div class="col-sm-12 col-md-12 col-lg-12 mb-3 d-none">
        <div id="usersChart" class="rounded rounded-2 bg-light p-3 h-100"></div>
    </div>
</div>

<script>
    const rawTermsData = @json($analyticTermsData);
    const terms = @json($terms);
    const avatars = @json($avatarsJson);
    //console.log(avatars);

    document.addEventListener('DOMContentLoaded', function() {
        ///////////////////////////////////////////////////////////////
        // START #barTermsChart
        var seriesData = [];
        var categories = [];

        for (var termId in rawTermsData) {
            var termData = rawTermsData[termId];
            var totalComplianceYes = 0;
            var totalComplianceNo = 0;

            for (var date in termData) {
                termData[date].forEach(function(item) {
                    if (item.compliance_survey === 'yes') {
                        totalComplianceYes++;
                    } else if (item.compliance_survey === 'no') {
                        totalComplianceNo++;
                    }
                });
            }

            seriesData.push({
                x: terms[termId] && terms[termId]['name'] ? terms[termId]['name'] : "Term " + termId,
                y: totalComplianceYes - totalComplianceNo
            });

            categories.push(terms[termId] && terms[termId]['name'] ? terms[termId]['name'] : "Term " + termId);
        }

        var optionsTermsChart = {
            series: [{
                name: 'Score',
                data: seriesData
            }],
            title: {
                text: 'Dinâmica de Pontuação na Conformidade entre Termos'// Score
            },
            chart: {
                type: 'bar',
                height: 400,
                toolbar: {
                    show: false,
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    colors: {
                        ranges: [{
                            from: -1000,
                            to: 0,
                            color: '#DF5253'
                        }, {
                            from: 1,
                            to: 1000,
                            color: '#1FDC01'
                        }],
                    },
                    dataLabels: {
                        position: 'top',
                    },
                },
            },
            xaxis: {
                categories: categories
            },
            fill: {
                opacity: 1
            },
        };

        var barTermsChart = new ApexCharts(document.querySelector("#barTermsChart"), optionsTermsChart);
        barTermsChart.render();

        // END #barTermsChart

        ///////////////////////////////////////////////////////////////
        // START #mixedTermsChart
        var columnSeriesData = [];
        var lineSeriesData = [];
        var categories = [];

        var termMetrics = {};

        // Aggregate data for each term
        for (var termId in rawTermsData) {
            var termData = rawTermsData[termId];
            var totalComplianceYes = 0;
            var totalComplianceNo = 0;

            for (var date in termData) {
                termData[date].forEach(function(item) {
                    if (item.compliance_survey === 'yes') {
                        totalComplianceYes++;
                    } else if (item.compliance_survey === 'no') {
                        totalComplianceNo++;
                    }
                });
            }

            termMetrics[termId] = {
                'yes': totalComplianceYes,
                'no': totalComplianceNo
            };
        }

        // Prepare data for the chart
        for (var termId in termMetrics) {
            columnSeriesData.push(termMetrics[termId]['yes']);
            lineSeriesData.push(termMetrics[termId]['no']);
            categories.push(terms[termId]['name']); // Assuming terms is an object with term names
        }

        var optionsMixedTermsChart = {
            series: [{
                name: 'Conforme',
                type: 'column',
                data: columnSeriesData
            }, {
                name: 'Não Conforme',
                type: 'line',
                data: lineSeriesData
            }],
            chart: {
                height: 400,
                type: 'line',
                toolbar: {
                    show: false,
                }
            },
            stroke: {
                width: [0, 4]
            },
            title: {
                text: 'Insights Comparativos de Conformidade'// Compliance Overview by Term
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1]
            },
            labels: categories,
            xaxis: {
                type: 'category'
            },
            yaxis: [{
                title: {
                    text: 'Conforme'
                }
            }, {
                opposite: true,
                title: {
                    text: 'Não Conforme'
                }
            }],
            colors: ['#1FDC01', '#DF5253']  // Assign custom colors to Compliance Yes and No
        };

        var mixedTermsChart = new ApexCharts(document.querySelector("#mixedTermsChart"), optionsMixedTermsChart);
        mixedTermsChart.render();
        // END #mixedTermsChart
        ///////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////
        // START #polarTermsAreaChart
        var seriesData = [];
        var labels = [];

        var termMetrics = {};

        // Aggregate data for each term
        for (var termId in rawTermsData) {
            var termData = rawTermsData[termId];
            var totalCompliance = 0;

            for (var date in termData) {
                termData[date].forEach(function(item) {
                    if (item.compliance_survey === 'yes') {
                        totalCompliance++;
                    }
                });
            }

            termMetrics[termId] = totalCompliance;
        }

        // Prepare data for the chart
        for (var termId in termMetrics) {
            seriesData.push(termMetrics[termId]);

            // Check if termId exists in terms object
            if (terms[termId] && terms[termId]['name']) {
                labels.push(terms[termId]['name']);
            } else {
                // Fallback if term name is not found
                labels.push("Term " + termId);
            }
        }

        var optionsTermsAreaChart = {
            series: seriesData,
            chart: {
                type: 'polarArea',
                toolbar: {
                    show: false,
                }
            },
            title: {
                text: 'Análise Polar de Conformidade'// Terms Compliance Polar Analysis
            },
            labels: labels,
            stroke: {
                colors: ['#fff']
            },
            fill: {
                opacity: 0.8
            },
            legend: {
                show: true,
                position: 'bottom'
            },
            yaxis: {
                show: false // Disable Y-axis labels
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    }
                }
            }],

        };

        var polarTermsAreaChart = new ApexCharts(document.querySelector("#polarTermsAreaChart"), optionsTermsAreaChart);
        polarTermsAreaChart.render();

        // END #polarTermsAreaChart
        ///////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////

        // START #usersChart
        function getAvatarUrl(userId) {
            avatarsObj = JSON.parse(avatars);
            return avatarsObj[userId].avatar;
        }

        function getUserName(userId) {
            avatarsObj = JSON.parse(avatars);
            return avatarsObj[userId].name;
        }

        var scatterData = [];
        var avatarUrls = {};
        var legendNames = [];

        /*for (var termId in rawTermsData) {
            for (var date in rawTermsData[termId]) {
                rawTermsData[termId][date].forEach(function(item) {
                    var x = item.surveyor_id;
                    var y = item.auditor_id || x;

                    scatterData.push([x, y]);

                    // Fetch and store avatar URLs and names for each user
                    avatarUrls[item.surveyor_id] = getAvatarUrl(item.surveyor_id);
                    if (!legendNames.includes(getUserName(item.surveyor_id))) {
                        legendNames.push(getUserName(item.surveyor_id));
                    }
                    if (item.auditor_id) {
                        avatarUrls[item.auditor_id] = getAvatarUrl(item.auditor_id);
                        if (!legendNames.includes(getUserName(item.auditor_id))) {
                            legendNames.push(getUserName(item.auditor_id));
                        }
                    }
                });
            }
        }*/
        for (var termId in rawTermsData) {
            for (var date in rawTermsData[termId]) {
                rawTermsData[termId][date].forEach(function(item) {
                    var x = item.surveyor_id;
                    var y = item.auditor_id || x;

                    // Avoid duplicate entries for the same user
                    if (!scatterData.some(point => point[0] === x && point[1] === y)) {
                        scatterData.push([x, y]);
                    }

                    // Fetch and store avatar URLs for each user
                    if (avatars[x]) {
                        avatarUrls[x] = getAvatarUrl(x);
                    }
                    if (avatars[y] && y !== x) {
                        avatarUrls[y] = getAvatarUrl(y);
                    }
                });
            }
        }

        //console.log("Scatter Data:", scatterData);
        //console.log("Avatar URLs:", avatarUrls);

        var options = {
            series: [{
                name: 'Users',
                data: scatterData
            }],
            chart: {
                height: 350,
                type: 'scatter',
                animations: {
                    enabled: false,
                },
                zoom: {
                    enabled: false,
                },
                toolbar: {
                    show: false
                }
            },
            title: {
                text: 'Usuários Envolvidos'
            },
            colors: ['#056BF6'],
            xaxis: {
                tickAmount: 10,
                min: 0,
                max: 40
            },
            yaxis: {
                tickAmount: 7
            },
            /*
            markers: {
                size: 20,
                customHTML: function({ seriesIndex, dataPointIndex, w }) {
                    var id = w.config.series[seriesIndex].data[dataPointIndex][0];
                    if (avatarUrls[id]) {
                        return `<img src="${avatarUrls[id]}" width="40" height="40">`;
                    }
                    return ''; // Return an empty string if avatar URL is not found
                }
            },
            fill: {
                type: 'image',
                opacity: 1,
                image: {
                    src: Object.values(avatarUrls),
                    width: 40,
                    height: 40
                }
            },*/
            markers: {
                size: 20,
                customHTML: function({ seriesIndex, dataPointIndex, w }) {
                    var id = w.config.series[seriesIndex].data[dataPointIndex][0];
                    if (avatarUrls[id]) {
                        return `<img src="${avatarUrls[id]}" width="40" height="40">`;
                    }
                    return ''; // Return an empty string if avatar URL is not found
                }
            },
            fill: {
                type: 'image',
                opacity: 1,
                image: {
                    src: Object.values(avatarUrls),
                    width: 40,
                    height: 40
                }
            },


        };

        var usersChart = new ApexCharts(document.querySelector("#usersChart"), options);
        usersChart.render();
        // END #usersChart
        ///////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////
        // START #calendar
        var calendarEl = document.getElementById('calendar');

        // Function to convert date format from DD-MM-YYYY to YYYY-MM-DD
        function convertDateFormat(dateStr) {
            var parts = dateStr.split('-');
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        function convertDateFormatWithBar(dateStr) {
            var parts = dateStr.split('-');
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }

        /*
        var taskCounts = {};
        for (var termId in rawTermsData) {
            for (var date in rawTermsData[termId]) {
                var formattedDate = convertDateFormat(date); // Convert date format
                if (!taskCounts[formattedDate]) {
                    taskCounts[formattedDate] = 0;
                }
                taskCounts[formattedDate] += rawTermsData[termId][date].length;
            }
        }
        */
        var uniqueCompaniesByDate = {};
        for (var termId in rawTermsData) {
            for (var date in rawTermsData[termId]) {
                var formattedDate = convertDateFormat(date); // Convert date format
                rawTermsData[termId][date].forEach(function(item) {
                    var companyId = item.company_id;
                    if (!uniqueCompaniesByDate[formattedDate]) {
                        uniqueCompaniesByDate[formattedDate] = new Set();
                    }
                    uniqueCompaniesByDate[formattedDate].add(companyId);
                });
            }
        }

        /*
        // Transform grouped data into FullCalendar event format
        var calendarEvents = [];
        for (var date in taskCounts) {
            var eventTitle = taskCounts[date];
            calendarEvents.push({
                title: eventTitle,
                start: date,
                overlap: false,
                display: 'background',
                //color: '#87DF01',
                textColor: '#000000',
                url: '{{ route('surveysShowURL', $surveyId) }}?created_at='+convertDateFormatWithBar(date),
                className: 'cursor-pointer text-dark bg-theme'
            });
        }
        */
        // Transform grouped data into FullCalendar event format
        var calendarEvents = [];
        for (var date in uniqueCompaniesByDate) {
            var companyCount = uniqueCompaniesByDate[date].size;
            //var eventTitle = 'Tasks: ' + companyCount;
            var eventTitle = companyCount;
            calendarEvents.push({
                title: eventTitle,
                start: date,
                overlap: false,
                display: 'background',
                textColor: '#000000',
                url: '{{ route('surveysShowURL', $surveyId) }}?created_at=' + convertDateFormatWithBar(date),
                className: 'cursor-pointer text-dark bg-theme'
            });
        }

        // Initialize FullCalendar
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'pt-BR',
            defaultView: 'month',
            initialView: 'dayGridMonth',
            events: calendarEvents,
            selectable: false,
            eventClick: function(info) {
                info.jsEvent.preventDefault();

                if (info.event.url) {
                    window.location.href = info.event.url
                }
            },
            eventDidMount: function(info) {
                if (info.event.url) {
                    info.el.title = 'Clique para mais detalhes desta data';
                }
            },
            /*
            customButtons: {
                monthViewButton: {
                    text: 'Mês',
                    click: function() {
                        calendar.changeView('dayGridMonth');
                    }
                },
                listViewButton: {
                    text: 'Lista',
                    click: function() {
                        calendar.changeView('listMonth'); // You can choose 'listDay', 'listWeek', 'listMonth', or 'listYear'
                    }
                }
            },
            */
            headerToolbar: {
                left: 'title',
                center: '',// monthViewButton,listViewButton
                right: 'prev,next'
            }

        });

        console.log(calendarEvents);
        calendar.render();
        // END #calendar
        ///////////////////////////////////////////////////////////////


    });
</script>
