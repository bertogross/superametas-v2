@extends('layouts.master')
@section('title')
    Visualização do Modelo de Vistoria
@endsection
@section('css')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('url')
            {{ route('surveysIndexURL') }}
        @endslot
        @slot('li_1')
            Formulários
        @endslot
        @slot('title')
            Visualização<small><i class="ri-arrow-drop-right-fill text-theme ms-2 me-2 align-bottom"></i> #<span class="text-theme">{{$data->id}}</span> {{ limitChars($data->title ?? '', 20) }}</small>
        @endslot
    @endcomponent

    <div id="content" class="rounded rounded-2 mb-4">
        <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show">
            <i class="ri-alert-line label-icon"></i> Este <strong>não</strong> é um formulário válido.<br>Esta demonstração é exibida durante a Pré-visualização na composição de um Modelo.
        </div>

        <div class="bg-primary-subtle position-relative">
            @if (!$edition)
                <span class="float-start m-3 position-absolute">{!! statusBadge($data->status) !!}</span>
            @endif

            @if(!$preview && !$edition)
                <a href="{{ route('surveysTemplateEditURL', ['id' => $data->id]) }}" class="btn btn-sm btn-light btn-icon waves-effect ms-2 float-end m-3" title="Editar registro: {{ limitChars($data->title ?? '', 20) }}"><i class="ri-edit-line"></i></a>
            @endif

            <div class="card-body p-5 text-center">
                <h2 class="text-theme text-uppercase">Modelo</h2>
                <h3>{{ $data ? $data->title : '' }}</h3>
                <div class="mb-0 text-muted">
                    Atualizado em:
                    {{ $data->updated_at ? \Carbon\Carbon::parse($data->updated_at)->locale('pt_BR')->isoFormat('D [de] MMMM, YYYY - HH:mm:ss') . 'hs' : '-' }}
                </div>
            </div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="1440" height="60" preserveAspectRatio="none" viewBox="0 0 1440 60">
                    <g mask="url(&quot;#SvgjsMask1001&quot;)" fill="none">
                        <path d="M 0,4 C 144,13 432,48 720,49 C 1008,50 1296,17 1440,9L1440 60L0 60z" style="fill: var(--vz-secondary-bg);"></path>
                    </g>
                    <defs>
                        <mask id="SvgjsMask1001">
                            <rect width="1440" height="60" fill="#ffffff"></rect>
                        </mask>
                    </defs>
                </svg>
            </div>
        </div>

        @if ($result)
            @component('surveys.layouts.form-surveyor-step-cards')
                @slot('data', $result)
                @slot('purpose', 'fakeForm')
            @endcomponent
        @endif
    </div>

@endsection
@section('script')
@endsection
