<?php

namespace App\Http\Controllers;

use App\Models\UserMeta;
use Illuminate\Http\Request;
use App\Models\SettingsDatabase;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SettingsUserController;

class SettingsDatabaseController extends Controller
{
    /**
     * Display the database settings page.
     * Retrieve departments and companies from the database and pass them to the view.
     */
    public function index() {
        $departments = DB::connection('smAppTemplate')
            ->table('wlsm_departments')
            ->orderBy('department_id', 'asc')
            ->get();

        $companies = DB::connection('smAppTemplate')
            ->table('wlsm_companies')
            ->orderBy('company_id', 'asc')
            ->get();

        return view('settings.database', compact('departments', 'companies'));
    }

    /**
     * Update the aliases and status of the departments.
     *
     * Validate the incoming request data, update the 'department_alias' and 'status' for each department
     * in the 'wlsm_departments' table, and redirect the user back to the previous page with a success message.
     */
    public function updateDepartments(Request $request)
    {
        // Update department aliases
        foreach ($request->aliases as $id => $alias) {
            DB::connection('smAppTemplate')
                ->table('wlsm_departments')
                ->where('id', $id)
                ->update(['department_alias' => $alias]);
        }

        // Update department status
        foreach ($request->status as $id => $status) {
            DB::connection('smAppTemplate')
                ->table('wlsm_departments')
                ->where('id', $id)
                ->update(['status' => $status ? 1 : 0]);
        }

        return redirect()->back()->with('active_tab', 'departments')->with('success', 'Departamentos atualizados');
    }

    /**
     * Update the aliases and status of the companies.
     *
     * Validate the incoming request data, update the 'company_alias' and 'status' for each company
     * in the 'wlsm_companies' table, and redirect the user back to the previous page with a success message.
     */
    public function updateCompanies(Request $request)
    {

        // Update company aliases
        foreach ($request->aliases as $id => $alias) {
            DB::connection('smAppTemplate')
                ->table('wlsm_companies')
                ->where('id', $id)
                ->update(['company_alias' => $alias]);
        }

        // Update company status
        foreach ($request->status as $id => $status) {
            DB::connection('smAppTemplate')
                ->table('wlsm_companies')
                ->where('id', $id)
                ->update(['status' => $status ? 1 : 0]);
        }

        // Get IDs of active companies
        $getActiveCompanies = getActiveCompanies();
        if (is_array($getActiveCompanies)) {
            $extractCompanyIds = array_column($getActiveCompanies, 'company_id');
        }

        // Create an instance of SettingsUserController and call updateUserMeta
        $userController = new SettingsUserController();
        $userController->updateUserMeta(1, 'companies', json_encode($extractCompanyIds));

        return redirect()->back()->with('active_tab', 'companies')->with('success', 'Empresas atualizadas');
    }






}
