<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyAssignments;
use App\Models\SurveyAudit;
use Illuminate\Http\Request;

class SurveysAuditController extends Controller
{
    public function index(Request $request, $userId = null)
    {
        $currentUser = auth()->user();

        $userId = request('user') ?? null;
        $status = request('status') ?? null;
        $filterCompanies = request('companies', []);
        $filterCreatedAt = request('created_at', '');

        $currentUserCapabilities = $currentUser->capabilities ? json_decode($currentUser->capabilities, true) : [];

        $createdAtRange = [];
        if (!empty($filterCreatedAt)) {
            $dateRange = explode(' até ', $filterCreatedAt);

            if (count($dateRange) === 2) {
                // Date range provided
                $startDate = Carbon::createFromFormat('d/m/Y', trim($dateRange[0]))->startOfDay()->format('Y-m-d H:i:s');
                $endDate = Carbon::createFromFormat('d/m/Y', trim($dateRange[1]))->endOfDay()->format('Y-m-d H:i:s');
            } else {
                // Single date provided
                $startDate = Carbon::createFromFormat('d/m/Y', trim($filterCreatedAt))->startOfDay()->format('Y-m-d H:i:s');
                $endDate = Carbon::createFromFormat('d/m/Y', trim($filterCreatedAt))->endOfDay()->format('Y-m-d H:i:s');
            }
            $createdAtRange = [$startDate, $endDate];
        }

        $query = SurveyAssignments::whereNotNull('survey_assignments.auditor_status')
        ->whereNotNull('survey_assignments.auditor_id')
        ->where('survey_assignments.auditor_status', '!=', 'bypass')
        ->join('surveys', 'survey_assignments.survey_id', '=', 'surveys.id')
        ->select(
            'surveys.title',
            'surveys.template_id',
            'surveys.distributed_data',
            'survey_assignments.id',
            'survey_assignments.survey_id',
            'survey_assignments.company_id',
            'survey_assignments.surveyor_id',
            'survey_assignments.auditor_id',
            'survey_assignments.surveyor_status',
            'survey_assignments.auditor_status',
            'survey_assignments.created_at',
            'survey_assignments.updated_at',
        )
        ->when(!empty($filterCompanies), function ($query) use ($filterCompanies) {
            $query->whereIn('survey_assignments.company_id', $filterCompanies);
        })
        ->when(!empty($createdAtRange), function ($query) use ($createdAtRange) {
            $query->whereBetween('survey_assignments.created_at', $createdAtRange);
        });

        if ($userId) {
            $query->where('survey_assignments.auditor_id', $userId);
        }

        if($status){
            $query->where('auditor_status', $status);
        }

        $dataDone = $query->orderBy('updated_at', 'desc')->paginate(10);

        $getSurveyAssignmentStatusTranslations = SurveyAssignments::getSurveyAssignmentStatusTranslations();

        $dateRange = SurveyAssignments::getAssignmentDateRange();
        $firstDate = $dateRange['first_date'] ?? null;
        $lastDate = $dateRange['last_date'] ?? null;

        $dataAvailable = SurveyAssignments::where('surveyor_status', 'completed')
            ->whereNull('auditor_status')
            ->orderBy('updated_at', 'desc')
            ->limit(100)
            ->get()
            ->toArray();

        return view('surveys.audits.index', compact(
            'dataDone',
            'dataAvailable',
            'getSurveyAssignmentStatusTranslations',
            'firstDate',
            'lastDate',
            'currentUserCapabilities'
        ));
    }
}