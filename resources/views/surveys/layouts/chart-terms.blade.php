<div class="row">
    <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
        <div id="barTermsChart" class="rounded rounded-2 bg-light p-3 h-100"></div>
    </div>

    <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
        <div id="mixedTermsChart" class="rounded rounded-2 bg-light p-3 h-100"></div>
    </div>

    <div class="col-sm-6 col-md-4 col-lg-4 mb-3">
        <div id="polarTermsAreaChart" class="rounded rounded-2 bg-light p-3 h-100"></div>
    </div>
</div>

<script>
    const rawTermsData = @json($analyticTermsData);
    const terms = @json($terms);
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // #barTermsChart
    var seriesData = [];
    var categories = [];

    for (var date in rawTermsData) {
        for (var termId in rawTermsData[date]) {
            var termData = rawTermsData[date][termId];
            var totalComplianceYes = termData.filter(item => item.compliance_survey === 'yes').length;
            var totalComplianceNo = termData.filter(item => item.compliance_survey === 'no').length;

            seriesData.push({
                x: terms[termId]['name'] + ' (' + date + ')',
                y: totalComplianceYes - totalComplianceNo
            });

            categories.push(terms[termId]['name'] + ' (' + date + ')');
        }
    }

    var optionsTermsChart = {
        series: [{
            name: 'Score',
            data: seriesData
        }],
        title: {
            //text: 'Compliance Bars'
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

    // #mixedTermsChart
    var columnSeriesData = [];
    var lineSeriesData = [];
    var categories = [];

    for (var date in rawTermsData) {
        for (var termId in rawTermsData[date]) {
            var termData = rawTermsData[date][termId];
            var totalComplianceYes = termData.filter(item => item.compliance_survey === 'yes').length;
            var totalComplianceNo = termData.filter(item => item.compliance_survey === 'no').length;

            columnSeriesData.push(totalComplianceYes);
            lineSeriesData.push(totalComplianceNo);
            categories.push(terms[termId]['name'] + ' (' + date + ')');
        }
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
            //text: 'Compliance Trends'
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

    // #polarTermsAreaChart
    var seriesData = [];
    var labels = [];

    var termMetrics = {};

    // Aggregate data for each term
    for (var date in rawTermsData) {
        for (var termId in rawTermsData[date]) {
            var termData = rawTermsData[date][termId];
            var totalCompliance = termData.filter(item => item.compliance_survey === 'yes').length;

            if (!termMetrics[termId]) {
                termMetrics[termId] = 0;
            }
            termMetrics[termId] += totalCompliance;
        }
    }

    // Prepare data for the chart
    for (var termId in termMetrics) {
        seriesData.push(termMetrics[termId]);
        labels.push(terms[termId]['name']);
    }

    var optionsTermsAreaChart = {
        series: seriesData,
        chart: {
            type: 'polarArea',
            toolbar: {
                show: false,
            }
        },
        labels: labels,
        stroke: {
            colors: ['#fff']
        },
        fill: {
            opacity: 0.8
        },
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                }
            }
        }]
    };

    var polarTermsAreaChart = new ApexCharts(document.querySelector("#polarTermsAreaChart"), optionsTermsAreaChart);
    polarTermsAreaChart.render();
});
</script>
