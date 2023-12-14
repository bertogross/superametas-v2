<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Survey;
use App\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SurveyAssignments;

class ProfileController extends Controller
{
    public function index($id = null)
    {
        if (!$id && auth()->check()) {
            $user = auth()->user();
        } else {
            $user = User::findOrFail($id);

            /*
            // IDEA: When Porfile is restrict, check if the authenticated user is the same as the user being viewed
            if($whenProfileIsRestrict){
                if(auth()->id() !== $user->id) {
                    abort(403, 'Unauthorized action.');
                }
            }
            */
        }

        $userId = $user->id;

        $roleName = \App\Models\User::getRoleName($user->role);

        $currentUser = auth()->user();
        $currentUserCapabilities = $currentUser->capabilities ? json_decode($currentUser->capabilities, true) : [];

        $assignmentData = SurveyAssignments::where(function ($query) use ($userId) {
            $query->where('surveyor_id', $userId)
                ->orWhere('auditor_id', $userId);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get()
            ->toArray();


        $filteredStatuses = SurveyAssignments::getSurveyAssignmentStatusTranslations();
        //$requiredKeys = ['waiting', 'new', 'pending', 'in_progress', 'auditing', 'completed', 'losted'];
        //$filteredStatuses = array_intersect_key($getSurveyAssignmentStatusTranslations, array_flip($requiredKeys));

        //$getAuthorizedCompanies = getAuthorizedCompanies();

        //$getActiveDepartments = getActiveDepartments();



        return view('profile.index', compact(
            'user',
            'roleName',
            'assignmentData',
            'filteredStatuses',
            'currentUserCapabilities'
            //'getAuthorizedCompanies',
            //'getActiveDepartments',
        ));

    }

    public function settings()
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect('/login'); // or wherever you want to redirect unauthenticated users
        }

        // Check if the authenticated user has ROLE_ADMIN
        if (Auth::user()->hasRole(User::ROLE_ADMIN)) {
            return redirect('/settings/account'); // Redirect admins to settings/account
        }

        // Load the view
        return view('profile/settings');
    }

    public function ChangeLayoutMode(Request $request){

        $currentUserId = auth()->id();

        $theme = $request->json('theme');

        UserMeta::updateOrCreate(
            ['user_id' => $currentUserId, 'meta_key' => 'theme'],
            ['meta_value' => $theme]
        );

        return response()->json([
            'success' => true,
            'message' => 'Layout modificado!'
        ]);

    }

}
