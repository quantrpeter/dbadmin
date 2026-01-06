<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Database Admin - Login</title>
        
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
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Roboto', sans-serif;
            }
            .login-container {
                width: 100%;
                max-width: 450px;
                padding: 20px;
            }
            .card {
                border-radius: 8px;
            }
            .brand-logo {
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 30px;
            }
            .brand-icon {
                width: 60px;
                height: 60px;
                background: #667eea;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 15px auto;
            }
            .input-field input:focus {
                border-bottom: 1px solid #667eea !important;
                box-shadow: 0 1px 0 0 #667eea !important;
            }
            .input-field input:focus + label {
                color: #667eea !important;
            }
            .btn-submit {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                width: 100%;
                text-transform: none;
                font-size: 16px;
                padding: 0 20px;
                height: 48px;
                line-height: 48px;
            }
            .btn-submit:hover {
                background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            }
            .footer-text {
                text-align: center;
                color: white;
                margin-top: 20px;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <!-- Header -->
            <div class="brand-logo">
                <div style="text-align: center;">
                    <div class="brand-icon">
                        <i class="material-icons" style="color: white; font-size: 36px;">storage</i>
                    </div>
                    <h4 style="color: white; margin: 0 0 10px 0; font-weight: 500;">DBAdmin</h4>
                    <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 15px;">Connect to your MySQL database</p>
                </div>
            </div>

            <!-- Login Card -->
            <div class="card">
                <div class="card-content">
                    @if(session('error'))
                        <div class="card-panel red lighten-4" style="padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                            <i class="material-icons tiny" style="color: #c62828; vertical-align: middle; margin-right: 8px;">error</i>
                            <span style="color: #c62828;">{{ session('error') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('database.connect') }}" method="POST">
                        @csrf

                        <!-- Host -->
                        <div class="input-field">
                            <i class="material-icons prefix">dns</i>
                            <input type="text" id="host" name="host" value="{{ old('host', 'localhost') }}" required>
                            <label for="host">Host</label>
                        </div>

                        <!-- Port -->
                        <div class="input-field">
                            <i class="material-icons prefix">settings_ethernet</i>
                            <input type="text" id="port" name="port" value="{{ old('port', '3306') }}" required>
                            <label for="port">Port</label>
                        </div>

                        <!-- Username -->
                        <div class="input-field">
                            <i class="material-icons prefix">person</i>
                            <input type="text" id="username" name="username" value="{{ old('username', 'root') }}" required autocomplete="username">
                            <label for="username">Username</label>
                        </div>

                        <!-- Password -->
                        <div class="input-field">
                            <i class="material-icons prefix">lock</i>
                            <input type="password" id="password" name="password" autocomplete="current-password">
                            <label for="password">Password</label>
                            <span class="helper-text">Leave blank if no password is set</span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-large waves-effect waves-light btn-submit">
                            <i class="material-icons left">power</i>
                            Connect to Database
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer-text">
                Similar to phpMyAdmin • Built with Laravel
            </div>
        </div>

        <script>
            $(document).ready(function(){
                M.updateTextFields();
            });
        </script>
    </body>
</html>

