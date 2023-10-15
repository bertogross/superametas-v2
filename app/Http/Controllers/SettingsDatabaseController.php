<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsDatabaseController extends Controller
{
    protected $connection = 'smAppTemplate';

    public function showSettingsDatabase() {
        $departments = DB::connection('smAppTemplate')
        ->table('wlsm_departments')
        ->orderBy('department_id', 'asc')
        ->get();

        $companies = DB::connection('smAppTemplate')
        ->table('wlsm_companies')
        ->orderBy('company_id', 'asc')
        ->get();

        return view('settings-database', compact('departments', 'companies'));
    }



    /**
     * Update the aliases of the departments.
     *
     * Validate the incoming request data, update the 'department_alias' for each department in the 'wlsm_departments' table,
     * and redirect the user back to the previous page with a success message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDepartments(Request $request)
    {
        //\Log::info($request->all());

        //dd($request->all());

        // Update aliases
        foreach ($request->aliases as $id => $alias) {
            DB::connection('smAppTemplate')
                ->table('wlsm_departments')
                ->where('id', $id)
                ->update(['department_alias' => $alias]);
        }

        // Update status
        foreach ($request->status as $id => $status) {
            DB::connection('smAppTemplate')
                ->table('wlsm_departments')
                ->where('id', $id)
                ->update(['status' => $status ? 1 : 0]);
        }

        return redirect()->back()->with('active_tab', 'departments')->with('success', 'Departamentos atualizados');
    }



    /**
     * Update the aliases of the Companies.
     *
     * Validate the incoming request data, update the 'company_alias' for each department in the 'wlsm_companies' table,
     * and redirect the user back to the previous page with a success message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCompanies(Request $request)
    {
        // Update aliases and other fields
        foreach ($request->aliases as $id => $alias) {
            DB::connection('smAppTemplate')
                ->table('wlsm_companies')
                ->where('id', $id)
                ->update(['company_alias' => $alias]);
        }

        // Update status
        foreach ($request->status as $id => $status) {
            DB::connection('smAppTemplate')
                ->table('wlsm_companies')
                ->where('id', $id)
                ->update(['status' => $status ? 1 : 0]);
        }

        return redirect()->back()->with('active_tab', 'companies')->with('success', 'Empresas atualizadas');
    }

}
