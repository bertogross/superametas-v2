<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GoalSalesController extends Controller
{
    protected $connection = 'smAppTemplate';

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

        $query = DB::connection($this->connection)->table('wlsm_goal_sales')
            ->join('wlsm_companies', 'wlsm_goal_sales.company_id', '=', 'wlsm_companies.company_id')
            ->join('wlsm_departments', 'wlsm_goal_sales.department_id', '=', 'wlsm_departments.department_id')
            ->select('wlsm_goal_sales.company_id', 'wlsm_goal_sales.department_id', DB::raw('SUM(net_value) as total_net_value'))
            ->whereBetween('wlsm_goal_sales.date_sale', [$startDate, $endDate])
            ->where('wlsm_companies.status', 1)
            ->where('wlsm_departments.status', 1);

        // Handle the selected companies filter
        if ($selectedCompanies) {
            $query->whereIn('wlsm_goal_sales.company_id', $selectedCompanies);
        }

        // Handle the selected departments filter
        if ($selectedDepartments) {
            $query->whereIn('wlsm_goal_sales.department_id', $selectedDepartments);
        }

        $goalSales = $query->groupBy('wlsm_goal_sales.company_id', 'wlsm_goal_sales.department_id')
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

        return view('goal-sales', compact('result', 'companies', 'departments'));
    }
}
