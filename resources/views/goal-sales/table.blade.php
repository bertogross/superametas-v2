@php
// Initialize arrays to store the sum of sales and goals for each company
$totalSales = [];
$totalGoals = [];
$uniqueDepartments = [];
@endphp

<div id="load-listing" class="mb-4 rounded position-relative wrap-filter-result toogle_zoomInOut ribbon-box border ribbon-fill shadow-none">
    <div class="ribbon ribbon-info bg-theme text-black fs-12 @if(empty($data)) d-none @endif" style="z-index: 2; scale: 1.5; top: -10px; left: -30px;">
        {{ metricGoalSales() }}
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
                            $sales = $departments[$departmentId]['sales'] ?? 0;
                            $goal = $departments[$departmentId]['goal'] ?? 0;

                            // Calculate the sum of sales and goals for each company
                            $totalSales[$companyId] = ($totalSales[$companyId] ?? 0) + $sales;
                            $totalGoals[$companyId] = ($totalGoals[$companyId] ?? 0) + $goal;
                            @endphp

                            <td class="text-center align-middle" data-company-id="{{ $companyId }}" data-chart-id="0">
                                <div>Sales: {{ number_format($sales, 2, '.', '') }}</div>
                                <div>Goals: {{ number_format($goal, 2, '.', '') }}</div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach

                <tr tr-department="sum" class="">
                    <th scope="row" class="text-uppercase fs-16 align-middle text-end p-3">
                        GERAL
                    </th>
                    @foreach ($data as $companyId => $departments)
                        <td class="text-center align-middle" data-company-id="{{ $companyId }}" data-chart-id="0">
                            <div>Sales: {{ number_format($totalSales[$companyId] ?? 0, 2, '.', '') }}</div>
                            <div>Goals: {{ number_format($totalGoals[$companyId] ?? 0, 2, '.', '') }}</div>
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
