<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\UserMeta;
use App\Models\GoalSales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GoalSalesController extends Controller
{
    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    /**
     * Chart Area and Table listing
     */
    public function index(Request $request)
    {
        $currentUserId = auth()->id();

        $selectedCompanies = $request->input('companies');
        $selectedDepartments = $request->input('departments');

        // Get the filters from the request
        $getMeantime = e($request->input('meantime', date('Y-m')));
        $getCustomMeantime = e($request->input('custom_meantime'));
        $getMeantime = $getMeantime == 'custom' && empty($getCustomMeantime) ? date('Y-m') : $getMeantime;

        if( strlen($getMeantime) == 7 ){
            $getMeantime = $getMeantime;
        } elseif ( strlen($getCustomMeantime) == 7 || strlen($getCustomMeantime) > 7 ){
            $getMeantime = $getCustomMeantime;
        }else{
            $getMeantime = $getMeantime;
        }

        // Calculate the start and end dates based on the selected timeframe
        list($startDate, $endDate) = $this->calculateStartAndEndDate($getMeantime);

        $dateRange = GoalSales::getSaleDateRange();
        $firstDate = $dateRange['first_date'];
        $lastDate = $dateRange['last_date'];

        // If the user has analytic mode enabled
        if (getUserMeta($currentUserId, 'analytic-mode') == 'on') {
            // Get sales data
            $resultsSales = $this->getSalesData($startDate, $endDate, $selectedCompanies, $selectedDepartments);

            // Get goals data
            $resultsGoals = $this->getGoalsData($startDate, $endDate, $selectedCompanies, $selectedDepartments);

            // Process the results
            list($data, $totalSalesByMonth, $totalGoalsByMonth) = $this->processResults($resultsSales, $resultsGoals, $getMeantime);

            $departments = $this->getDepartmentData($data);

            // Return the view with the processed data
            return view('goal-sales.index', compact(
                'data',
                'departments',
                'resultsSales',
                'resultsGoals',
                'totalSalesByMonth',
                'totalGoalsByMonth',
                'firstDate',
                'lastDate'
            ));
        } else {
            // If analytic mode is not enabled, use the getGoalAndSalesData query
            $data = $this->getGoalAndSalesData($startDate, $endDate, $selectedCompanies, $selectedDepartments);

            // Return the view with the old data
            return view('goal-sales.index', compact('data', 'firstDate', 'lastDate'));
        }
    }

    /**
     * Get the content for the modal settings.
     */
    public function settings() {

        $dateRange = GoalSales::getSaleDateRange();
        $firstDate = $dateRange['first_date'];
        $lastDate = $dateRange['last_date'];

        return view('goal-sales.settings-edit', compact('firstDate', 'lastDate'));

    }

    /**
     * Get the content for the modal edit.
     */
    public function edit(Request $request)
    {
        // Cache::flush();

        $getActiveDepartments = getActiveDepartments();

        //$meantime = $request->input('meantime');
        $meantime = request('meantime', date('Y-m'));
        //$companyId = $request->input('companyId');
        $companyId = request('companyId');

        $previousMeantimeMonthBefore = date('Y-m', strtotime($meantime." -1 months"));

        $previousMeantimeYearBefore = date('Y-m', strtotime($meantime." -12 months"));

        $getIPCA = getIPCAdata($meantime);

        // Query the wlsm_goals table to get the goals for the given companyId and meantime
        $goals = DB::connection($this->connection)
            ->table('wlsm_goals')
            ->where('company_id', $companyId)
            ->where('meantime', $meantime)
            ->where('type', 'sales')
            ->get()
            ->pluck('goal_value', 'department_id')
            ->toArray();

        // Query the wlsm_sales table to get the sales for the given companyId and meantime
        $salesYearBefore = DB::connection($this->connection)
            ->table('wlsm_sales')
            ->where('company_id', $companyId)
            ->where('date_sale', 'LIKE', $previousMeantimeYearBefore . '%')
            ->selectRaw('department_id, SUM(net_value) as total_net_value')
            ->groupBy('department_id')
            ->get()
            ->pluck('total_net_value', 'department_id')
            ->toArray();


        // Query the wlsm_sales table to get the sales for the given companyId and meantime
        $salesMonthBefore = DB::connection($this->connection)
            ->table('wlsm_sales')
            ->where('company_id', $companyId)
            ->where('date_sale', 'LIKE', $previousMeantimeMonthBefore . '%')
            ->selectRaw('department_id, SUM(net_value) as total_net_value')
            ->groupBy('department_id')
            ->get()
            ->pluck('total_net_value', 'department_id')
            ->toArray();

        $dateRange = GoalSales::getSaleDateRange();
        $firstDate = $dateRange['first_date'];
        $lastDate = $dateRange['last_date'];

        return view('goal-sales.edit', compact(
                'goals',
                'salesYearBefore',
                'salesMonthBefore',
                'meantime',
                'firstDate',
                'lastDate',
                'previousMeantimeYearBefore',
                'previousMeantimeMonthBefore',
                'getActiveDepartments',
                'getIPCA',
                'companyId'
            )
        );

    }

    /**
     * GStore or Update Goals
     */
    public function storeOrUpdate(Request $request)
    {
        // Cache::flush();

        $data = $request->all();

        $companyId = $data['company'];
        $meantime = $data['meantime'];
        $type = $data['type'];
        $goals = $data['goals'];
        $currentUserId = auth()->id();

        $now = now();

        foreach ($goals as $departmentId => $goalData) {
            $goalValue = onlyNumber($goalData);

            $exists = DB::connection($this->connection)->table('wlsm_goals')
                ->where('company_id', $companyId)
                ->where('department_id', $departmentId)
                ->where('meantime', $meantime)
                ->where('type', $type)
                ->exists();

            if ($goalValue <= 0) {
                if ($exists) {
                    // Delete existing record
                    DB::connection($this->connection)->table('wlsm_goals')
                        ->where('company_id', $companyId)
                        ->where('department_id', $departmentId)
                        ->where('meantime', $meantime)
                        ->where('type', $type)
                        ->delete();
                }
            } else {
                if ($exists) {
                    // Update existing record
                    DB::connection($this->connection)->table('wlsm_goals')
                        ->where('company_id', $companyId)
                        ->where('department_id', $departmentId)
                        ->where('meantime', $meantime)
                        ->where('type', $type)
                        ->update([
                            'user_id' => $currentUserId,
                            'goal_value' => $goalValue,
                        ]);
                } else {
                    // Insert new record
                    DB::connection($this->connection)->table('wlsm_goals')->insert([
                        'user_id' => $currentUserId,
                        'company_id' => $companyId,
                        'department_id' => $departmentId,
                        'meantime' => $meantime,
                        'type' => $type,
                        'goal_value' => $goalValue,
                        'created_at' => $now,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Goals updated successfully!']);
    }

    private function calculateStartAndEndDate($meantime)
    {
        if ($meantime == 'today') {
            $startDate = Carbon::today();
            $endDate = Carbon::today();
        } elseif ( strlen($meantime) > 7 ) {
            $explodeMeantime = explode(' até ', $meantime);

            if (is_array($explodeMeantime) && count($explodeMeantime) == 2) {
                list($year1, $month1) = explode('-', $explodeMeantime[0]);
                list($year2, $month2) = explode('-', $explodeMeantime[1]);

                $startDate = Carbon::createFromDate($year1, $month1, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year2, $month2, 1)->endOfMonth();
            } else {
                list($year, $month) = explode('-', $meantime);

                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            }
        } elseif ( strlen($meantime) == 7 ) {
            list($year, $month) = explode('-', $meantime);

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        } else {
            $meantime = date('Y-m');

            list($year, $month) = explode('-', $meantime);

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        }

        return [$startDate, $endDate];
    }

    private function getSalesData($startDate, $endDate, $selectedCompanies, $selectedDepartments)
    {
        // Query for sales data
        $query = DB::connection($this->connection)
            ->table('wlsm_sales as sales')
            ->join('wlsm_companies as companies', 'sales.company_id', '=', 'companies.company_id')
            ->join('wlsm_departments as departments', 'sales.department_id', '=', 'departments.department_id')
            ->select(
                'sales.company_id',
                'sales.department_id',
                'sales.date_sale as meantime',
                DB::raw('SUM(sales.net_value) as total_net_value'),
                DB::raw('ANY_VALUE(companies.company_alias) as company_alias'),
                DB::raw('ANY_VALUE(departments.department_alias) as department_alias')
            )
            ->whereBetween('sales.date_sale', [$startDate, $endDate])
            ->where('companies.status', 1)
            ->where('departments.status', 1);

        if ($selectedCompanies) {
            $query->whereIn('sales.company_id', $selectedCompanies);
        }

        if ($selectedDepartments) {
            $query->whereIn('sales.department_id', $selectedDepartments);
        }

        return $query->groupBy('sales.company_id', 'sales.department_id', 'meantime')
            ->orderBy('company_id')
            ->orderBy('department_alias')
            ->orderBy('meantime')
            ->get();
    }

    private function getGoalsData($startDate, $endDate, $selectedCompanies, $selectedDepartments)
    {
        // Query for goals data
        $query = DB::connection($this->connection)
            ->table('wlsm_goals as goals')
            ->join('wlsm_companies as companies', 'goals.company_id', '=', 'companies.company_id')
            ->join('wlsm_departments as departments', 'goals.department_id', '=', 'departments.department_id')
            ->select(
                'goals.company_id',
                'goals.department_id',
                'goals.meantime',
                DB::raw('SUM(goals.goal_value) as total_goal_value'),
                DB::raw('ANY_VALUE(companies.company_alias) as company_alias'),
                DB::raw('ANY_VALUE(departments.department_alias) as department_alias')
            )
            ->whereBetween('goals.meantime', [date('Y-m', strtotime($startDate)), date('Y-m', strtotime($endDate))])
            ->where('companies.status', 1)
            ->where('departments.status', 1)
            ->where('goals.type', 'sales');

        if ($selectedCompanies) {
            $query->whereIn('goals.company_id', $selectedCompanies);
        }

        if ($selectedDepartments) {
            $query->whereIn('goals.department_id', $selectedDepartments);
        }

        return $query->groupBy('goals.company_id', 'goals.department_id', 'meantime')
            ->orderBy('company_id')
            ->orderBy('department_alias')
            ->orderBy('meantime')
            ->get();
    }

    private function processResults($resultsSales, $resultsGoals, $meantime)
    {
        // Process the results
        $data = [];
        $totalGoalsByMonth = [];
        $totalSalesByMonth = [];

        if (strlen($meantime) == 7){
            $dateFormat = 'd/m/Y';

            /**
             * if a date is missing or dont exists yeat from data, it adds with a 0 value
             */
            $firstOfMonth = Carbon::createFromFormat('Y-m', $meantime)->startOfMonth();
            $endOfMonth = Carbon::createFromFormat('Y-m', $meantime)->endOfMonth();
            $currentDate = $firstOfMonth->copy();

            while ($currentDate->lte($endOfMonth)) {
                $formattedDate = $currentDate->format($dateFormat);
                if (!isset($totalSalesByMonth[$formattedDate])) {
                    $totalSalesByMonth[$formattedDate] = 0;
                }
                if (!isset($totalGoalsByMonth[$formattedDate])) {
                    $totalGoalsByMonth[$formattedDate] = 0;
                }
                $currentDate->addDay();
            }
        }else{
            $dateFormat = 'F/Y';
        }

        foreach ($resultsSales as $result) {
            $companyId = $result->company_id;
            $departmentId = $result->department_id;

            if (!isset($data[$companyId])) {
                $data[$companyId] = [];
            }

            if (!isset($data[$companyId][$departmentId])) {
                $data[$companyId][$departmentId] = [
                    'sales' => 0,
                    'goal' => 0,
                ];
            }

            $data[$companyId][$departmentId]['sales'] += $result->total_net_value;

            $yearMonth = Carbon::createFromDate($result->meantime, 1)->format($dateFormat);

            if (!isset($totalSalesByMonth[$yearMonth])) {
                $totalSalesByMonth[$yearMonth] = 0;
            }
            $totalSalesByMonth[$yearMonth] += $result->total_net_value;
        }

        foreach ($resultsGoals as $result) {
            $companyId = $result->company_id;
            $departmentId = $result->department_id;

            if (!isset($data[$companyId])) {
                $data[$companyId] = [];
            }

            if (!isset($data[$companyId][$departmentId])) {
                $data[$companyId][$departmentId] = [
                    'sales' => 0,
                    'goal' => 0,
                ];
            }

            $data[$companyId][$departmentId]['goal'] += $result->total_goal_value;

            $yearMonth = Carbon::createFromDate($result->meantime, 1)->format($dateFormat);

            if (!isset($totalGoalsByMonth[$yearMonth])) {
                $totalGoalsByMonth[$yearMonth] = 0;
            }
            $totalGoalsByMonth[$yearMonth] += $result->total_goal_value;
        }

        return [$data, $totalSalesByMonth, $totalGoalsByMonth];
    }

    /**
     * Emoji Chart Default Mode and Slide Mode
     */
    private function getGoalAndSalesData($startDate, $endDate, $selectedCompanies, $selectedDepartments)
    {
        // Query for old data
        $query = DB::connection($this->connection)
            ->table('wlsm_sales as sales')
            ->join('wlsm_companies as companies', 'sales.company_id', '=', 'companies.company_id')
            ->join('wlsm_departments as departments', 'sales.department_id', '=', 'departments.department_id')
            ->leftJoin(DB::raw('(SELECT company_id, department_id, SUM(goal_value) AS goal_value
                    FROM wlsm_goals
                    WHERE meantime BETWEEN "' . date('Y-m', strtotime($startDate)) . '" AND "' . date('Y-m', strtotime($endDate)) . '"
                    AND type = "sales"
                    GROUP BY company_id, department_id) as goals'),
                function ($join) {
                    $join->on('goals.company_id', '=', 'sales.company_id')
                        ->on('goals.department_id', '=', 'sales.department_id');
                })
            ->select(
                'sales.company_id',
                'sales.department_id',
                DB::raw('SUM(sales.net_value) as total_net_value'),
                'goals.goal_value',
                DB::raw('ANY_VALUE(companies.company_alias) as company_alias'),
                DB::raw('ANY_VALUE(departments.department_alias) as department_alias')
            )
            ->whereBetween('sales.date_sale', [$startDate, $endDate])
            ->where('companies.status', 1)
            ->where('departments.status', 1);

        if ($selectedCompanies) {
            $query->whereIn('sales.company_id', $selectedCompanies);
        }

        if ($selectedDepartments) {
            $query->whereIn('sales.department_id', $selectedDepartments);
        }

        $results = $query->groupBy('sales.company_id', 'sales.department_id')
            ->orderBy('company_id')
            ->orderBy('department_alias')
            ->get();

        $data = [];
        foreach ($results as $result) {
            $companyId = $result->company_id;
            $departmentId = $result->department_id;

            if (!isset($data[$companyId])) {
                $data[$companyId] = [];
            }

            $data[$companyId][$departmentId] = [
                'sales' => isset($result->total_net_value) ? $result->total_net_value : '',
                'goal' => isset($result->goal_value) ? $result->goal_value : '',
            ];
        }

        return $data;
    }

    /**
     * Left widget
     */
    public function getDepartmentData($data)
    {
        // Initialize an empty array to store processed department data.
        $departments = [];

        // Loop through each company's data.
        foreach ($data as $companyId => $companyData) {
            // Loop through each department's data within the company.
            foreach ($companyData as $departmentId => $departmentData) {
                // Extract sales and goal values for the department.
                $sales = $departmentData['sales'];
                $goal = $departmentData['goal'];

                // Calculate progress as a percentage of sales over goal.
                // If goal or sales is zero, progress is set to zero to avoid division by zero error.
                $progress = $goal > 0 && $sales > 0 ? ($sales / $goal) * 100 : 0;

                // Determine the color of the progress bar based on the progress percentage.
                $color = 'theme'; // Default color
                if ($progress < 25) {
                    $color = 'danger';
                } elseif ($progress < 50) {
                    $color = 'warning';
                } elseif ($progress < 75) {
                    $color = 'info';
                } elseif ($progress < 99) {
                    $color = 'success';
                }

                // Create a tooltip with formatted goal, sales, and progress values.
                $tooltip = "Meta: " . brazilianRealFormat($goal, 0) . "<br>";
                $tooltip .= "Vendas: " . brazilianRealFormat($sales, 0) . "<br>";
                $tooltip .= "Progresso: " . numberFormat($progress, 2) . '%';

                // If the department is not already in the array, add it with calculated values.
                if (!isset($departments[$departmentId])) {
                    $departments[$departmentId] = (object) [
                        'name' => getDepartmentNameById($departmentId),
                        'progress' => $progress,
                        'color' => $color,
                        'tooltip' => $tooltip,
                        'sales' => $sales,
                        'goal' => $goal,
                    ];
                } else {
                    // If the department is already in the array, update the values.
                    $departments[$departmentId]->sales += $sales;
                    $departments[$departmentId]->goal += $goal;
                    $departments[$departmentId]->progress = intval($departments[$departmentId]->sales) > 0 && intval($departments[$departmentId]->goal) > 0 ? ($departments[$departmentId]->sales / $departments[$departmentId]->goal) * 100 : 0;
                    $departments[$departmentId]->tooltip = "Meta: " . brazilianRealFormat($departments[$departmentId]->goal, 0) . "<br>Vendas: " . brazilianRealFormat($departments[$departmentId]->sales, 0) . "<br>Progresso: " . numberFormat($departments[$departmentId]->progress, 2) . '%';
                }
            }// foreach
        }// foreach

        // Return the processed department data.
        return $departments;
    }

    /**
     * Chart Area and Table listing
     */
    public function analyticMode(Request $request)
    {
        // Get the current user
        $user = auth()->user();

        // Get the current analytics mode from the user's metadata
        $analyticMode = UserMeta::getUserMeta($user->id, 'analytic-mode');

        // Toggle the analytics mode
        $newAnalyticsMode = $analyticMode == 'on' ? 'off' : 'on';

        // Update the analytics mode in the user's metadata
        UserMeta::updateUserMeta($user->id, 'analytic-mode', $newAnalyticsMode);

        // Update the slide mode in the user's metadata
        if($newAnalyticsMode == 'on'){
            UserMeta::updateUserMeta($user->id, 'slide-mode', false);
        }

        // Return the new analytics mode
        return response()->json(['analyticMode' => $newAnalyticsMode]);
    }

    /**
     * Emoji ChartSlide Mode
     */
    public function slideMode(Request $request)
    {
        // Get the current user
        $user = auth()->user();

        // Get the current slide mode from the user's metadata
        $slideMode = UserMeta::getUserMeta($user->id, 'slide-mode');

        // Toggle the slide mode
        $newSlideMode = $slideMode == 'on' ? 'off' : 'on';

        // Update the slide mode in the user's metadata
        UserMeta::updateUserMeta($user->id, 'slide-mode', $newSlideMode);

        // Update the analytics mode in the user's metadata
        if($newSlideMode == 'on'){
            UserMeta::updateUserMeta($user->id, 'analytic-mode', false);
        }

        // Return the new slide mode
        return response()->json(['slideMode' => $newSlideMode]);
    }

    /**
     * Emoji Chart Default Mode
     */
    public function defaultMode(Request $request)
    {
        // Get the current user
        $user = auth()->user();

        UserMeta::updateUserMeta($user->id, 'slide-mode', false);

        UserMeta::updateUserMeta($user->id, 'analytic-mode', false);

        // Return the new slide mode
        return response()->json(['defaultMode' => true]);
    }

}
