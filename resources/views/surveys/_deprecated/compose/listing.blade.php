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

    <div id="composeList" class="card">
        <ul class="nav nav-tabs nav-tabs-custom nav-theme nav-justified" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-uppercase active" data-bs-toggle="tab" href="#departments" role="tab">
                    Departamentos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" data-bs-toggle="tab" href="#custom" role="tab">
                    Customizados
                </a>
            </li>
        </ul>
        <div class="tab-content text-muted">
            <div class="tab-pane active" id="departments" role="tabpanel">
                @if ($default->isEmpty())
                    @component('components.nothing')
                        @slot('url', route('surveysComposeCreateURL', ['type'=>'default']))
                    @endcomponent
                @else
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0  me-2">Departamentos</h4>
                            <div class="flex-shrink-0 ms-auto">
                                <a class="btn btn-outline-theme" href="{{ route('surveysComposeCreateURL', ['type'=>'default']) }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Formulário Departamentos">
                                    <i class="ri-add-line align-bottom me-1"></i>Departamentos
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
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
                        </div>
                    </div>
                @endif
            </div>
            <div class="tab-pane" id="custom" role="tabpanel">
                @if ($custom->isEmpty())
                    @component('components.nothing')
                        @slot('url', route('surveysComposeCreateURL', ['type'=>'custom']))
                    @endcomponent
                @else
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0  me-2">Customizados</h4>
                            <div class="flex-shrink-0 ms-auto">
                                <a class="btn btn-outline-theme" href="{{ route('surveysComposeCreateURL', ['type'=>'custom']) }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Formulário Customizado">
                                    <i class="ri-add-line align-bottom me-1"></i>Customizado
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
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
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection
@section('script')

@endsection

