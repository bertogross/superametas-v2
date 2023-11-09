<div class="nav nav-pills flex-column nav-pills-tab verti-nav-pills custom-verti-nav-pills nav-pills-theme" aria-orientation="vertical">
    <a href="{{ route('surveysIndexURL') }}" class="nav-link text-uppercase {{ $url == route('surveysIndexURL') ? 'active' : '' }} mt-0 mb-2" title="Listar Vistorias"><i class="ri-list-check-2 text-muted fs-16 align-middle me-1"></i> Vistorias</a>

    <a href="{{ route('surveysCreateURL') }}" class="nav-link text-uppercase {{ $url == route('surveysCreateURL') || $url == route('surveysEditURL') ? 'active' : '' }} mt-0 mb-2" title="Adicionar Vistoria"><i class="ri-add-line text-muted fs-16 align-middle me-1"></i> Vistoria</a>

    <hr class="m-3 mt-2">

    <a href="{{ route('surveysComposeIndexURL') }}" class="nav-link text-uppercase {{ $url == route('surveysComposeIndexURL') ? 'active' : '' }} mt-0 mb-2" title="Listar Formulários"><i class="ri-list-check-2 text-muted fs-16 align-middle me-1"></i> Formulários</a>

    <a href="{{ route('surveysComposeCreateURL', ['type'=>'default']) }}" class="nav-link text-uppercase {{ $url == route('surveysComposeCreateURL', ['type'=>'default']) || $url == route('surveysComposeEditURL', ['type'=>'default']) ? 'active' : '' }} mt-0 mb-2" title="Adicionar Formulário Departamentos"><i class="ri-add-line text-muted fs-16 align-middle me-1"></i> Formulário Departamentos</a>

    <a href="{{ route('surveysComposeCreateURL', ['type'=>'custom']) }}" class="nav-link text-uppercase {{ $url == route('surveysComposeCreateURL', ['type'=>'custom']) || $url == route('surveysComposeEditURL', ['type'=>'custom']) ? 'active' : '' }} mt-0 mb-2" title="Adicionar Formulário Customizado"><i class="ri-add-line text-muted fs-16 align-middle me-1"></i> Formulário Customizado</a>
</div>
