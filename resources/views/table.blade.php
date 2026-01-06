<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $table }} - Database Admin</title>
        
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
                padding: 0 20px !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
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
            .main-container {
                display: flex;
                height: calc(100vh - 64px);
                margin-top: 64px;
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
            .table-container {
                overflow-x: auto;
                margin-top: 20px;
            }
            table.striped > tbody > tr:nth-child(odd) {
                background-color: #f5f5f5;
            }
            table.highlight > tbody > tr:hover {
                background-color: #e8eaf6;
            }
            table th {
                background: #5c6bc0 !important;
                color: white !important;
                font-weight: 500 !important;
                text-transform: uppercase !important;
                font-size: 12px !important;
                letter-spacing: 0.5px !important;
            }
            table td {
                font-size: 14px;
                color: #424242;
            }
            .breadcrumb {
                display: flex;
                align-items: center;
                gap: 8px;
                color: #757575;
                font-size: 14px;
                margin-bottom: 20px;
            }
            .breadcrumb a {
                color: #5c6bc0;
                text-decoration: none;
            }
            .breadcrumb a:hover {
                text-decoration: underline;
            }
            .breadcrumb i {
                font-size: 16px;
            }
            .stats-row {
                display: flex;
                gap: 20px;
                margin-bottom: 20px;
            }
            .stat-card {
                flex: 1;
                background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);
                padding: 16px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .stat-card i {
                font-size: 32px;
                color: #5c6bc0;
            }
            .stat-content label {
                font-size: 12px;
                color: #5c6bc0;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .stat-content .value {
                font-size: 24px;
                font-weight: 500;
                color: #424242;
            }
        </style>
    </head>
    <body>
        <!-- Top Navigation Bar -->
        <nav class="indigo lighten-1">
            <div class="nav-wrapper">
                <!-- Brand Icon and Title on Left -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="brand-icon">
                        <i class="material-icons" style="color: white; font-size: 24px;">storage</i>
                    </div>
                    <span style="color: white; font-size: 18px; font-weight: 500;">DBAdmin</span>
                </div>
                
                <!-- Right side navigation -->
                <div style="display: flex; align-items: center; gap: 15px;">
                    <a href="{{ route('database.main') }}" class="btn waves-effect waves-light white" style="color: #5c6bc0; height: 36px; line-height: 36px; padding: 0 16px;">
                        <i class="material-icons left" style="line-height: inherit;">arrow_back</i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-container">
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

                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <a href="{{ route('database.main') }}">{{ session('current_database') }}</a>
                    <i class="material-icons">chevron_right</i>
                    <span style="color: #424242; font-weight: 500;">{{ $table }}</span>
                </div>

                <!-- Stats Row -->
                <div class="stats-row">
                    <div class="stat-card">
                        <i class="material-icons">table_rows</i>
                        <div class="stat-content">
                            <label>Total Rows</label>
                            <div class="value">{{ count($data) }}</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="material-icons">view_column</i>
                        <div class="stat-content">
                            <label>Columns</label>
                            <div class="value">{{ count($data) > 0 ? count((array)$data[0]) : 0 }}</div>
                        </div>
                    </div>
                </div>

                <!-- Table Data Card -->
                <div class="info-card">
                    <h5 style="margin-top: 0; display: flex; align-items: center; gap: 10px;">
                        <i class="material-icons" style="color: #5c6bc0;">table_chart</i>
                        {{ $table }}
                        <span style="font-size: 14px; color: #757575; font-weight: 400;">(showing first 100 rows)</span>
                    </h5>
                    
                    @if(count($data) > 0)
                        <div class="table-container">
                            <table class="striped highlight responsive-table">
                                <thead>
                                    <tr>
                                        @foreach(array_keys((array)$data[0]) as $column)
                                            <th>{{ $column }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $row)
                                        <tr>
                                            @foreach((array)$row as $value)
                                                <td>{{ $value ?? 'NULL' }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="text-align: center; padding: 60px; color: #757575;">
                            <i class="material-icons" style="font-size: 64px; margin-bottom: 16px; color: #bdbdbd;">inbox</i>
                            <h6 style="margin: 0 0 8px 0; font-weight: 500;">No data found</h6>
                            <p style="margin: 0; font-size: 14px;">This table is empty or has no rows.</p>
                        </div>
                    @endif
                </div>
            </main>
        </div>

        <script>
            $(document).ready(function(){
                // Initialize Material components
                M.AutoInit();
            });
        </script>
    </body>
</html>

