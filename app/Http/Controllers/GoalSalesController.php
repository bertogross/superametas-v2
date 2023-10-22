<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Post;

class GoalSalesController extends Controller
{
    protected $connection = 'smAppTemplate';

    /*public function index(Request $request)
    {
        $meantime = $request->input('meantime');
        $meantime = empty($meantime) ? date('Y-m') : $meantime;
        $customMeantime = $request->input('custom_meantime');
        $selectedCompanies = $request->input('companies');
        $selectedDepartments = $request->input('departments');

        if ($meantime == 'today') {
            $startDate = Carbon::today();
            $endDate = Carbon::today();
        } elseif ($meantime == 'custom' && !empty($customMeantime)) {
            $explodeCustomMeantime = explode(' até ', $customMeantime);
            if(is_array($explodeCustomMeantime) && count($explodeCustomMeantime) == 2){
                $customMeantime1 = $explodeCustomMeantime[0];
                $customMeantime2 = $explodeCustomMeantime[1];

                list($year1, $month1) = explode('-', $customMeantime1);
                list($year2, $month2) = explode('-', $customMeantime2);

                $startDate = Carbon::createFromDate($year1, $month1, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year2, $month2, 1)->endOfMonth();
            }else{
                list($year, $month) = explode('-', $customMeantime);
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            }
        } else {
            if($meantime == 'custom' && empty($customMeantime)){
                $meantime = date('Y-m');
            }
            list($year, $month) = explode('-', $meantime);
            list($year, $month) = explode('-', $meantime);

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        }

        $query = DB::connection($this->connection)->table('wlsm_sales')
            ->join('wlsm_companies', 'wlsm_sales.company_id', '=', 'wlsm_companies.company_id')
            ->join('wlsm_departments', 'wlsm_sales.department_id', '=', 'wlsm_departments.department_id')
            ->select('wlsm_sales.company_id', 'wlsm_sales.department_id', DB::raw('SUM(net_value) as total_net_value'))
            ->whereBetween('wlsm_sales.date_sale', [$startDate, $endDate])
            ->where('wlsm_companies.status', 1)
            ->where('wlsm_departments.status', 1);

        // Handle the selected companies filter
        if ($selectedCompanies) {
            $query->whereIn('wlsm_sales.company_id', $selectedCompanies);
        }

        // Handle the selected departments filter
        if ($selectedDepartments) {
            $query->whereIn('wlsm_sales.department_id', $selectedDepartments);
        }

        $goalSales = $query->groupBy('wlsm_sales.company_id', 'wlsm_sales.department_id')
            ->orderBy(DB::raw('MAX(wlsm_companies.company_alias)'))
            ->orderBy(DB::raw('MAX(wlsm_departments.department_alias)'))
            ->get();

        $companies = $goalSales->unique('company_id')->pluck('company_id');
        $departments = $goalSales->unique('department_id')->pluck('department_id');

        $result = [];
        foreach ($departments as $department) {
            $row = ['department_id' => $department];
            foreach ($companies as $company) {
                $value = $goalSales->where('company_id', $company)->where('department_id', $department)->first();
                $row[$company] = $value ? $value->total_net_value : 0;
            }
            $result[] = $row;
        }

        $generalRow = ['department_id' => 'general'];
        foreach ($companies as $company) {
            $generalRow[$company] = array_sum(array_column($result, $company));
        }
        $result[] = $generalRow;

        return view('goal-sales.index', compact('result', 'companies', 'departments'));
    }*/
    public function index(Request $request)
    {
        $meantime = $request->input('meantime');
        $meantime = empty($meantime) ? date('Y-m') : $meantime;
        $customMeantime = $request->input('custom_meantime');
        $selectedCompanies = $request->input('companies');
        $selectedDepartments = $request->input('departments');

        if ($meantime == 'today') {
            $startDate = Carbon::today();
            $endDate = Carbon::today();
        } elseif ($meantime == 'custom' && !empty($customMeantime)) {
            $explodeCustomMeantime = explode(' até ', $customMeantime);
            if(is_array($explodeCustomMeantime) && count($explodeCustomMeantime) == 2){
                $customMeantime1 = $explodeCustomMeantime[0];
                $customMeantime2 = $explodeCustomMeantime[1];

                list($year1, $month1) = explode('-', $customMeantime1);
                list($year2, $month2) = explode('-', $customMeantime2);

                $startDate = Carbon::createFromDate($year1, $month1, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year2, $month2, 1)->endOfMonth();
            }else{
                list($year, $month) = explode('-', $customMeantime);
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            }
        } else {
            if($meantime == 'custom' && empty($customMeantime)){
                $meantime = date('Y-m');
            }
            list($year, $month) = explode('-', $meantime);
            list($year, $month) = explode('-', $meantime);

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        }

        $query = DB::connection($this->connection)
            ->table('wlsm_sales as sales')
            ->join('wlsm_companies as companies', 'sales.company_id', '=', 'companies.company_id')
            ->join('wlsm_departments as departments', 'sales.department_id', '=', 'departments.department_id')
            ->leftJoin(DB::raw('(SELECT company_id, department_id, SUM(goal_value) AS goal_value
                    FROM wlsm_goals
                    WHERE meantime BETWEEN "'.date('Y-m', strtotime($startDate)).'" AND "'.date('Y-m', strtotime($endDate)).'"
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

        // Handle the selected companies filter
        if ($selectedCompanies) {
            $query->whereIn('sales.company_id', $selectedCompanies);
        }

        // Handle the selected departments filter
        if ($selectedDepartments) {
            $query->whereIn('sales.department_id', $selectedDepartments);
        }

        $results = $query->groupBy('sales.company_id', 'sales.department_id')
            ->orderBy('company_alias')
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

        return view('goal-sales.index', compact('data'));
    }




    public function storeOrUpdateGoals(Request $request)
    {
        $data = $request->all();

        $companyId = $data['company'];
        $meantime = $data['meantime'];
        $type = $data['type'];
        $goals = $data['goals'];
        $userId = auth()->id();

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
                            'user_id' => $userId,
                            'goal_value' => $goalValue,
                            'updated_at' => $now,
                        ]);
                } else {
                    // Insert new record
                    DB::connection($this->connection)->table('wlsm_goals')->insert([
                        'user_id' => $userId,
                        'company_id' => $companyId,
                        'department_id' => $departmentId,
                        'meantime' => $meantime,
                        'type' => $type,
                        'goal_value' => $goalValue,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Goals updated successfully!']);
    }





    /**
     * Get the content for the modal settings.
     *
     * @param int|null $id The user's ID.
     * @return \Illuminate\View\View
     */
    public function getGoalSalesSettingsModalContent() {

        return view('goal-sales/settings-modal');

    }

    /**
     * Get the content for the modal edit.
     *
     * @param int|null $id The user's ID.
     * @return \Illuminate\View\View
     */
    public function getGoalSalesEditModalContent() {

        return view('goal-sales/edit-modal');

    }



}
