@php
// Initialize arrays to store the sum of sales and goals for each company
$totalSales = [];
$totalGoals = [];
$totalPercent = [];
$uniqueDepartments = [];
$metric = metricGoalSales($getMeantime);
$totalPercentAccrued = 0;
$ndxChartId = 0;
@endphp

<div id="load-listing" class="mb-4 rounded position-relative wrap-filter-result toogle_zoomInOut ribbon-box border ribbon-fill shadow-none">
    <div class="ribbon ribbon-info bg-theme text-black fs-12 @if(empty($data)) d-none @endif" style="z-index: 2; scale: 1.5; top: -10px; left: -30px;">
        {{ $metric . '%' }}
    </div>
    <div class="table-responsive mb-0">
        <table id="goal-sales-dataTable" class="table table-striped-columns table-nowrap listing-chart mb-0">
            <thead class="text-uppercase table-light">
                <tr>
                    <th scope="col" class="bg-transparent fs-20 text-center invisible"></th>
                    @foreach ($data as $companyId => $departments)
                        <th scope="col" class="text-center" data-company-id="{{ $companyId }}">
                            {{ getCompanyAlias(intval($companyId)) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $companyId => $departments)
                    @foreach ($departments as $departmentId => $values)
                        @php
                        $uniqueDepartments[$departmentId] = getDepartmentAlias(intval($departmentId));
                        @endphp
                    @endforeach
                @endforeach

                @foreach ($uniqueDepartments as $departmentId => $departmentAlias)
                    <tr tr-department="{{ $departmentId }}" class="">
                        <th scope="row" class="text-uppercase fs-16 align-middle text-end p-3">
                            {{ $departmentAlias }}
                        </th>
                        @foreach ($data as $companyId => $departments)
                            @php
                            $ndxChartId++;

                            $sales = floatval($departments[$departmentId]['sales'] ?? 0);
                            $goal = floatval($departments[$departmentId]['goal'] ?? 0);

                            $percent = $sales > 0 && $goal > 0 ? ($sales / $goal) * 100 : 0;

                            $percentAccrued = ($percent/$metric) * 100;

                            // Calculate the sum of sales and goals for each company
                            $totalSales[$companyId] = floatval($totalSales[$companyId] ?? 0) + $sales;
                            $totalGoals[$companyId] = floatval($totalGoals[$companyId] ?? 0) + $goal;

                            $totalPercent[$companyId] = $totalSales[$companyId] > 1 && $totalGoals[$companyId] > 1 ? ($totalSales[$companyId] / $totalGoals[$companyId]) * 100 : 0;
                            @endphp
                            <td class="text-center align-middle" data-company-id="{{ $companyId }}" data-chart-id="{{ $ndxChartId }}">
                                {{--
                                <div>Sales: {{ number_format($sales, 2, '.', '') }}</div>
                                <div>Goals: {{ number_format($goal, 2, '.', '') }}</div>
                                <div>Percent: {{ number_format($percent, 2, '.', '') }}</div>
                                --}}
                                @php
                                echo goalsEmojiChart($ndxChartId, $goal, $sales, $departmentId, getDepartmentAlias($departmentId), getCompanyAlias($companyId), $percent, $percentAccrued)
                                @endphp
                            </td>
                        @endforeach
                    </tr>
                @endforeach

                <tr tr-department="sum" class="">
                    <th scope="row" class="text-uppercase fs-16 align-middle text-end p-3">
                        GERAL
                    </th>
                    @foreach ($data as $companyId => $departments)
                        @php
                        $ndxChartId++;

                        //APP_print_r($totalPercent);
                        $totalPercentValue = number_format($totalPercent[$companyId] ?? 0, 2, '.', '');
                        $totalPercentAccrued = ($totalPercentValue / $metric) * 100;
                        @endphp
                        <td class="text-center align-middle" data-company-id="{{ $companyId }}" data-chart-id="{{ $ndxChartId }}">
                            {{--
                            <div>Sales: {{ number_format($totalSales[$companyId] ?? 0, 2, '.', '') }}</div>
                            <div>Goals: {{ number_format($totalGoals[$companyId] ?? 0, 2, '.', '') }}</div>
                            <div>Percent: {{ number_format($totalPercentValue ?? 0, 2, '.', '') }}</div>
                            --}}
                            @php
                            echo goalsEmojiChart($ndxChartId, number_format($totalGoals[$companyId] ?? 0, 2, '.', ''), number_format($totalSales[$companyId] ?? 0, 2, '.', ''), $departmentId, getDepartmentAlias($departmentId), getCompanyAlias($companyId), $totalPercentValue, $totalPercentAccrued, 'general')
                            @endphp
                        </td>
                    @endforeach
                </tr>
            </tbody>
            <tfoot class="text-uppercase table-light">
                <tr>
                    <th scope="col" class="bg-transparent invisible"></th>
                    @foreach ($data as $companyId => $departments)
                        <th scope="col" class="text-center" data-company-id="{{ $companyId }}">
                            {{ getCompanyAlias(intval($companyId)) }}
                        </th>
                    @endforeach
                </tr>
            </tfoot>
        </table>
    </div>
</div>
