<div class="dropstart float-end">
    <button type="button" class="btn btn-sm btn-ghost-theme fs-4 pe-0" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" title="Opções"><i class="ri-more-2-line"></i></button>

    <ul class="dropdown-menu">

        <a class="dropdown-item {{ request()->is('audits/compose') ? 'd-none' : '' }}" href="{{ route('auditsComposeIndexURL') }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Listar Formulários de Composição">
            <i class="ri-list-check-2 text-muted fs-16 align-middle me-1"></i>
            <span class="align-middle"> Composições</span>
        </a>

        <a class="dropdown-item {{ request()->is('audits') ? 'd-none' : '' }}" href="{{ route('auditsComposeAddURL') }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Compor Formulário Customizado">
            <i class="ri-add-line text-muted fs-16 align-middle me-1"></i>
            <span class="align-middle">Compor</span>
        </a>

        <div class="dropdown-divider"></div>

        <a class="dropdown-item {{ request()->is('audits') ? 'd-none' : '' }}" href="{{ route('auditsIndexURL') }}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Listar Tarefas de Auditoria">
            <i class="ri-list-check-2 text-muted fs-16 align-middle me-1"></i>
            <span class="align-middle">Auditorias</span>
        </a>

        <a class="dropdown-item btn-audit-edit {{ request()->is('audits/compose') ? 'd-none' : '' }}" href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="left" title="Adicionar Tarefa de Vistoria">
            <i class="ri-add-line text-muted fs-16 align-middle me-1"></i>
            <span class="align-middle">Auditoria</span>
        </a>

    </ul>
</div>
