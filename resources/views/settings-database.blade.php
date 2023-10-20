@php
//phpinfo();
//exit;
@endphp
@extends('layouts.master')
@section('title')
    @lang('translation.your-erp')
@endsection
@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ url('settings') }}
        @endslot
        @slot('li_1')
            @lang('translation.settings')
        @endslot
        @slot('title')
            @lang('translation.your-erp')
        @endslot
    @endcomponent

    @include('components.alert-errors')

    @include('components.alert-success')

    <!-- resources/views/settings-database.blade.php -->

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-2">
                    <div class="nav nav-pills flex-column nav-pills-tab verti-nav-pills custom-verti-nav-pills nav-pills-theme" role="tablist" aria-orientation="vertical">
                        <a class="nav-link text-uppercase {{ session('active_tab') == 'departments' || session('active_tab') == '' ? 'active show' : '' }}" id="v-pills-departments-tab" data-bs-toggle="pill" href="#v-pills-departments" role="tab" aria-controls="v-pills-departments"
                            aria-selected="true">
                            Departamentos</a>
                        <a class="nav-link text-uppercase {{ session('active_tab') == 'companies' ? 'active show' : '' }}" id="v-pills-companies-tab" data-bs-toggle="pill" href="#v-pills-companies" role="tab" aria-controls="v-pills-companies"
                            aria-selected="false">
                            Empresas</a>
                        <a class="nav-link text-uppercase {{ session('active_tab') == 'synchronization' ? 'active show' : '' }}" id="v-pills-synchronization-tab" data-bs-toggle="pill" href="#v-pills-synchronization" role="tab" aria-controls="v-pills-synchronization"
                            aria-selected="false">
                            Sincronizacão</a>
                    </div>
                </div> <!-- end col-->
                <div class="col-lg-10">
                    <div class="tab-content text-muted mt-3 mt-lg-0">
                        <div class="tab-pane fade {{ session('active_tab') == 'departments' || session('active_tab') == '' ? 'active show' : '' }}" id="v-pills-departments" role="tabpanel" aria-labelledby="v-pills-departments-tab">
                            <form action="{{ route('departments.updateDepartments') }}" method="POST" autocomplete="off">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-theme float-end">Atualizar Departamentos</button>

                                <h2 class="text-body mb-2 h4">Departamentos</h2>
                                <p>Renomeie cada dos departamentos caso entenda que será necessário para fins de exibição em relatórios </p>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered table-sm mb-0">
                                        <thead class="table-light text-uppercase">
                                            <tr>
                                                <th width="65" class="text-center"></th>
                                                <th width="130">ID</th>
                                                <th>Departamento</th>
                                                <th>Alias</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($departments as $department)
                                                <tr>
                                                    <td class="align-middle">
                                                        <!-- Checkbox for Status Update -->
                                                        <div class="form-check form-switch form-switch-md form-switch-theme text-end">
                                                            <input type="hidden" name="status[{{ $department->id }}]" value="0">

                                                            <input type="checkbox"
                                                            class="form-check-input"
                                                            value="1"
                                                            name="status[{{ $department->id }}]"
                                                            data-id="{{ $department->id }}"
                                                            {{ $department->status == 1 ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle">
                                                        {{ e($department->department_id) }}
                                                    </td>
                                                    <td class="align-middle">
                                                        {!! $department->description !!}
                                                    </td>
                                                    <td class="align-middle">
                                                        <input type="text" name="aliases[{{ $department->id }}]" value="{{ empty($department->department_alias) ? e(strip_tags($department->description )) : e(strip_tags($department->department_alias)) }}" maxlength="100" class="form-control">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div><!--end tab-pane-->
                        <div class="tab-pane fade {{ session('active_tab') == 'companies' ? 'active show' : '' }}" id="v-pills-companies" role="tabpanel" aria-labelledby="v-pills-companies-tab">
                            <form action="{{ route('companies.updateCompanies') }}" method="POST" autocomplete="off">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-theme float-end">Atualizar Empresas</button>

                                <h2 class="text-body mb-2 h4">Empresas</h2>
                                <p>Renomeie cada das empresas caso entenda que será necessário para fins de exibição em relatórios </p>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered table-sm mb-0">
                                        <thead class="table-light text-uppercase">
                                            <tr>
                                                <th width="65" class="text-center"></th>
                                                <th width="130">ID</th>
                                                <th>Empresa</th>
                                                <th>Alias</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($companies as $company)
                                                <tr>
                                                    <td class="align-middle">
                                                        <!-- Checkbox for Status Update -->
                                                        <div class="form-check form-switch form-switch-md form-switch-theme text-end">
                                                            <input type="hidden" name="status[{{ $company->id }}]" value="0">

                                                            <input type="checkbox"
                                                            class="form-check-input"
                                                            name="status[{{ $company->id }}]" value="1"
                                                            data-id="{{ $company->id }}"
                                                            {{ $company->status == 1 ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle">
                                                        {{ e($company->company_id) }}
                                                    </td>
                                                    <td class="align-middle">
                                                        {!! $company->company_name !!}
                                                    </td>
                                                    <td class="align-middle">
                                                        <input type="text" name="aliases[{{ $company->id }}]" value="{{ empty($company->company_alias) ? e($company->company_name) : e($company->company_alias) }}" maxlength="100" class="form-control">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div><!--end tab-pane-->
                        <div class="tab-pane fade {{ session('active_tab') == 'synchronization' ? 'active show' : '' }}" id="v-pills-synchronization" role="tabpanel" aria-labelledby="v-pills-synchronization-tab">
                            <button type="button" id="btn-start-synchronization" class="btn btn-theme float-end">Sincronizar</button>

                            <h2 class="text-body mb-2 h4">Sincronização da Base de Dados</h2>
                            <p>Clique em Sincronizar para efetuar a pré carga</p>

                            <div id="synchronization-progress" class="card bg-light overflow-hidden d-none">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">
                                                <b class="text-theme synchronization-percent me-2 d-none">0%</b>
                                                <span class="synchronization-percent-text"></span>
                                            </h6>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h6 class="synchronization-time mb-0"></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress bg-warning-subtle rounded-0">
                                    <div class="progress-bar bg-warning synchronization-percent" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <ul class="list-unstyled concluded-meantimes">
                            </ul>
                        </div><!--end tab-pane-->
                    </div>
                </div> <!-- end col-->
            </div> <!-- end row-->
        </div><!-- end card-body -->
    </div><!--end card-->

@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="{{ URL::asset('build/js/settings-database.js') }}" type="module"></script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
