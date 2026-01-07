<style>
    /* Navigation Bar Styles */
    nav {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        height: 64px !important;
        line-height: normal !important;
    }
    .nav-wrapper {
        height: 64px !important;
        line-height: normal !important;
        padding: 0 !important;
    }
    nav a {
        line-height: normal !important;
        height: auto !important;
        padding: 0 !important;
    }
    .logo{
        height: 50px;
    }
    nav .select-wrapper {
        margin: 0 !important;
    }
    nav .select-wrapper input.select-dropdown {
        box-sizing: border-box !important;
        color: white !important;
        border: 1px solid rgba(255,255,255,0.5) !important;
        border-radius: 4px !important;
        margin: 0 !important;
        height: 36px !important;
        line-height: 36px !important;
        padding: 0 30px 0 10px !important;
        background-color: rgba(255,255,255,0.1) !important;
        text-indent: 0 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    nav .select-wrapper input.select-dropdown:focus {
        border-color: white !important;
        background-color: rgba(255,255,255,0.15) !important;
    }
    nav .select-wrapper .caret {
        fill: white !important;
        right: 8px !important;
    }
    nav .select-wrapper ul.dropdown-content {
        top: 40px !important;
    }
    nav .select-wrapper ul.dropdown-content li {
        min-height: 36px !important;
        line-height: 36px !important;
        display: flex !important;
        align-items: center !important;
    }
    nav .select-wrapper ul.dropdown-content li > span {
        padding: 0 12px !important;
        display: block !important;
        line-height: 1.5 !important;
    }
    nav .btn {
        height: 36px !important;
        line-height: 36px !important;
        margin: 0 !important;
        padding: 0 16px !important;
    }
    nav .btn i {
        line-height: 36px !important;
        height: 36px !important;
    }
    nav form {
        display: flex !important;
        align-items: center !important;
    }
    nav span, nav div {
        line-height: normal !important;
    }
    .nav-wrapper > div {
        flex-shrink: 0 !important;
    }
    .nav-wrapper > div > * {
        white-space: nowrap !important;
    }
    .status-badge {
        display: inline-flex !important;
        align-items: center !important;
        padding: 6px 12px !important;
        background: #4caf50 !important;
        color: white !important;
        border-radius: 16px !important;
        font-size: 12px !important;
        height: 28px !important;
        line-height: 1 !important;
        white-space: nowrap !important;
        margin: 0 !important;
    }
    .status-dot {
        width: 6px;
        height: 6px;
        background: white;
        border-radius: 50%;
        margin-right: 6px;
        flex-shrink: 0;
    }
</style>

<!-- Top Navigation Bar -->
<nav class="indigo lighten-1">
    <div class="nav-wrapper" style="display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 0 20px !important; height: 64px !important;">
        <!-- Brand Icon and Title on Left -->
        <div style="display: flex !important; align-items: center !important; gap: 10px !important; flex-shrink: 0 !important;">
            <a href="javascript:void(0)" onclick="goToHome()" style="color: white; font-size: 18px; font-weight: 500; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: opacity 0.2s; cursor: pointer;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                <img src="{{ asset('image/logo.png') }}" class="logo" alt="DBAdmin Logo"> 
                <span>DBAdmin{{ isset($pageTitle) ? ' - ' . $pageTitle : '' }}</span>
            </a>
        </div>
        
        <!-- Hidden form to deselect database -->
        <form id="deselectDatabaseForm" action="{{ route('database.deselect') }}" method="POST" style="display: none;">
            @csrf
        </form>
        
        <script>
            function goToHome() {
                @if(session('current_database'))
                    // If a database is selected, deselect it first
                    document.getElementById('deselectDatabaseForm').submit();
                @else
                    // If no database is selected, just go to main
                    window.location.href = '{{ route('database.main') }}';
                @endif
            }
        </script>
        
        <!-- Right side navigation -->
        <div style="display: flex !important; align-items: center !important; gap: 15px !important; flex-shrink: 0 !important;">
            @if(isset($customButtons))
                {!! $customButtons !!}
            @else
                <!-- Connection Status -->
                <span class="status-badge">
                    <span class="status-dot"></span>
                    Connected
                </span>
                
                <!-- Connection Info -->
                <span style="color: rgba(255,255,255,0.9); font-size: 13px;">
                    {{ session('db_username') }} @ {{ session('db_host') }} : {{ session('db_port') }}
                </span>
                
                <!-- Database Dropdown -->
                @if(isset($databases) && count($databases) > 0)
                    <div style="display: inline-block; width: 280px; position: relative; flex-shrink: 0;">
                        <select id="database-select">
                            <option value="" disabled {{ !session('current_database') ? 'selected' : '' }}>Select Database</option>
                            @foreach($databases as $database)
                                <option value="{{ $database }}" {{ session('current_database') === $database ? 'selected' : '' }}>{{ $database }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                <!-- Deselect Database Button -->
                @if(session('current_database'))
                    <form action="{{ route('database.deselect') }}" method="POST" style="margin: 0; flex-shrink: 0;">
                        @csrf
                        <button type="submit" class="btn waves-effect waves-light white" style="color: #5c6bc0; height: 36px; line-height: 36px; padding: 0 16px;" title="Deselect current database">
                            <i class="material-icons" style="line-height: inherit;">close</i>
                        </button>
                    </form>
                @endif
                
                <!-- Disconnect Button -->
                <form action="{{ route('database.disconnect') }}" method="POST" style="margin: 0; flex-shrink: 0;">
                    @csrf
                    <button type="submit" class="btn waves-effect waves-light white" style="color: #5c6bc0; height: 36px; line-height: 36px; padding: 0 16px;">
                        <i class="material-icons left" style="line-height: inherit;">power_settings_new</i>
                        Disconnect
                    </button>
                </form>
            @endif
        </div>
    </div>
</nav>

