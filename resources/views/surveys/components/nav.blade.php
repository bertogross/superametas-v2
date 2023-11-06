<div class="dropstart float-end">
    <button type="button" class="btn btn-sm btn-ghost-theme fs-4 pe-0" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" title="Opções"><i class="ri-more-2-line"></i></button>

    <ul class="dropdown-menu">

        <a class="dropdown-item {{ request()->is('surveys/compose/listing') ? 'd-none' : '' }}" href="{{ route('surveysComposeIndexURL') }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Listar Formulários de Vistoria">
            <i class="ri-list-check-2 text-muted fs-16 align-middle me-1"></i>
            <span class="align-middle">Listar Formulários</span>
        </a>

        <a class="dropdown-item {{ request()->is('surveys/listing') ? 'd-none' : '' }}" href="{{ route('surveysIndexURL') }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Listar Tarefas de Vistorias">
            <i class="ri-list-check-2 text-muted fs-16 align-middle me-1"></i>
            <span class="align-middle">Listar Vistorias</span>
        </a>

        <a class="dropdown-item {{ request()->is('surveys/compose/listing') ? 'd-none' : '' }}" href="{{ route('surveysCreateURL') }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tarefa de Vistoria">
            <i class="ri-add-line text-muted fs-16 align-middle me-1"></i>
            <span class="align-middle">Adicionar Vistoria</span>
        </a>

        <a class="dropdown-item {{ request()->is('surveys/listing') ? 'd-none' : '' }}" href="{{ route('surveysComposeCreateURL', ['type'=>'default']) }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tarefa de Vistoria">
            <i class="ri-add-line text-muted fs-16 align-middle me-1"></i>
            <span class="align-middle">Adicionar Formulário Departamentos</span>
        </a>

        <a class="dropdown-item {{ request()->is('surveys/listing') ? 'd-none' : '' }}" href="{{ route('surveysComposeCreateURL', ['type'=>'custom']) }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tarefa de Vistoria">
            <i class="ri-add-line text-muted fs-16 align-middle me-1"></i>
            <span class="align-middle">Adicionar Formulário Customizado</span>
        </a>

    </ul>
</div>
