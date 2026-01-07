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
        @php
            $customButtons = '
                <button class="btn waves-effect waves-light white" onclick="$(\'#renameTableModal\').modal(\'open\')" style="color: #5c6bc0; height: 36px; line-height: 36px; padding: 0 16px;">
                    <i class="material-icons left" style="line-height: inherit;">edit</i>
                    Rename
                </button>
                <button class="btn waves-effect waves-light white" onclick="$(\'#manageColumnsModal\').modal(\'open\')" style="color: #5c6bc0; height: 36px; line-height: 36px; padding: 0 16px;">
                    <i class="material-icons left" style="line-height: inherit;">view_column</i>
                    Manage Columns
                </button>
                <button class="btn waves-effect waves-light red lighten-1" onclick="dropCurrentTable()" style="height: 36px; line-height: 36px; padding: 0 16px;">
                    <i class="material-icons left" style="line-height: inherit;">delete</i>
                    Drop Table
                </button>
                <a href="' . route('database.main') . '" class="btn waves-effect waves-light white" style="color: #5c6bc0; height: 36px; line-height: 36px; padding: 0 16px;">
                    <i class="material-icons left" style="line-height: inherit;">arrow_back</i>
                    Back
                </a>
            ';
        @endphp
        <x-navbar pageTitle="{{ $table }}" :customButtons="$customButtons" />

        <!-- Main Content -->
        <div class="main-container">
            <x-table-sidebar :tables="$tables ?? []" :currentTable="$table" />

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

        <!-- Rename Table Modal -->
        <div id="renameTableModal" class="modal" style="max-width: 500px;">
            <div class="modal-content">
                <h5 style="margin-top: 0; color: #5c6bc0;">
                    <i class="material-icons" style="vertical-align: middle;">edit</i>
                    Rename Table
                </h5>
                <form action="{{ route('table.rename', ['database' => session('current_database'), 'table' => $table]) }}" method="POST">
                    @csrf
                    <div class="input-field">
                        <input id="current_table_name" type="text" value="{{ $table }}" disabled>
                        <label for="current_table_name">Current Table Name</label>
                    </div>
                    <div class="input-field">
                        <input id="new_name" name="new_name" type="text" required pattern="[a-zA-Z0-9_]+" maxlength="64">
                        <label for="new_name">New Table Name</label>
                        <span class="helper-text">Only letters, numbers, and underscores allowed</span>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 16px 0 0 0; margin-top: 20px;">
                        <button type="button" class="modal-close waves-effect waves-light btn-flat">Cancel</button>
                        <button type="submit" class="waves-effect waves-light btn indigo lighten-1">
                            <i class="material-icons left">save</i>Rename
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Manage Columns Modal -->
        <div id="manageColumnsModal" class="modal" style="max-width: 700px; max-height: 90%;">
            <div class="modal-content">
                <h5 style="margin-top: 0; color: #5c6bc0;">
                    <i class="material-icons" style="vertical-align: middle;">view_column</i>
                    Manage Columns - {{ $table }}
                </h5>
                
                <!-- Add Column Form -->
                <div class="card-panel indigo lighten-5" style="padding: 16px; margin-bottom: 20px;">
                    <h6 style="margin: 0 0 12px 0; color: #5c6bc0; font-weight: 500;">Add New Column</h6>
                    <form action="{{ route('table.addColumn', ['database' => session('current_database'), 'table' => $table]) }}" method="POST">
                        @csrf
                        <div class="row" style="margin-bottom: 0;">
                            <div class="input-field col s4" style="margin-top: 0;">
                                <input type="text" name="column_name" required pattern="[a-zA-Z0-9_]+" placeholder="Column Name">
                            </div>
                            <div class="col s3" style="margin-top: 0;">
                                <select name="column_type" required class="browser-default" style="display: block; height: 35px; border: 1px solid #9e9e9e; border-radius: 4px; padding: 0 8px; background-color: white;">
                                    <option value="">Type</option>
                                    <option value="INT">INT</option>
                                    <option value="VARCHAR">VARCHAR</option>
                                    <option value="TEXT">TEXT</option>
                                    <option value="DATE">DATE</option>
                                    <option value="DATETIME">DATETIME</option>
                                    <option value="TIMESTAMP">TIMESTAMP</option>
                                    <option value="BIGINT">BIGINT</option>
                                    <option value="DECIMAL">DECIMAL</option>
                                    <option value="BOOLEAN">BOOLEAN</option>
                                </select>
                            </div>
                            <div class="input-field col s2" style="margin-top: 0;">
                                <input type="text" name="column_length" placeholder="Length">
                            </div>
                            <div class="col s2" style="padding-top: 12px;">
                                <label>
                                    <input type="checkbox" name="nullable" value="1" checked />
                                    <span style="font-size: 12px;">Nullable</span>
                                </label>
                            </div>
                            <div class="col s1" style="padding-top: 5px;">
                                <button type="submit" class="btn-small indigo lighten-1" style="padding: 0 12px;">
                                    <i class="material-icons" style="font-size: 18px;">add</i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Existing Columns -->
                <h6 style="color: #757575; font-size: 14px; font-weight: 600; margin-bottom: 10px;">Existing Columns:</h6>
                @if(count($data) > 0)
                    @foreach(array_keys((array)$data[0]) as $columnName)
                        <div class="card-panel" style="padding: 12px; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <strong style="color: #424242;">{{ $columnName }}</strong>
                                </div>
                                <div>
                                    <button class="btn-small waves-effect waves-light red lighten-1" onclick="dropColumn('{{ $columnName }}')" style="padding: 0 12px;">
                                        <i class="material-icons left" style="font-size: 16px;">delete</i>
                                        Drop
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; color: #757575;">No columns to display (empty table)</p>
                @endif

                <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 16px 0 0 0; margin-top: 20px;">
                    <button type="button" class="modal-close waves-effect waves-light btn-flat">Close</button>
                </div>
            </div>
        </div>

        <!-- Drop Column Form -->
        <form id="dropColumnForm" method="POST" style="display: none;">
            @csrf
            <input type="hidden" id="drop_column_name" name="column_name">
        </form>

        <!-- Drop Table Form -->
        <form id="dropTableForm" action="{{ route('table.drop', ['database' => session('current_database'), 'table' => $table]) }}" method="POST" style="display: none;">
            @csrf
        </form>

        <script>
            $(document).ready(function(){
                // Initialize Material components
                M.AutoInit();
                
                // Initialize modals
                $('.modal').modal();
            });

            // Drop current table
            function dropCurrentTable() {
                if (confirm('Are you sure you want to drop table "{{ $table }}"? This action cannot be undone!')) {
                    document.getElementById('dropTableForm').submit();
                }
            }

            // Drop column
            function dropColumn(columnName) {
                if (confirm('Are you sure you want to drop column "' + columnName + '"? This action cannot be undone!')) {
                    var form = document.getElementById('dropColumnForm');
                    document.getElementById('drop_column_name').value = columnName;
                    form.action = '{{ route("table.dropColumn", ["database" => session("current_database"), "table" => $table]) }}';
                    form.submit();
                }
            }
        </script>
    </body>
</html>

