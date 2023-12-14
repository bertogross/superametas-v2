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
                <a class="nav-link menu-link {{ request()->is('settings/account') ? 'active' : '' }}" href="{{ route('settingsAccountShowURL') }}">
                    <i class="ri-arrow-right-up-line"></i> <span>Meu {{ appName() }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-link {{ request()->is('settings.users') ? 'active' : '' }}" href="{{ route('settingsUsersIndexURL') }}">
                    <i class="ri-admin-fill"></i> <span>@lang('translation.users')</span>
                </a>
            </li>

            @if (getERP())
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('settings/database') ? 'active' : '' }}" href="{{ route('settingsDatabaseIndexURL') }}">
                        <i class="ri-database-2-line"></i> @lang('translation.your-erp')
                    </a>
                </li>
            @endif

            <li class="nav-item">
                <a class="nav-link menu-link {{ request()->is('settings/api-keys') ? 'active' : '' }}" href="{{ route('settingsApiKeysURL') }}">
                    <i class="ri-cloud-windy-fill"></i> @lang('translation.api-conections')
                </a>
            </li>

            {{--
            <li class="nav-item">
                <a class="nav-link menu-link {{ request()->is('settings/api-keys') || request()->is('settings/database') ? 'active' : '' }}" href="#sidebarAPIs" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAPIs">
                    <i class="ri-cloud-windy-fill"></i> <span>@lang('translation.api-conections')</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarAPIs">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('settingsApiKeysURL') }}" class="nav-link {{ request()->is('settings/api-keys') ? 'active' : '' }}">@lang('translation.api-keys')</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('settingsDatabaseIndexURL') }}" class="nav-link {{ request()->is('settings/database') ? 'active' : '' }}">@lang('translation.your-erp')</a>
                        </li>
                    </ul>
                </div>
            </li>
            --}}

            @if ( getDropboxToken() )
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('settings/dropbox') ? 'active' : '' }}" href="{{ route('DropboxIndexURL') }}">
                        <i class="ri-dropbox-fill {{ request()->is('settings/dropbox') ? 'text-primary' : '' }}"></i> <span class="{{ request()->is('settings/dropbox') ? 'text-white' : '' }}">Armazenamento</span>
                    </a>
                </li>
            @endif

            <!--
            <li class="nav-item">
                <a class="nav-link menu-link {{ request()->is('settings/security') ? 'active' : '' }}" href="{{-- route('settingsSecurityIndexURL') --}}#">
                    <i class="ri-shield-keyhole-line"></i> <span>@lang('translation.security')</span>
                </a>
            </li>
            -->
        </ul>
    </div>
</div>
<div class="sidebar-background d-none"></div>
