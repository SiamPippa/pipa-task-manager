<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <rect width="32" height="32" rx="8" fill="#696cff" />
          <text x="16" y="22" text-anchor="middle" fill="#ffffff" font-size="18" font-weight="700" font-family="Public Sans, sans-serif">P</text>
        </svg>
      </span>
      <span class="app-brand-text demo menu-text fw-bolder ms-2">Pippa</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <a href="{{ route('dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div>Dashboard</div>
      </a>
    </li>

    @can('viewAny', App\Models\Company::class)
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Organization</span>
    </li>

    <li class="menu-item {{ request()->routeIs('companies.*') ? 'active' : '' }}">
      <a href="{{ route('companies.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-buildings"></i>
        <div>Companies</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('company-settings.*') ? 'active' : '' }}">
      <a href="{{ route('company-settings.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-cog"></i>
        <div>Company Settings</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('departments.*') ? 'active' : '' }}">
      <a href="{{ route('departments.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-sitemap"></i>
        <div>Departments</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('designations.*') ? 'active' : '' }}">
      <a href="{{ route('designations.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-id-card"></i>
        <div>Designations</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
      <a href="{{ route('users.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div>Users</div>
      </a>
    </li>
    @endcan

    @can('view-rbac')
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Access Control</span>
    </li>

    <li class="menu-item {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
      <a href="{{ route('permissions.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-shield-quarter"></i>
        <div>RBAC Permissions</div>
      </a>
    </li>

    @can('assign-user-roles')
    <li class="menu-item {{ request()->routeIs('user-roles.*') ? 'active' : '' }}">
      <a href="{{ route('user-roles.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user-pin"></i>
        <div>Assign User Roles</div>
      </a>
    </li>
    @endcan
    @endcan

    @if(Gate::check('viewAny', App\Models\Project::class) || Gate::check('viewAny', App\Models\Team::class) || Gate::check('viewAny', App\Models\Task::class))
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Projects & Tasks</span>
    </li>
    @endif

    @can('viewAny', App\Models\Project::class)
    <li class="menu-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
      <a href="{{ route('projects.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-folder"></i>
        <div>Projects</div>
      </a>
    </li>
    @endcan

    @can('viewAny', App\Models\Team::class)
    <li class="menu-item {{ request()->routeIs('teams.*') ? 'active' : '' }}">
      <a href="{{ route('teams.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-group"></i>
        <div>Teams</div>
      </a>
    </li>
    @endcan

    @can('viewAny', App\Models\ProjectTeamAssignment::class)
    <li class="menu-item {{ request()->routeIs('project-team-assignments.*') ? 'active' : '' }}">
      <a href="{{ route('project-team-assignments.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-link-alt"></i>
        <div>Project Team Assignments</div>
      </a>
    </li>
    @endcan

    @can('viewAny', App\Models\Task::class)
    <li class="menu-item {{ request()->routeIs('project-tasks.*') ? 'active' : '' }}">
      <a href="{{ route('project-tasks.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-task"></i>
        <div>Tasks</div>
      </a>
    </li>
    @endcan

    @can('viewAny', App\Models\TaskAssignment::class)
    <li class="menu-item {{ request()->routeIs('task-assignments.*') ? 'active' : '' }}">
      <a href="{{ route('task-assignments.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user-check"></i>
        <div>Task Assignments</div>
      </a>
    </li>
    @endcan

    @can('viewAny', App\Models\DailyReport::class)
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Reports</span>
    </li>

    <li class="menu-item {{ request()->routeIs('daily-reports.*') ? 'active' : '' }}">
      <a href="{{ route('daily-reports.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-notepad"></i>
        <div>Daily Reports</div>
      </a>
    </li>
    @endcan
  </ul>
</aside>
