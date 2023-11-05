@extends('layouts.master')
@section('title')
    Formulários de Vistoria
@endsection
@section('css')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ route('surveysIndexURL') }}
        @endslot
        @slot('li_1')
            @lang('translation.surveys')
        @endslot
        @slot('title')
            Formulários de Vistoria
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header border-0">
            <div class="d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">Listagem</h5>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2">
                        @component('surveys.components.nav')
                            @slot('url', route('surveysIndexURL'))
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>
        {{--
        <div class="card-body border border-dashed border-end-0 border-start-0">
            <form action="{{ route('surveysComposeIndexURL') }}" method="get" autocomplete="off">
                <div class="row g-3">
                    <div class="col">
                        <div class="search-box">
                            <input type="text" class="form-control search bg-light border-light" placeholder="Search for tasks or something...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>
                    <div class="col-auto wrap-form-btn">
                        <button type="submit" class="btn btn-theme w-100 init-loader"> <i class="ri-equalizer-fill me-1 align-bottom"></i> Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
        --}}
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-custom nav-theme nav-justified" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#departments" role="tab">
                        Departamentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#custom" role="tab">
                        Customizado
                    </a>
                </li>
            </ul>
            <div class="tab-content text-muted">
                <div class="tab-pane active" id="departments" role="tabpanel">
                    @if ($default->isEmpty())
                        @component('components.nothing')
                            @slot('url', route('surveysComposeAddURL', ['type'=>'default']))
                        @endcomponent
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0 table-striped table-hover" id="composeTable">
                                <thead class="table-light text-muted text-uppercase">
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Registrado</th>
                                        <th>Atualizado</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($default as $data)
                                        <tr>
                                            <td>{{ $data->id }}</td>
                                            <td>
                                                {{ $data->title }}
                                            </td>
                                            <td>
                                                {{ $data->created_at ? \Carbon\Carbon::parse($data->created_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-' }}
                                            </td>
                                            <td>
                                                {{ $data->updated_at ? \Carbon\Carbon::parse($data->updated_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-' }}
                                            </td>
                                            <td>
                                                {!! statusBadge($data->status) !!}
                                            </td>
                                            <td scope="row" class="text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('surveysComposeEditURL', $data->id) }}" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-line"></i></a>

                                                    <a href="{{ route('surveysComposeShowURL', $data->id) }}" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-line"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="tab-pane" id="custom" role="tabpanel">
                    @if ($custom->isEmpty())
                        @component('components.nothing')
                            @slot('url', route('surveysComposeAddURL', ['type'=>'custom']))
                        @endcomponent
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0 table-striped table-hover" id="composeTable">
                                <thead class="table-light text-muted text-uppercase">
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Registrado</th>
                                        <th>Atualizado</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($custom as $data)
                                        <tr>
                                            <td>{{ $data->id }}</td>
                                            <td>
                                                {{ $data->title }}
                                            </td>
                                            <td>
                                                {{ $data->created_at ? \Carbon\Carbon::parse($data->created_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-' }}
                                            </td>
                                            <td>
                                                {{ $data->updated_at ? \Carbon\Carbon::parse($data->updated_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-' }}
                                            </td>
                                            <td>
                                                {!! statusBadge($data->status) !!}
                                            </td>
                                            <td scope="row" class="text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('surveysComposeEditURL', $data->id) }}" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-line"></i></a>

                                                    <a href="{{ route('surveysComposeShowURL', $data->id) }}" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-line"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')

@endsection

