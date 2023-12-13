@extends('layouts.master')
@section('title')
    {{ $user->name }}
@endsection
@section('css')
@endsection
@section('content')
    @php
        $profileUserId = $user->id;
        $phone = getUserMeta($profileUserId, 'phone');
        $phone = formatPhoneNumber($phone);
        //appPrintR($assignmentData);
        //appPrintR($auditorData);
        //appPrintR($filteredStatuses);
        //appPrintR($assignmentData);
    @endphp
    <div class="profile-foreground position-relative mx-n4 mt-n5">
        <div class="profile-wid-bg">
            <img
            @if( empty(trim($user->cover)))
                src="{{URL::asset('build/images/small/img-9.jpg')}}"
            @else
                src="{{ URL::asset('storage/' . $user->cover) }}"
            @endif
            alt="cover" class="profile-wid-img" />
        </div>
    </div>

    <div class="pt-5 mb-2 mb-lg-1 pb-lg-4 profile-wrapper">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg profile-user position-relative d-inline-block">
                    <img id="avatar-img"
                    @if( empty(trim($user->avatar)) )
                        src="{{URL::asset('build/images/users/user-dummy-img.jpg')}}"
                    @else
                        src="{{ URL::asset('storage/' . $user->avatar) }}"
                    @endif
                    alt="avatar" class="img-thumbnail rounded-circle" />
                    @if($user->id == auth()->id())
                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right" title="Alterar Avatar">
                            <input class="d-none" name="avatar" id="member-image-input" type="file" accept="image/jpeg">
                            <label for="member-image-input" class="profile-photo-edit avatar-xs">
                                <span class="avatar-title rounded-circle bg-light text-body">
                                    <i class="ri-camera-fill"></i>
                                </span>
                            </label>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1 text-shadow">{{ $user->name }}</h3>
                    <p class="text-white mb-2 text-shadow">{{ $roleName }}</p>
                    <div class="hstack text-white gap-1">
                        <div class="me-2 text-shadow">
                            <i class="ri-mail-line text-white fs-16 align-middle me-2"></i>{{ $user->email }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-auto order-last order-lg-0">
                <div class="row text text-white-50 text-center">
                    {{--
                    <div class="col-lg-6 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">24.3K</h4>
                            <p class="fs-14 mb-0">Followers</p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">1.3K</h4>
                            <p class="fs-14 mb-0">Following</p>
                        </div>
                    </div>
                    --}}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header align-items-center d-flex">
            <h5 class="card-title mb-0 flex-grow-1"><i class="ri-todo-fill fs-16 align-bottom text-theme me-2"></i>Tarefas</h5>
        </div>
        <div class="card-body h-100" style="min-height: 150px">
            @if ( $assignmentData && is_array($assignmentData) )
                <div class="tasks-board mb-0 position-relative" id="kanbanboard">
                    @foreach ($filteredStatuses as $key => $status)
                        @php
                            $filteredSurveyorData = [];
                            $filteredAuditorData = [];

                            array_walk($assignmentData, function ($item) use (&$filteredSurveyorData, $key, $profileUserId) {
                                if ($item['surveyor_status'] == $key && $item['surveyor_id'] == $profileUserId) {
                                    $filteredSurveyorData[] = $item;
                                }
                            });

                            array_walk($assignmentData, function ($item) use (&$filteredAuditorData, $key, $profileUserId) {
                                if ($item['auditor_status'] == $key && $item['auditor_id'] == $profileUserId) {
                                    $filteredAuditorData[] = $item;
                                }
                            });

                            $countFilteredSurveyorData = is_array($filteredSurveyorData) ? count($filteredSurveyorData) : 0;

                            $countFilteredAuditorData = is_array($filteredAuditorData) ? count($filteredAuditorData) : 0;

                            $countTotal = $countFilteredSurveyorData + $countFilteredAuditorData;
                        @endphp

                        <div class="tasks-list p-2 {{-- in_array($key, ['waiting', 'auditing', 'pending', 'completed', 'in_progress', 'losted']) && $countTotal < 1 ? 'd-none' : '' --}} {{ in_array($key, ['waiting', 'auditing', 'losted']) && $countTotal < 1 ? 'd-none' : '' }}">
                            <div class="d-flex mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="fs-14 text-uppercase fw-semibold mb-1">
                                        <span data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="{{$status['label']}}" data-bs-content="{{$status['description']}}">
                                            {{$status['label']}}
                                        </span>
                                        <small class="badge bg-{{$status['color']}} align-bottom ms-1 totaltask-badge">
                                            {{ $countTotal }}
                                        </small>
                                    </h6>
                                    <p class="text-muted mb-2">{{$status['description']}}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    {{--
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <span class="fw-medium text-muted fs-12">Priority<i
                                                    class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Priority</a>
                                            <a class="dropdown-item" href="#">Date Added</a>
                                        </div>
                                    </div>
                                    --}}
                                </div>
                            </div>
                            <div data-simplebar class="tasks-wrapper">
                                <div id="{{$key}}-task" class="tasks mb-2">
                                    @include('surveys.layouts.profile-task-card', [
                                        'status' => $status,
                                        'statusKey' => $key,
                                        'designated' => 'auditor',
                                        'data' => $filteredAuditorData
                                    ])

                                    @include('surveys.layouts.profile-task-card', [
                                        'status' => $status,
                                        'statusKey' => $key,
                                        'designated' => 'surveyor',
                                        'data' => $filteredSurveyorData
                                    ])
                                </div>
                            </div>
                        </div>
                        <!--end tasks-list-->
                    @endforeach

                    {{--
                    @if ($countTasks === 0)
                        <div class="alert alert-info alert-dismissible alert-label-icon label-arrow fade show ms-auto me-auto" role="alert">
                            <i class="ri-alert-line label-icon"></i> Tarefas ainda não lhe foram atribuídas
                        </div>
                    @endif
                    --}}
                </div>
            @else
                <div class="alert alert-info alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                    <i class="ri-alert-line label-icon"></i> Tarefas ainda não lhe foram delegadas
                </div>
            @endif
        </div>
    </div>

@endsection
@section('script')

<script>
    var surveysIndexURL = "{{ route('surveysIndexURL') }}";
    var surveysCreateURL = "{{ route('surveysCreateURL') }}";
    var surveysEditURL = "{{ route('surveysEditURL') }}";
    var surveysChangeStatusURL = "{{ route('surveysChangeStatusURL') }}";
    var surveysShowURL = "{{ route('surveysShowURL') }}";
    var surveysStoreOrUpdateURL = "{{ route('surveysStoreOrUpdateURL') }}";
    var formSurveyorAssignmentURL = "{{ route('formSurveyorAssignmentURL') }}";
    var formAuditorAssignmentURL = "{{ route('formAuditorAssignmentURL') }}";
    var changeAssignmentSurveyorStatusURL = "{{ route('changeAssignmentSurveyorStatusURL') }}";
    var changeAssignmentAuditorStatusURL = "{{ route('changeAssignmentAuditorStatusURL') }}";
    var profileShowURL = "{{ route('profileShowURL') }}";
</script>
<script src="{{ URL::asset('build/js/surveys.js') }}" type="module"></script>

<script>
    var formSurveyorAssignmentURL = "{{ route('formSurveyorAssignmentURL') }}";
    var changeAssignmentSurveyorStatusURL = "{{ route('changeAssignmentSurveyorStatusURL') }}";
    var responsesSurveyorStoreOrUpdateURL = "{{ route('responsesSurveyorStoreOrUpdateURL') }}";
    var profileShowURL = "{{ route('profileShowURL') }}";
</script>
<script src="{{ URL::asset('build/js/surveys-surveyor.js') }}" type="module"></script>

<script>
    var profileShowURL = "{{ route('profileShowURL') }}";
    var changeAssignmentAuditorStatusURL = "{{ route('changeAssignmentAuditorStatusURL') }}";
    var responsesAuditorStoreOrUpdateURL = "{{ route('responsesAuditorStoreOrUpdateURL') }}";
</script>
<script src="{{ URL::asset('build/js/surveys-auditor.js') }}" type="module"></script>

<script type="module">
    import { attachImage } from '{{ URL::asset('build/js/settings-attachments.js') }}';

    var uploadAvatarURL = "{{ route('uploadAvatarURL') }}";

    attachImage("#member-image-input", "#avatar-img", uploadAvatarURL, false);
</script>

<script>
    // Auto refresh page
    setInterval(function() {
        window.location.reload();// true to cleaning cache
    }, 600000); // 600000 milliseconds = 10 minutes
</script>
@endsection
