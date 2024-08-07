@php
    $uniqueDepartments = $totalSales = $totalGoals = $totalPercent = [];
@endphp

<div id="load-listing" class="mb-4 rounded position-relative toogle_zoomInOut ribbon-box border ribbon-fill shadow-none @if (!$data) d-none @endif">
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
                            {{ getCompanyNameById(intval($companyId)) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $companyId => $departments)
                    @foreach ($departments as $departmentId => $values)
                        @php
                            $uniqueDepartments[$departmentId] = getDepartmentNameById(intval($departmentId));
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

                                $sales = isset($departments[$departmentId]['sales']) ? floatval($departments[$departmentId]['sales']) : 0;
                                $goal = isset($departments[$departmentId]['goal']) ? floatval($departments[$departmentId]['goal']) : 0;

                                $percent = $sales > 0 && $goal > 0 ? ($sales / $goal) * 100 : 0;

                                $percentAccrued = $percent > 0 && $metricNumber > 0 ? ($percent / $metricNumber) * 100 : 0;

                                // Calculate the sum of sales and goals for each company
                                $totalSales[$companyId] = isset($totalSales[$companyId]) ? floatval($totalSales[$companyId]) + $sales : 0;
                                $totalGoals[$companyId] = isset($totalGoals[$companyId]) ? floatval($totalGoals[$companyId]) + $goal : 0;

                                $totalPercent[$companyId] = $totalSales[$companyId] > 1 && $totalGoals[$companyId] > 1 ? ($totalSales[$companyId] / $totalGoals[$companyId]) * 100 : 0;

                            @endphp
                            <td class="text-center align-middle" data-company-id="{{ $companyId }}" data-chart-id="{{ $ndxChartId }}">
                                @php
                                    echo goalsEmojiChart($ndxChartId, $goal, $sales, $departmentId, getDepartmentNameById($departmentId), getCompanyNameById($companyId), $percent, $percentAccrued);
                                @endphp
                            </td>
                        @endforeach
                    </tr>
                @endforeach

                @if (count($getActiveDepartments) > 1)
                    <tr tr-department="sum" class="">
                        <th scope="row" class="text-uppercase fs-16 align-middle text-end p-3 @if (!empty($filterDepartments) && count($filterDepartments) == 1) d-none @endif">
                            GERAL
                        </th>
                        @foreach ($data as $companyId => $departments)
                            @php
                                $ndxChartId++;

                                $totalPercentValue = isset($totalPercent[$companyId]) ? floatval($totalPercent[$companyId]) : 0;
                                $totalPercentAccrued = $totalPercentValue > 0 ? ($totalPercentValue / $metricNumber) * 100 : 0;
                            @endphp
                            <td class="text-center align-middle @if (!empty($filterDepartments) && count($filterDepartments) == 1)  d-none @endif" data-company-id="{{ $companyId }}" data-chart-id="{{ $ndxChartId }}">
                                @php
                                    echo goalsEmojiChart($ndxChartId, floatval($totalGoals[$companyId]), floatval($totalSales[$companyId]), 'general', 'Geral', getCompanyNameById($companyId), $totalPercentValue, $totalPercentAccrued, 'general');
                                @endphp
                            </td>
                        @endforeach
                    </tr>
                @endif
            </tbody>
            {{--
            <tfoot class="text-uppercase table-light">
                <tr>
                    <th scope="col" class="bg-transparent invisible"></th>
                    @foreach ($data as $companyId => $departments)
                        <th scope="col" class="text-center" data-company-id="{{ $companyId }}">
                            {{ getCompanyNameById(intval($companyId)) }}
                        </th>
                    @endforeach
                </tr>
            </tfoot>
            --}}
        </table>
    </div>
</div>
