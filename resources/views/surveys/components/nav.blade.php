<div class="dropstart float-end">
    <button type="button" class="btn btn-sm btn-ghost-theme fs-4 pe-0" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" title="Opções"><i class="ri-more-2-line"></i></button>

    <ul class="dropdown-menu">
        @if ( $url == route('surveysIndexURL') )
            <a class="dropdown-item" href="{{ route('surveysCreateURL') }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tarefa de Vistoria">
                <i class="ri-add-line text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Adicionar Vistoria</span>
            </a>
        @endif

        @if ( $url == route('surveysComposeIndexURL') )
            <a class="dropdown-item" href="{{ route('surveysComposeCreateURL', ['type'=>'default']) }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tarefa de Vistoria">
                <i class="ri-add-line text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Adicionar Formulário Departamentos</span>
            </a>

            <a class="dropdown-item" href="{{ route('surveysComposeCreateURL', ['type'=>'custom']) }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tarefa de Vistoria">
                <i class="ri-add-line text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Adicionar Formulário Customizado</span>
            </a>
        @endif
    </ul>
</div>
