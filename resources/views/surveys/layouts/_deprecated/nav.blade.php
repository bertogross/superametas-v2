<div id="scrollbar">
    <div class="container-fluid">

        <div id="two-column-menu">
        </div>
        <ul class="navbar-nav" id="navbar-nav">
            {{--
            <li class="menu-title"><span>@lang('translation.menu')</span></li>
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                    <i class="ri-bar-chart-2-fill"></i> <span>@lang('translation.dashboards')</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarDashboards">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('goalSalesIndexURL') }}" class="nav-link">@lang('translation.goal-sales')</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('surveysIndexURL') }}" class="nav-link">@lang('translation.surveys')</a>
                        </li>
                    </ul>
                </div>
            </li>
            --}}

            <li class="menu-title"><i class="ri-more-fill"></i> <span>@lang('translation.components')</span></li>

            <li class="nav-item">
                <a href="{{ route('surveysIndexURL') }}" class="nav-link menu-link
                {{
                request()->is('surveys/listing') ||
                request()->is('surveys/listing-cards') ||
                request()->is('surveys/create')
                ? 'active' : ''
                }}
                " title="Listar Vistorias"><i class="ri-todo-line"></i> Vistorias</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('surveysComposeIndexURL') }}" class="nav-link menu-link
                {{
                request()->is('surveys/compose/listing') ||
                request()->is('surveys/compose/create/default') ||
                request()->is('surveys/compose/create/custom')
                ? 'active' : ''
                }}
                " title="Listar Formulários"><i class="ri-list-check-2"></i> Formulários</a>
            </li>

        </ul>
    </div>
</div>
<div class="sidebar-background d-none"></div>
