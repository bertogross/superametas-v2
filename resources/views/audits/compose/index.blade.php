@extends('layouts.master')
@section('title')
    Composição de Auditorias
@endsection
@section('css')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ route('auditsIndexURL') }}
        @endslot
        @slot('li_1')
            @lang('translation.audits')
        @endslot
        @slot('title')
            Composições
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header border-0">
            <div class="d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">Listagem</h5>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2">
                        @component('components.audits-nav')
                            @slot('url')
                                {{ route('auditsIndexURL') }}
                            @endslot
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>
        {{--
        <div class="card-body border border-dashed border-end-0 border-start-0">
            <form action="{{ route('auditsComposeIndexURL') }}" method="get" autocomplete="off">
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
            <div class="table-responsive table-card mb-4">
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
                        @foreach ($composes as $compose)
                            <tr>
                                <td>{{ $compose->id }}</td>
                                <td>
                                    {{ $compose->title }}
                                </td>
                                <td>
                                    {{ $compose->created_at ? \Carbon\Carbon::parse($compose->created_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-' }}
                                </td>
                                <td>
                                    {{ $compose->updated_at ? \Carbon\Carbon::parse($compose->updated_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-' }}
                                </td>
                                <td>
                                    {!! statusBadge($compose->status) !!}
                                </td>
                                <td scope="row" class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('auditsComposeEditURL', $compose->id) }}" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-line"></i></a>

                                        <a href="{{ route('auditsComposeShowURL', $compose->id) }}" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-line"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
@section('script')

@endsection

