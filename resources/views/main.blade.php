<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Database Admin - {{ session('db_host') }}</title>
        
        <!-- Material UI CSS -->
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        
        <!-- Material UI JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
        
        <style>
            body {
                font-family: 'Roboto', sans-serif;
                background: #f5f5f5;
                margin: 0;
                padding: 0;
                overflow: hidden;
            }
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
            .brand-icon {
                width: 40px;
                height: 40px;
                background: #5c6bc0;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
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
            .main-container {
                display: flex;
                height: calc(100vh - 64px);
                margin-top: 64px;
            }
            .sidebar {
                width: 280px;
                background: white;
                box-shadow: 2px 0 4px rgba(0,0,0,0.1);
                overflow-y: auto;
            }
            .sidebar-section {
                padding: 20px;
            }
            .sidebar-title {
                font-size: 12px;
                font-weight: 600;
                color: #757575;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 12px;
            }
            .sidebar-item {
                display: flex;
                align-items: center;
                padding: 10px 12px;
                margin-bottom: 4px;
                border-radius: 6px;
                color: #424242;
                text-decoration: none;
                transition: all 0.2s;
            }
            .sidebar-item:hover {
                background: #f5f5f5;
                color: #424242;
            }
            .sidebar-item.active {
                background: #e8eaf6;
                color: #5c6bc0;
            }
            .sidebar-item i {
                margin-right: 10px;
                font-size: 18px;
            }
            .content-area {
                flex: 1;
                overflow-y: auto;
                padding: 30px;
            }
            .info-card {
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                padding: 24px;
                margin-bottom: 24px;
            }
            .info-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-top: 20px;
            }
            .info-item label {
                font-size: 12px;
                color: #757575;
                display: block;
                margin-bottom: 4px;
            }
            .info-item .value {
                font-size: 16px;
                font-weight: 500;
                color: #212121;
            }
            .quick-actions {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
                margin-top: 20px;
            }
            .action-card {
                background: white;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 20px;
                text-align: left;
                cursor: pointer;
                transition: all 0.2s;
            }
            .action-card:hover {
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                transform: translateY(-2px);
            }
            .action-card i {
                font-size: 32px;
                color: #5c6bc0;
                margin-bottom: 12px;
            }
            .action-card h6 {
                margin: 0 0 6px 0;
                font-size: 16px;
                font-weight: 500;
            }
            .action-card p {
                margin: 0;
                font-size: 12px;
                color: #757575;
            }
            table.highlight > tbody > tr:hover {
                background-color: #f5f5f5;
            }
            .table-actions a {
                margin-right: 15px;
                color: #5c6bc0;
                text-decoration: none;
            }
            .table-actions a:hover {
                text-decoration: underline;
            }
            .table-actions a.delete {
                color: #f44336;
            }
        </style>
    </head>
    <body>
        <!-- Top Navigation Bar -->
        <nav class="indigo lighten-1">
            <div class="nav-wrapper" style="display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 0 20px !important; height: 64px !important;">
                <!-- Brand Icon and Title on Left -->
                <div style="display: flex !important; align-items: center !important; gap: 10px !important; flex-shrink: 0 !important;">
                    <div class="brand-icon">
                        <i class="material-icons" style="color: white; font-size: 24px;">storage</i>
                    </div>
                    <span style="color: white; font-size: 18px; font-weight: 500;">DBAdmin</span>
                </div>
                
                <!-- Right side navigation -->
                <div style="display: flex !important; align-items: center !important; gap: 15px !important; flex-shrink: 0 !important;">
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
                    
                    <!-- Disconnect Button -->
                    <form action="{{ route('database.disconnect') }}" method="POST" style="margin: 0; flex-shrink: 0;">
                        @csrf
                        <button type="submit" class="btn waves-effect waves-light white" style="color: #5c6bc0; height: 36px; line-height: 36px; padding: 0 16px;">
                            <i class="material-icons left" style="line-height: inherit;">power_settings_new</i>
                            Disconnect
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-container">
            <!-- Sidebar - Tables List -->
            <aside class="sidebar">
                @if(session('current_database'))
                    <div class="sidebar-section">
                        <div class="sidebar-title">Tables in {{ session('current_database') }}</div>
                        @if(isset($tables) && count($tables) > 0)
                            @foreach($tables as $table)
                                <a href="{{ route('database.table', ['table' => $table]) }}" class="sidebar-item">
                                    <i class="material-icons">table_chart</i>
                                    <span>{{ $table }}</span>
                                </a>
                            @endforeach
                        @else
                            <p style="color: #757575; font-size: 14px; font-style: italic; padding: 0 12px;">No tables found</p>
                        @endif
                    </div>
                @else
                    <div class="sidebar-section">
                        <div style="text-align: center; padding: 40px 20px; color: #9e9e9e;">
                            <i class="material-icons" style="font-size: 48px; margin-bottom: 10px;">storage</i>
                            <p style="margin: 0; font-size: 14px;">Select a database from the dropdown above</p>
                        </div>
                    </div>
                @endif
            </aside>

            <!-- Main Content Area -->
            <main class="content-area">
                @if(session('success'))
                    <div class="card-panel green lighten-4" style="padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                        <i class="material-icons tiny" style="color: #2e7d32; vertical-align: middle; margin-right: 8px;">check_circle</i>
                        <span style="color: #2e7d32;">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="card-panel red lighten-4" style="padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                        <i class="material-icons tiny" style="color: #c62828; vertical-align: middle; margin-right: 8px;">error</i>
                        <span style="color: #c62828;">{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Welcome Section -->
                @if(!session('current_database'))
                    <div style="max-width: 1200px;">
                        <h4 style="margin-top: 0;">Welcome to Database Admin</h4>
                        <p style="font-size: 16px; color: #616161; margin-bottom: 30px;">
                            You are successfully connected to the MySQL server. Select a database from the left sidebar to begin.
                        </p>

                        <!-- Server Information Card -->
                        <div class="info-card">
                            <h5 style="margin-top: 0;">Server Information</h5>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Host</label>
                                    <div class="value">{{ session('db_host') }}</div>
                                </div>
                                <div class="info-item">
                                    <label>Port</label>
                                    <div class="value">{{ session('db_port') }}</div>
                                </div>
                                <div class="info-item">
                                    <label>Username</label>
                                    <div class="value">{{ session('db_username') }}</div>
                                </div>
                                <div class="info-item">
                                    <label>Databases</label>
                                    <div class="value">{{ isset($databases) ? count($databases) : 0 }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="info-card" style="background: linear-gradient(135deg, #e8eaf6 0%, #f3e5f5 100%);">
                            <h5 style="margin-top: 0;">Quick Actions</h5>
                            <div class="quick-actions">
                                <div class="action-card">
                                    <i class="material-icons">add_circle</i>
                                    <h6>Create Database</h6>
                                    <p>Add a new database</p>
                                </div>
                                <div class="action-card">
                                    <i class="material-icons">code</i>
                                    <h6>SQL Query</h6>
                                    <p>Run custom queries</p>
                                </div>
                                <div class="action-card">
                                    <i class="material-icons">cloud_upload</i>
                                    <h6>Import</h6>
                                    <p>Import SQL file</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Database Selected View -->
                    <div style="max-width: 1400px;">
                        <h4 style="margin-top: 0;">{{ session('current_database') }}</h4>
                        <p style="color: #616161; margin-bottom: 30px;">Database management and operations</p>

                        <!-- Tables Overview -->
                        <div class="info-card">
                            <h5 style="margin-top: 0;">Tables in this database</h5>
                            @if(isset($tables) && count($tables) > 0)
                                <table class="highlight responsive-table">
                                    <thead>
                                        <tr>
                                            <th>Table Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tables as $table)
                                            <tr>
                                                <td>
                                                    <i class="material-icons tiny" style="vertical-align: middle; margin-right: 8px; color: #757575;">table_chart</i>
                                                    <span style="font-weight: 500;">{{ $table }}</span>
                                                </td>
                                                <td class="table-actions">
                                                    <a href="{{ route('database.table', ['table' => $table]) }}">Browse</a>
                                                    <a href="#">Structure</a>
                                                    <a href="#" class="delete">Drop</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div style="text-align: center; padding: 40px; color: #757575;">
                                    <i class="material-icons" style="font-size: 48px; margin-bottom: 10px;">inbox</i>
                                    <p>No tables found in this database</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </main>
        </div>

        <script>
            $(document).ready(function(){
                // Initialize Material components
                M.AutoInit();
                
                // Initialize dropdown for database selection
                $('select').formSelect();
                
                // Fix dropdown padding issue - remove extra spaces
                setTimeout(function() {
                    var dropdownInput = $('.select-wrapper input.select-dropdown');
                    if (dropdownInput.length) {
                        var currentValue = dropdownInput.val();
                        if (currentValue) {
                            dropdownInput.val(currentValue.trim());
                        }
                    }
                }, 100);
                
                // Handle database selection change
                $('#database-select').on('change', function() {
                    var selectedDb = $(this).val();
                    if (selectedDb) {
                        window.location.href = '{{ url("/database") }}/' + encodeURIComponent(selectedDb);
                    }
                });
                
                // Add smooth hover effects
                $('.action-card').hover(
                    function() {
                        $(this).addClass('z-depth-2');
                    },
                    function() {
                        $(this).removeClass('z-depth-2');
                    }
                );
            });
        </script>
    </body>
</html>
