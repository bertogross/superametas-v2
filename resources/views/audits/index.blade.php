@php
// APP_print_r($getAuditStatusTranslations);

@endphp
@extends('layouts.master')
@section('title')
    @lang('translation.audits')
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ route('audits.index') }}
        @endslot
        @slot('li_1')
            @lang('translation.audits')
        @endslot
        @slot('title')
            @lang('translation.list')
        @endslot
    @endcomponent

    <div class="row">
        @foreach ($getAuditStatusTranslations as $key => $value)
        <div class="col">
            <div class="card card-animate" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="{{ $value['description'] }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">{{ $value['label'] }}</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="{{ $auditStatusCount[$key] ?? 0 }}"></span></h2>
                            {{--
                            <p class="mb-0 text-muted"><span class="badge bg-light text-{{ $value['color'] }} mb-0">
                                    <i class="ri-arrow-up-line align-middle"></i> 0.63 %
                                </span> vs. previous month
                            </p>
                            --}}
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-{{ $value['color'] }}-subtle text-{{ $value['color'] }} rounded-circle fs-4">
                                    <i class="{{ !empty($value['icon']) ? $value['icon'] : 'ri-ticket-2-line' }}"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        <!--end col-->
        @endforeach
    </div>
    <!--end row-->

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Auditorias</h5>
                        <div class="flex-shrink-0">
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-outline-theme btn-audit-edit"><i class="ri-add-line align-bottom me-1"></i> Compor</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{ route('audits.index') }}" method="get" autocomplete="off">
                        <div class="row g-3">
                            {{--
                            <div class="col-sm-12 col-md-2 col-lg">
                                <div class="search-box">
                                    <input type="text" class="form-control search bg-light border-light" placeholder="Search for tasks or something...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            --}}

                            <div class="col-sm-12 col-md col-lg">
                                <div class="input-light">
                                    <select class="form-select" data-choices data-choices-removeItem name="delegated_to[]" multiple data-placeholder="Atribuíção">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ in_array($user->id, $delegated_to) ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md col-lg">
                                <div class="input-light">
                                    <select class="form-select" data-choices data-choices-removeItem name="audited_by[]" multiple data-placeholder="Auditoria">
                                        @foreach ($usersByRole as $auditor)
                                            <option value="{{ $auditor->id }}" {{ in_array($auditor->id, $audited_by) ? 'selected' : '' }}>{{ $auditor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md col-lg">
                                <input type="text" class="form-control bg-light border-light flatpickr-range" name="created_at" placeholder="Período do Registro" value="{{ request('created_at') }}">
                            </div>

                            <div class="col-sm-12 col-md col-lg">
                                <div class="input-light">
                                    <select class="form-select" name="status">
                                        <option value="" {{  !request('status') ? 'selected' : '' }}>Status</option>
                                        @foreach ($getAuditStatusTranslations as $key => $value)
                                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-auto col-lg-auto wrap-form-btn">{{-- d-none --}}
                                <button type="submit" class="btn btn-theme w-100 init-loader"> <i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar</button>
                            </div>

                        </div>
                        <!--end row-->
                    </form>
                </div>
                <!--end card-body-->
                <div class="card-body">
                    <div class="table-responsive table-card mb-4">
                        <table class="table align-middle table-nowrap mb-0 table-striped table-hover" id="tasksTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th class="sort" data-sort="created_at" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Registro">Registro</th>
                                    <th class="sort" data-sort="created_at" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Execução da Vistoria">Vistoria</th>
                                    <th class="sort" data-sort="created_at" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Data de Execução da Auditoria">Auditoria</th>
                                    <th class="sort" data-sort="delegated_to" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Colaborador ao qual foi atribuída a tarefa de Vistoria">Atribuído a</th>
                                    <th class="sort" data-sort="assigned_to" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="A loja em que a tarefa será/foi desempenhada">Loja</th>
                                    <th class="sort" data-sort="status" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Status: Pendente, Em Andamento, Concluído, Auditado">Status</th>
                                    {{--
                                    <th class="sort" data-sort="priority">Priority</th>
                                    --}}
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($audits as $audit)
                                    <tr>
                                        <td scope="row"><a class="fw-medium link-primary" href="{{ route('audits.show', $audit->id) }}">{{ $audit->id }}</a></td>
                                        <td class="created_at">
                                            {{ $audit->created_at ? \Carbon\Carbon::parse($audit->created_at)->format('d F, Y') : '-' }}
                                        </td>
                                        <td class="completed_at">
                                            {{ $audit->completed_at ? \Carbon\Carbon::parse($audit->completed_at)->format('d F, Y') : '-' }}
                                        </td>
                                        <td class="audited_at">
                                            {{ $audit->audited_at ? \Carbon\Carbon::parse($audit->audited_at)->format('d F, Y') : '-' }}
                                        </td>
                                        <td class="delegated_to align-middle">
                                            @if ($audit->delegated_to)
                                                @php
                                                    $avatar = getUserData($audit->delegated_to)['avatar'];
                                                    $name = getUserData($audit->delegated_to)['name'];
                                                @endphp
                                                <a href="javascript: void(0);" class="avatar-group-item me-1" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="{{ $name }}" title="{{ $name }}">
                                                    <img
                                                    @if(empty(trim($avatar)))
                                                        src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                                    @else
                                                        src="{{ URL::asset('storage/' .$avatar ) }}"
                                                    @endif
                                                    alt="{{ $name }}" class="rounded-circle avatar-xxs">
                                                </a> {{ $name }}
                                            @endif
                                        </td>
                                        <td class="assigned_to">
                                            {{ getCompanyAlias($audit->assigned_to) }}
                                        </td>
                                        <td class="status">
                                            <span class="badge bg-{{ $getAuditStatusTranslations[$audit->status]['color'] }}-subtle text-{{ $getAuditStatusTranslations[$audit->status]['color'] }} text-uppercase" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $getAuditStatusTranslations[$audit->status]['description'] }}">
                                                {{ $getAuditStatusTranslations[$audit->status]['label'] }}
                                            </span>

                                        </td>
                                        {{--
                                        <td class="priority">
                                            <span class="badge bg-danger text-uppercase">
                                                {{ $audit->priority }}
                                            </span>
                                        </td>
                                        --}}
                                        <td scope="row" class="text-end">
                                            <div class="btn-group">
                                                @if ($audit->status == 'pending' )
                                                    <button type="button" data-audit-id="{{ $audit->id }}" class="btn btn-sm btn-outline-dark waves-effect btn-audit-edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-line"></i></button>
                                                @else
                                                    <button type="button" disabled class="btn btn-sm btn-outline-dark cursor-not-allowed" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-title="Edição Bloqueada" data-bs-content="Status <b class='text-{{ $getAuditStatusTranslations[$audit->status]['color'] }}'>{{ $getAuditStatusTranslations[$audit->status]['label'] }}</b><br><br>A edição será possível somente se o usuário ao qual foi atribuída tal tarefa optar por <b>Abortar</b>"><i class="ri-edit-line"></i></button>
                                                @endif
                                                <a href="{{ route('audits.show', $audit->id) }}" class="btn btn-sm btn-outline-dark waves-effect" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-line"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="d-flex justify-content-center">
                        {!! $audits->links('layouts.custom-pagination') !!}
                    </div>

                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->

@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/l10n/pt.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/monthSelect/index.js') }}"></script>
    <script src="{{ URL::asset('build/libs/flatpickr/plugins/confirmDate/confirmDate.js') }}"></script>

    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script src="{{ URL::asset('build/js/audits.js') }}" type="module"></script>
@endsection
