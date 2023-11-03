<div id="load-listing" class="mb-4 rounded position-relative toogle_zoomInOut ribbon-box border ribbon-fill shadow-none @if (!$data) d-none @endif">
    <div class="ribbon ribbon-info bg-theme text-black fs-12 @if(empty($data)) d-none @endif" style="z-index: 2; scale: 1.5; top: -10px; left: -30px;">
        {{ $metric . '%' }}
    </div>

    <div class="row listing-chart">
        @foreach ($data as $companyId => $departments)
            @foreach ($departments as $departmentId => $values)
                @php
                    $ndxChartId++;

                    $sales = floatval($values['sales'] ?? 0);
                    $goal = floatval($values['goal'] ?? 0);

                    $percent = $sales > 0 && $goal > 0 ? ($sales / $goal) * 100 : 0;

                    $percentAccrued = $percent > 0 && $metric > 0 ? ($percent/$metric) * 100 : 0;

                    // Calculate the sum of sales and goals for each company
                    $totalSales[$companyId] = floatval($totalSales[$companyId] ?? 0) + $sales;
                    $totalGoals[$companyId] = floatval($totalGoals[$companyId] ?? 0) + $goal;

                    $totalPercent[$companyId] = $totalSales[$companyId] > 1 && $totalGoals[$companyId] > 1 ? ($totalSales[$companyId] / $totalGoals[$companyId]) * 100 : 0;
                @endphp
                <div class="col m-4 text-center text-uppercase">
                    {{--
                    <div>Sales: {{ numberFormat($sales, 2) }}</div>
                    <div>Goals: {{ numberFormat($goal, 2) }}</div>
                    <div>Percent: {{ numberFormat($percent, 2) }}</div>
                    --}}
                    @php
                        echo goalsEmojiChart($ndxChartId, $goal, $sales, $departmentId, getDepartmentAlias($departmentId), getCompanyAlias($companyId), $percent, $percentAccrued);
                    @endphp
                    <div class="chart-label fw-bold">{{ getDepartmentAlias($departmentId) }}</div>
                </div>
            @endforeach

            @if (count($getDepartmentsActive) > 1)
                <div class="col-12 m-4 text-center text-uppercase @if (!empty($filterDepartments) && count($filterDepartments) == 1) d-none @endif">
                    {{--
                    <div>Sales: {{ numberFormat($totalSales[$companyId], 2) }}</div>
                    <div>Goals: {{ numberFormat($totalGoals[$companyId], 2) }}</div>
                    <div>Percent: {{ numberFormat($totalPercentValue, 2) }}</div>
                    --}}
                    @php
                    $ndxChartId++;

                    //appPrintR($totalPercent);
                    $totalPercentValue = numberFormat($totalPercent[$companyId], 2);
                    $totalPercentAccrued = ($totalPercentValue / $metric) * 100;

                    echo goalsEmojiChart($ndxChartId, numberFormat($totalGoals[$companyId], 2), numberFormat($totalSales[$companyId], 2), 'general', 'Geral', getCompanyAlias($companyId), $totalPercentValue, $totalPercentAccrued, 'general');
                    @endphp
                    <div class="chart-label fw-bold fs-4">Geral</div>
                </div>
            @endif
        @endforeach
    </div>
</div>
