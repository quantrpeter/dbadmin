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
            /* Fix dropdown in modals */
            .modal {
                overflow: visible !important;
            }
            .modal-content {
                overflow: visible !important;
            }
            .modal .select-wrapper ul.dropdown-content {
                position: fixed !important;
                z-index: 9999 !important;
                max-height: 300px !important;
                overflow-y: auto !important;
                width: auto !important;
                min-width: 100px !important;
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
            .database-card {
                background: white;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 16px;
                cursor: pointer;
                transition: all 0.2s;
            }
            .database-card:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transform: translateY(-2px);
                border-color: #5c6bc0;
            }
        </style>
    </head>
    <body>
        <x-navbar :databases="$databases ?? []" />

        <!-- Main Content -->
        <div class="main-container">
            <x-table-sidebar :tables="$tables ?? []" />

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
                                </div>Quick Actions>
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
                                <div class="action-card" onclick="$('#createDatabaseModal').modal('open')">
                                    <i class="material-icons">add_circle</i>
                                    <h6>Create Database</h6>
                                    <p>Add a new database</p>
                                </div>
                                <div class="action-card" onclick="window.location.href='{{ route('users.list') }}'">
                                    <i class="material-icons">group</i>
                                    <h6>Manage Users</h6>
                                    <p>Create and manage users</p>
                                </div>
                                <div class="action-card" onclick="$('#dropDatabaseModal').modal('open')">
                                    <i class="material-icons">delete</i>
                                    <h6>Drop Database</h6>
                                    <p>Remove a database</p>
                                </div>
                            </div>
                            atabase management and operations        </div>

                        <!-- Available Databases -->
                        <div class="info-card">
                            <h5 style="margin-top: 0; display: flex; align-items: center; justify-content: space-between;">
                                <span>
                                    <i class="material-icons" style="vertical-align: middle; color: #5c6bc0;">storage</i>
                                    Available Databases
                                </span>
                                <span style="font-size: 14px; color: #757575; font-weight: 400;">{{ count($databases) }} database(s)</span>
                            </h5>
                            
                            @if(count($databases) > 0)
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; margin-top: 20px;">
                                    @foreach($databases as $db)
                                        <div class="database-card" onclick="window.location.href='{{ route('database.select', ['database' => $db]) }}'">
                                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                                <div style="width: 40px; height: 40px; background: #e8eaf6; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <i class="material-icons" style="color: #5c6bc0; font-size: 20px;">storage</i>
                                                </div>
                                                <div style="flex: 1; min-width: 0;">
                                                    <h6 style="margin: 0; font-size: 16px; font-weight: 500; color: #424242; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $db }}</h6>
                                                    <p style="margin: 4px 0 0 0; font-size: 12px; color: #757575;">Click to select</p>
                                                </div>
                                            </div>
                                            <div style="display: flex; gap: 8px; padding-top: 12px; border-top: 1px solid #e0e0e0;">
                                                <button onclick="event.stopPropagation(); window.location.href='{{ route('database.select', ['database' => $db]) }}'" class="btn-small waves-effect waves-light indigo lighten-1" style="flex: 1; height: 32px; line-height: 32px; padding: 0 12px;">
                                                    <i class="material-icons left" style="font-size: 16px;">folder_open</i>
                                                    Open
                                                </button>
                                                <button onclick="event.stopPropagation(); dropDatabaseFromCard('{{ $db }}')" class="btn-small waves-effect waves-light red lighten-1" style="height: 32px; line-height: 32px; padding: 0 12px;">
                                                    <i class="material-icons" style="font-size: 16px;">delete</i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="text-align: center; padding: 40px; color: #757575;">
                                    <i class="material-icons" style="font-size: 48px; color: #bdbdbd; margin-bottom: 12px;">inbox</i>
                                    <p style="margin: 0;">No databases found. Create one to get started!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Database Selected View -->
                    <div style="max-width: 1400px;">
                        <h4 style="margin-top: 0;">{{ session('current_database') }}</h4>
                        <p style="color: #616161; margin-bottom: 30px;">Database management and operations</p>

                        <!-- Tables Overview -->
                        <div class="info-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <h5 style="margin: 0;">Tables in this database</h5>
                                <button class="btn waves-effect waves-light indigo lighten-1" onclick="$('#createTableModal').modal('open')">
                                    <i class="material-icons left">add</i>Create Table
                                </button>
                            </div>
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
                                                    <a href="{{ route('database.table', ['database' => session('current_database'), 'table' => $table]) }}">Browse</a>
                                                    <a href="#" onclick="event.preventDefault(); dropTableFromList('{{ $table }}')" class="delete">Drop</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div style="text-align: center; padding: 40px; color: #757575;">
                                    <i class="material-icons" style="font-size: 48px; margin-bottom: 10px;">inbox</i>
                                    <p>No tables found in this database</p>
                                    <button class="btn waves-effect waves-light indigo lighten-1" onclick="$('#createTableModal').modal('open')" style="margin-top: 16px;">
                                        <i class="material-icons left">add</i>Create Your First Table
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </main>
        </div>

        <!-- Create Database Modal -->
        <div id="createDatabaseModal" class="modal" style="max-width: 500px; overflow: visible;">
            <div class="modal-content" style="overflow: visible;">
                <h5 style="margin-top: 0; color: #5c6bc0;">
                    <i class="material-icons" style="vertical-align: middle;">add_circle</i>
                    Create New Database
                </h5>
                <form action="{{ route('database.create') }}" method="POST">
                    @csrf
                    <div class="input-field">
                        <input id="database_name" name="database_name" type="text" required pattern="[a-zA-Z0-9_]+" maxlength="64">
                        <label for="database_name">Database Name</label>
                        <span class="helper-text">Only letters, numbers, and underscores allowed</span>
                    </div>
                    <div class="input-field">
                        <select id="charset" name="charset">
                            <option value="utf8mb4" selected>utf8mb4</option>
                            <option value="utf8">utf8</option>
                            <option value="latin1">latin1</option>
                        </select>
                        <label>Character Set</label>
                    </div>
                    <div class="input-field">
                        <select id="collation" name="collation">
                            <option value="utf8mb4_unicode_ci" selected>utf8mb4_unicode_ci</option>
                            <option value="utf8mb4_general_ci">utf8mb4_general_ci</option>
                            <option value="utf8_general_ci">utf8_general_ci</option>
                        </select>
                        <label>Collation</label>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 16px 0 0 0; margin-top: 20px;">
                        <button type="button" class="modal-close waves-effect waves-light btn-flat">Cancel</button>
                        <button type="submit" class="waves-effect waves-light btn indigo lighten-1">
                            <i class="material-icons left">add</i>Create
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Drop Database Modal -->
        <div id="dropDatabaseModal" class="modal" style="max-width: 500px; overflow: visible;">
            <div class="modal-content" style="overflow: visible;">
                <h5 style="margin-top: 0; color: #f44336;">
                    <i class="material-icons" style="vertical-align: middle;">warning</i>
                    Drop Database
                </h5>
                <p style="color: #757575;">Select a database to permanently delete. This action cannot be undone!</p>
                <form id="dropDatabaseForm" method="POST">
                    @csrf
                    <div class="input-field" style="overflow: visible;">
                        <select id="drop_database_select" name="database" required>
                            <option value="" disabled selected>Choose database</option>
                            @if(isset($databases))
                                @foreach($databases as $db)
                                    <option value="{{ $db }}">{{ $db }}</option>
                                @endforeach
                            @endif
                        </select>
                        <label>Database to Drop</label>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 16px 0 0 0; margin-top: 20px;">
                        <button type="button" class="modal-close waves-effect waves-light btn-flat">Cancel</button>
                        <button type="submit" class="waves-effect waves-light btn red">
                            <i class="material-icons left">delete</i>Drop
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Create Table Modal -->
        <div id="createTableModal" class="modal" style="max-width: 700px; max-height: 90%;">
            <div class="modal-content">
                <h5 style="margin-top: 0; color: #5c6bc0;">
                    <i class="material-icons" style="vertical-align: middle;">table_chart</i>
                    Create New Table
                </h5>
                <form action="" method="POST" id="createTableForm">
                    @csrf
                    <input type="hidden" id="createTableDatabase" name="database" value="{{ session('current_database') }}">
                    <div class="input-field">
                        <input id="table_name" name="table_name" type="text" required pattern="[a-zA-Z0-9_]+" maxlength="64">
                        <label for="table_name">Table Name</label>
                        <span class="helper-text">Only letters, numbers, and underscores allowed</span>
                    </div>

                    <h6 style="color: #757575; font-size: 14px; font-weight: 600; margin-top: 20px; margin-bottom: 10px;">Columns:</h6>
                    <div id="columnsContainer">
                        <!-- Initial column row -->
                        <div class="column-row" style="background: #f5f5f5; padding: 12px; border-radius: 4px; margin-bottom: 8px; position: relative;">
                            <div class="row" style="margin-bottom: 0;">
                                <div class="col s3">
                                    <label style="font-size: 11px; color: #9e9e9e;">Column Name</label>
                                    <input type="text" name="columns[0][name]" required style="margin-bottom: 0; margin-top: 2px; height: 35px;">
                                </div>
                                <div class="col s3">
                                    <label style="font-size: 11px; color: #9e9e9e;">Type</label>
                                    <select name="columns[0][type]" required class="browser-default" style="display: block; margin-top: 2px; height: 35px; border: 1px solid #9e9e9e; border-radius: 4px; padding: 0 8px; background-color: white;">
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
                                <div class="col s2">
                                    <label style="font-size: 11px; color: #9e9e9e;">Length</label>
                                    <input type="text" name="columns[0][length]" style="margin-bottom: 0; margin-top: 2px; height: 35px;">
                                </div>
                                <div class="col s4" style="padding-top: 20px;">
                                    <p style="margin: 0 0 5px 0;">
                                        <label>
                                            <input type="checkbox" name="columns[0][nullable]" value="1" />
                                            <span style="font-size: 12px;">Nullable</span>
                                        </label>
                                    </p>
                                    <p style="margin: 0 0 5px 0;">
                                        <label>
                                            <input type="checkbox" name="columns[0][primary]" value="1" />
                                            <span style="font-size: 12px;">Primary Key</span>
                                        </label>
                                    </p>
                                    <p style="margin: 0;">
                                        <label>
                                            <input type="checkbox" name="columns[0][auto_increment]" value="1" />
                                            <span style="font-size: 12px;">Auto Increment</span>
                                        </label>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn-small waves-effect waves-light indigo lighten-1" onclick="addColumnRow()" style="margin-top: 10px;">
                        <i class="material-icons left" style="font-size: 18px;">add</i>Add Column
                    </button>

                    <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 16px 0 0 0; margin-top: 20px;">
                        <button type="button" class="modal-close waves-effect waves-light btn-flat">Cancel</button>
                        <button type="submit" class="waves-effect waves-light btn indigo lighten-1">
                            <i class="material-icons left">add</i>Create Table
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            $(document).ready(function(){
                // Initialize Material components
                M.AutoInit();
                
                // Initialize modals
                $('.modal').modal();
                
                // Initialize dropdown for database selection (exclude modal selects)
                $('#database-select, #drop_database_select, #charset, #collation').formSelect();
                
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
                        window.location.href = '{{ url("/main/database") }}/' + encodeURIComponent(selectedDb);
                    }
                });
                
                // Handle drop database form submission
                $('#dropDatabaseForm').on('submit', function(e) {
                    e.preventDefault();
                    var database = $('#drop_database_select').val();
                    if (database && confirm('Are you sure you want to drop database "' + database + '"? This action cannot be undone!')) {
                        this.action = '{{ url("/main/database") }}/' + encodeURIComponent(database) + '/drop';
                        this.submit();
                    }
                });
                
                // Handle create table form submission
                $('#createTableForm').on('submit', function(e) {
                    var database = $('#createTableDatabase').val();
                    if (database) {
                        this.action = '{{ url("/main/database") }}/' + encodeURIComponent(database) + '/table/create';
                    } else {
                        e.preventDefault();
                        alert('Please select a database first');
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

            // Function to drop database from card
            function dropDatabaseFromCard(database) {
                if (confirm('Are you sure you want to drop database "' + database + '"? This action cannot be undone!')) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ url("/main/database") }}/' + encodeURIComponent(database) + '/drop';
                    
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            // Function to drop table from list
            function dropTableFromList(table) {
                if (confirm('Are you sure you want to drop table "' + table + '"? This action cannot be undone!')) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ url("/main/database") }}/' + encodeURIComponent('{{ session("current_database") }}') + '/' + encodeURIComponent(table) + '/drop';
                    
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            // Column row counter for create table form
            let columnRowCount = 1;

            // Function to add column row in create table modal
            function addColumnRow() {
                const container = document.getElementById('columnsContainer');
                const newRow = document.createElement('div');
                newRow.className = 'column-row';
                newRow.style.cssText = 'background: #f5f5f5; padding: 12px; border-radius: 4px; margin-bottom: 8px; position: relative;';
                
                newRow.innerHTML = `
                    <div class="row" style="margin-bottom: 0;">
                        <div class="col s3">
                            <label style="font-size: 11px; color: #9e9e9e;">Column Name</label>
                            <input type="text" name="columns[${columnRowCount}][name]" required style="margin-bottom: 0; margin-top: 2px; height: 35px;">
                        </div>
                        <div class="col s3">
                            <label style="font-size: 11px; color: #9e9e9e;">Type</label>
                            <select name="columns[${columnRowCount}][type]" required class="browser-default" style="display: block; margin-top: 2px; height: 35px; border: 1px solid #9e9e9e; border-radius: 4px; padding: 0 8px; background-color: white;">
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
                        <div class="col s2">
                            <label style="font-size: 11px; color: #9e9e9e;">Length</label>
                            <input type="text" name="columns[${columnRowCount}][length]" style="margin-bottom: 0; margin-top: 2px; height: 35px;">
                        </div>
                        <div class="col s3" style="padding-top: 20px;">
                            <p style="margin: 0 0 5px 0;">
                                <label>
                                    <input type="checkbox" name="columns[${columnRowCount}][nullable]" value="1" />
                                    <span style="font-size: 12px;">Nullable</span>
                                </label>
                            </p>
                            <p style="margin: 0 0 5px 0;">
                                <label>
                                    <input type="checkbox" name="columns[${columnRowCount}][primary]" value="1" />
                                    <span style="font-size: 12px;">Primary Key</span>
                                </label>
                            </p>
                            <p style="margin: 0;">
                                <label>
                                    <input type="checkbox" name="columns[${columnRowCount}][auto_increment]" value="1" />
                                    <span style="font-size: 12px;">Auto Increment</span>
                                </label>
                            </p>
                        </div>
                        <div class="col s1" style="padding-top: 20px;">
                            <button type="button" class="btn-small red lighten-1" onclick="this.closest('.column-row').remove()" style="padding: 0 8px;">
                                <i class="material-icons" style="font-size: 18px;">delete</i>
                            </button>
                        </div>
                    </div>
                `;
                
                container.appendChild(newRow);
                columnRowCount++;
            }
        </script>
    </body>
</html>
