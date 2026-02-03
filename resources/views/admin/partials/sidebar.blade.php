<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=dc3545&color=fff&size=64" 
                 class="rounded-circle mb-2" alt="Admin">
            <h6 class="text-white">{{ Auth::user()->name }}</h6>
            <span class="badge bg-danger">Administrator</span>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                   href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('admin.events') ? 'active' : '' }}" 
                   href="{{ route('admin.events.index') }}">
                    <i class="fas fa-calendar-alt me-2"></i>Events
                </a>
            </li>
            <li class="nav-item">
    <a class="nav-link text-white {{ request()->routeIs('admin.surveys') ? 'active' : '' }}" 
       href="{{ route('admin.surveys.index') }}">
        <i class="fas fa-poll me-2"></i>Surveys
    </a>
</li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#">
                    <i class="fas fa-chart-bar me-2"></i>Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#">
                    <i class="fas fa-users me-2"></i>Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#">
                    <i class="fas fa-cog me-2"></i>Settings
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link text-warning" href="{{ route('dashboard') }}">
                    <i class="fas fa-user me-2"></i>Switch to User View
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</nav>

<style>
.sidebar {
    min-height: calc(100vh - 56px);
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}
.sidebar .nav-link {
    font-weight: 500;
    border-radius: 5px;
    margin-bottom: 5px;
}
.sidebar .nav-link:hover {
    background-color: rgba(255,255,255,.1);
}
.sidebar .nav-link.active {
    background-color: rgba(220,53,69,.9);
}
</style>