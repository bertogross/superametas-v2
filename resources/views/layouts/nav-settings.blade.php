<div id="scrollbar">
    <div class="container-fluid">

        <div id="two-column-menu">
        </div>
        <ul class="navbar-nav" id="navbar-nav">
            <li class="menu-title"><span>@lang('translation.menu')</span></li>
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                    <i class="ri-bar-chart-2-fill"></i> <span>@lang('translation.dashboards')</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarDashboards">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ url('goal-sales') }}" class="nav-link">@lang('translation.goal-sales')</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('audit') }}" class="nav-link">@lang('translation.audit')</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-title"><i class="ri-more-fill"></i> <span>@lang('translation.components')</span></li>

            <li class="nav-item">
                <a class="nav-link menu-link {{ request()->is('settings-account') ? 'active' : '' }}" href="{{ url('settings-account') }}">
                    <i class="ri-arrow-right-up-line"></i> <span>Meu {{ env('APP_NAME') }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link {{ request()->is('settings-api-keys') || request()->is('settings-storage') || request()->is('settings-database') ? 'active' : '' }}" href="#sidebarAPIs" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAPIs">
                    <i class="ri-cloud-windy-fill"></i> <span>@lang('translation.api-conections')</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarAPIs">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ url('settings-api-keys') }}" class="nav-link {{ request()->is('settings-api-keys') ? 'active' : '' }}">@lang('translation.api-keys')</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('settings-storage') }}" class="nav-link {{ request()->is('settings-storage') ? 'active' : '' }}">@lang('translation.file-manager')</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('settings-database') }}" class="nav-link {{ request()->is('settings-database') ? 'active' : '' }}">@lang('translation.your-erp')</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link {{ request()->is('settings-users') ? 'active' : '' }}" href="{{ url('settings-users') }}">
                    <i class="ri-admin-fill"></i> <span>@lang('translation.users')</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link {{ request()->is('settings-security') ? 'active' : '' }}" href="{{ url('settings-security') }}">
                    <i class="ri-shield-keyhole-line"></i> <span>@lang('translation.security')</span>
                </a>
            </li>

        </ul>
    </div>
</div>
<div class="sidebar-background d-none"></div>
