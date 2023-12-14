@extends('layouts.master')
@section('title')
    @lang('translation.users')
@endsection
@section('content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h4 class="mb-sm-0 font-size-18">Equipe</h4>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col">
                            <div class="search-box">
                                <input type="text" class="form-control" id="searchMemberList" placeholder="Pesquisar por nome...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-sm-auto ms-auto">
                            <div class="list-grid-nav hstack gap-1">
                                <button type="button" id="grid-view-button" class="btn btn-soft-info nav-link btn-icon fs-14 active filter-button"><i class="ri-grid-fill"></i></button>
                                <button type="button" id="list-view-button" class="btn btn-soft-info nav-link  btn-icon fs-14 filter-button"><i class="ri-list-unordered"></i></button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
            </div>
            <div id="teamlist">
                <div class="team-list grid-view-filter row" id="team-member-list">
                    @php
                    // Sort the users by name in descending order and send with status 0 to the end
                    $users = $users->toArray();

                    usort($users, function ($a, $b) {
                        if ($a['status'] == $b['status']) {
                            return strcmp($a['name'], $b['name']);
                        }
                        return $b['status'] - $a['status'];
                    });

                    $users = collect($users);
                    @endphp

                    @foreach ($users as $user)
                        @php
                            $id = $user['id'];
                            $capabilities = $user['capabilities'] ? json_decode($user['capabilities'], true) : [];
                            $status = $user['status'];
                            $avatar = $user['avatar'];
                            $cover = $user['cover'];
                            $name = $user['name'];
                            $role = \App\Models\User::getRoleName($user['role']);
                        @endphp
                        @include('settings.users-card')
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-auto mb-4">
            <div class="card h-100 rounded-2 mb-0">
                <div class="card-body p-3">
                    <div class="tasks-wrapper overflow-auto" id="load-surveys-activities">
                        <div class="text-center">
                            <div class="spinner-border text-theme mt-3 mb-3" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var uploadAvatarURL = "{{ route('uploadAvatarURL') }}";
        var uploadCoverURL = "{{ route('uploadCoverURL') }}";
        var getUserModalContentURL = "{{ route('getUserModalContentURL') }}";
        var settingsUsersStoreURL = "{{ route('settingsUsersStoreURL') }}";
        var settingsUsersUpdateURL = "{{ route('settingsUsersUpdateURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/settings-users.js') }}" type="module"></script>

    <script>
        var surveysIndexURL = "{{ route('surveysIndexURL') }}";
        var surveysCreateURL = "{{ route('surveysCreateURL') }}";
        var surveysEditURL = "{{ route('surveysEditURL') }}";
        var surveysChangeStatusURL = "{{ route('surveysChangeStatusURL') }}";
        var surveysShowURL = "{{ route('surveysShowURL') }}";
        var surveysStoreOrUpdateURL = "{{ route('surveysStoreOrUpdateURL') }}";
        var getRecentActivitiesURL = "{{ route('getRecentActivitiesURL') }}";
    </script>
    <script src="{{ URL::asset('build/js/surveys.js') }}" type="module"></script>
@endsection
