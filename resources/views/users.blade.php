<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>User Management - Database Admin</title>
        
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
                height: calc(100vh - 64px);
                margin-top: 64px;
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
            .user-card {
                background: white;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 16px;
                transition: all 0.2s;
            }
            .user-card:hover {
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            }
            .user-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 12px;
            }
            .user-info {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .user-avatar {
                width: 48px;
                height: 48px;
                background: #e8eaf6;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .user-avatar i {
                color: #5c6bc0;
                font-size: 24px;
            }
            .user-details h6 {
                margin: 0;
                font-size: 18px;
                font-weight: 500;
                color: #424242;
            }
            .user-details p {
                margin: 4px 0 0 0;
                font-size: 14px;
                color: #757575;
            }
            .user-actions {
                display: flex;
                gap: 8px;
            }
            .user-actions button {
                height: 36px;
                line-height: 36px;
                padding: 0 16px;
            }
            .grants-section {
                background: #f5f5f5;
                border-radius: 4px;
                padding: 12px;
                margin-top: 12px;
            }
            .grants-section h6 {
                margin: 0 0 8px 0;
                font-size: 12px;
                font-weight: 600;
                color: #757575;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .grant-item {
                background: white;
                border-radius: 4px;
                padding: 8px 12px;
                margin-bottom: 4px;
                font-size: 13px;
                font-family: 'Courier New', monospace;
                color: #424242;
            }
            .fab-button {
                position: fixed;
                right: 30px;
                bottom: 30px;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: #5c6bc0;
                box-shadow: 0 4px 8px rgba(0,0,0,0.3);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s;
                z-index: 100;
            }
            .fab-button:hover {
                background: #3f51b5;
                box-shadow: 0 6px 12px rgba(0,0,0,0.4);
            }
            .fab-button i {
                color: white;
                font-size: 24px;
            }
        </style>
    </head>
    <body>
        @php
            $customButtons = '
                <a href="' . route('database.main') . '" class="btn waves-effect waves-light white" style="color: #5c6bc0; height: 36px; line-height: 36px; padding: 0 16px;">
                    <i class="material-icons left" style="line-height: inherit;">arrow_back</i>
                    Back to Dashboard
                </a>
            ';
        @endphp
        <x-navbar pageTitle="User Management" :customButtons="$customButtons" />

        <!-- Main Content -->
        <div class="main-container">
            <div style="max-width: 1200px; margin: 0 auto;">
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

                <h4 style="margin-top: 0; display: flex; align-items: center; gap: 12px;">
                    <i class="material-icons" style="color: #5c6bc0; font-size: 36px;">group</i>
                    Database Users
                </h4>
                <p style="color: #757575; margin-bottom: 30px;">Manage database users, passwords, and permissions</p>

                <!-- Users List -->
                @if(isset($users) && count($users) > 0)
                    @foreach($users as $userData)
                        <div class="user-card">
                            <div class="user-header">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <i class="material-icons">person</i>
                                    </div>
                                    <div class="user-details">
                                        <h6>{{ $userData['user'] }} @ {{ $userData['host'] }}</h6>
                                        <p>Host: {{ $userData['host'] }}</p>
                                    </div>
                                </div>
                                <div class="user-actions">
                                    <button class="btn-small waves-effect waves-light indigo lighten-1" onclick="openEditUser('{{ $userData['user'] }}', '{{ $userData['host'] }}')">
                                        <i class="material-icons left" style="font-size: 18px;">edit</i>
                                        Edit
                                    </button>
                                    <button class="btn-small waves-effect waves-light blue" onclick="openPermissions('{{ $userData['user'] }}', '{{ $userData['host'] }}')">
                                        <i class="material-icons left" style="font-size: 18px;">lock</i>
                                        Permissions
                                    </button>
                                    <button class="btn-small waves-effect waves-light red" onclick="deleteUser('{{ $userData['user'] }}', '{{ $userData['host'] }}')">
                                        <i class="material-icons left" style="font-size: 18px;">delete</i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                            
                            @if(isset($userData['grants']) && count($userData['grants']) > 0)
                                <div class="grants-section">
                                    <h6>Current Privileges</h6>
                                    @foreach($userData['grants'] as $grant)
                                        <div class="grant-item">{{ implode('', array_values((array)$grant)) }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="info-card" style="text-align: center; padding: 60px;">
                        <i class="material-icons" style="font-size: 64px; color: #bdbdbd; margin-bottom: 16px;">group</i>
                        <h6 style="margin: 0 0 8px 0; font-weight: 500; color: #757575;">No users found</h6>
                        <p style="margin: 0; color: #9e9e9e;">Create your first user to get started</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Floating Action Button -->
        <div class="fab-button" onclick="$('#createUserModal').modal('open')">
            <i class="material-icons">add</i>
        </div>

        <!-- Create User Modal -->
        <div id="createUserModal" class="modal" style="max-width: 500px;">
            <div class="modal-content">
                <h5 style="margin-top: 0; color: #5c6bc0;">
                    <i class="material-icons" style="vertical-align: middle;">person_add</i>
                    Create New User
                </h5>
                <form action="{{ route('users.create') }}" method="POST">
                    @csrf
                    <div class="input-field">
                        <input id="username" name="username" type="text" required maxlength="32">
                        <label for="username">Username</label>
                    </div>
                    <div class="input-field">
                        <input id="host" name="host" type="text" required value="%" maxlength="255">
                        <label for="host">Host</label>
                        <span class="helper-text">Use '%' for all hosts, 'localhost' for local only</span>
                    </div>
                    <div class="input-field">
                        <input id="password" name="password" type="password" required>
                        <label for="password">Password</label>
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

        <!-- Edit User Modal -->
        <div id="editUserModal" class="modal" style="max-width: 500px;">
            <div class="modal-content">
                <h5 style="margin-top: 0; color: #5c6bc0;">
                    <i class="material-icons" style="vertical-align: middle;">edit</i>
                    Update User Password
                </h5>
                <form id="editUserForm" method="POST">
                    @csrf
                    <input type="hidden" id="edit_host" name="host">
                    <div class="input-field">
                        <input id="edit_username" type="text" disabled>
                        <label for="edit_username">Username</label>
                    </div>
                    <div class="input-field">
                        <input id="edit_password" name="password" type="password" required>
                        <label for="edit_password">New Password</label>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 16px 0 0 0; margin-top: 20px;">
                        <button type="button" class="modal-close waves-effect waves-light btn-flat">Cancel</button>
                        <button type="submit" class="waves-effect waves-light btn indigo lighten-1">
                            <i class="material-icons left">save</i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Permissions Modal -->
        <div id="permissionsModal" class="modal" style="max-width: 600px;">
            <div class="modal-content">
                <h5 style="margin-top: 0; color: #5c6bc0;">
                    <i class="material-icons" style="vertical-align: middle;">lock</i>
                    Manage Permissions
                </h5>
                <form id="permissionsForm" method="POST">
                    @csrf
                    <input type="hidden" id="perm_host" name="host">
                    <div class="input-field">
                        <input id="perm_username" type="text" disabled>
                        <label for="perm_username">Username</label>
                    </div>
                    <div class="input-field">
                        <select id="perm_database" name="database" required>
                            <option value="" disabled selected>Choose database</option>
                            <option value="*">All Databases (*.*)</option>
                            @if(isset($databases))
                                @foreach($databases as $db)
                                    <option value="{{ $db }}">{{ $db }}</option>
                                @endforeach
                            @endif
                        </select>
                        <label>Database</label>
                    </div>
                    <div style="margin: 20px 0;">
                        <label style="font-size: 14px; color: #757575; font-weight: 500;">Select Privileges:</label>
                        <p>
                            <label>
                                <input type="checkbox" name="privileges[]" value="ALL PRIVILEGES" />
                                <span>ALL PRIVILEGES</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" name="privileges[]" value="SELECT" />
                                <span>SELECT</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" name="privileges[]" value="INSERT" />
                                <span>INSERT</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" name="privileges[]" value="UPDATE" />
                                <span>UPDATE</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" name="privileges[]" value="DELETE" />
                                <span>DELETE</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" name="privileges[]" value="CREATE" />
                                <span>CREATE</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" name="privileges[]" value="DROP" />
                                <span>DROP</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" name="privileges[]" value="ALTER" />
                                <span>ALTER</span>
                            </label>
                        </p>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 16px 0 0 0; margin-top: 20px;">
                        <button type="button" class="modal-close waves-effect waves-light btn-flat">Cancel</button>
                        <button type="submit" class="waves-effect waves-light btn indigo lighten-1">
                            <i class="material-icons left">save</i>Grant Permissions
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete User Form -->
        <form id="deleteUserForm" method="POST" style="display: none;">
            @csrf
            <input type="hidden" id="delete_host" name="host">
        </form>

        <script>
            $(document).ready(function(){
                // Initialize Material components
                M.AutoInit();
                
                // Initialize modals
                $('.modal').modal();
                
                // Initialize selects
                $('select').formSelect();
            });

            function openEditUser(username, host) {
                $('#edit_username').val(username);
                $('#edit_host').val(host);
                $('#editUserForm').attr('action', '{{ url("/users") }}/' + encodeURIComponent(username) + '/update');
                $('#editUserModal').modal('open');
                
                // Update label
                setTimeout(function() {
                    M.updateTextFields();
                }, 100);
            }

            function openPermissions(username, host) {
                $('#perm_username').val(username + '@' + host);
                $('#perm_host').val(host);
                $('#permissionsForm').attr('action', '{{ url("/users") }}/' + encodeURIComponent(username) + '/permissions');
                $('#permissionsModal').modal('open');
                
                // Update label and reinitialize select
                setTimeout(function() {
                    M.updateTextFields();
                    $('select').formSelect();
                }, 100);
            }

            function deleteUser(username, host) {
                if (confirm('Are you sure you want to delete user "' + username + '@' + host + '"? This action cannot be undone!')) {
                    $('#delete_host').val(host);
                    $('#deleteUserForm').attr('action', '{{ url("/users") }}/' + encodeURIComponent(username) + '/delete');
                    $('#deleteUserForm').submit();
                }
            }
        </script>
    </body>
</html>

