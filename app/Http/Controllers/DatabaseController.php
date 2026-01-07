<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class DatabaseController extends Controller
{
    /**
     * Show the login page
     */
    public function showLogin()
    {
        return view('login');
    }

    /**
     * Handle database connection
     */
    public function connect(Request $request)
    {
        $request->validate([
            'host' => 'required|string',
            'port' => 'required|numeric',
            'username' => 'required|string',
            'password' => 'nullable|string',
        ]);

        $host = $request->input('host');
        $port = $request->input('port');
        $username = $request->input('username');
        $password = $request->input('password', '');

        try {
            // Create a temporary database configuration
            config([
                'database.connections.temp_mysql' => [
                    'driver' => 'mysql',
                    'host' => $host,
                    'port' => $port,
                    'database' => null,
                    'username' => $username,
                    'password' => $password,
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => false,
                ]
            ]);

            // Test the connection
            DB::connection('temp_mysql')->getPdo();

            // Store connection details in session
            session([
                'db_host' => $host,
                'db_port' => $port,
                'db_username' => $username,
                'db_password' => $password,
                'db_connected' => true,
            ]);

            return redirect()->route('database.main')->with('success', 'Successfully connected to database server!');
        } catch (Exception $e) {
            return back()
                ->withInput($request->except('password'))
                ->with('error', 'Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Show the main database page
     */
    public function main()
    {
        if (!session('db_connected')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $this->setupConnection();
            
            // Get list of databases
            $databases = DB::connection('temp_mysql')
                ->select('SHOW DATABASES');
            
            $databaseList = array_map(function($db) {
                return array_values((array)$db)[0];
            }, $databases);

            $tables = [];
            if (session('current_database')) {
                // Get tables for the selected database
                $dbName = session('current_database');
                DB::connection('temp_mysql')->statement('USE `' . str_replace('`', '``', $dbName) . '`');
                $tablesResult = DB::connection('temp_mysql')->select('SHOW TABLES');
                $tables = array_map(function($table) {
                    return array_values((array)$table)[0];
                }, $tablesResult);
            }

            return view('main', [
                'databases' => $databaseList,
                'tables' => $tables,
            ]);
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Database error: ' . $e->getMessage());
        }
    }

    /**
     * Select a database
     */
    public function selectDatabase($database)
    {
        if (!session('db_connected')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $this->setupConnection();
            
            // Switch to the selected database (escape with backticks for special characters)
            DB::connection('temp_mysql')->statement('USE `' . str_replace('`', '``', $database) . '`');
            
            session(['current_database' => $database]);
            
            return redirect()->route('database.main')->with('success', 'Database "' . $database . '" selected');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to select database: ' . $e->getMessage());
        }
    }

    /**
     * Deselect current database
     */
    public function deselectDatabase()
    {
        if (!session('db_connected')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        session()->forget('current_database');
        
        return redirect()->route('database.main')->with('success', 'Database deselected');
    }

    /**
     * Show table data
     */
    public function showTable($database, $table)
    {
        if (!session('db_connected')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $this->setupConnection();
            
            // Ensure the database in URL matches session or update session
            if (session('current_database') !== $database) {
                session(['current_database' => $database]);
            }
            
            $dbName = $database;
            DB::connection('temp_mysql')->statement('USE `' . str_replace('`', '``', $dbName) . '`');
            
            // Get list of all tables for sidebar
            $tablesResult = DB::connection('temp_mysql')->select('SHOW TABLES');
            $tables = array_map(function($tableItem) {
                return array_values((array)$tableItem)[0];
            }, $tablesResult);
            
            // Get table data (limit to 100 rows for performance)
            // Escape table name as well
            $escapedTable = str_replace('`', '``', $table);
            $data = DB::connection('temp_mysql')
                ->table(DB::raw('`' . $escapedTable . '`'))
                ->limit(100)
                ->get();
            
            return view('table', [
                'table' => $table,
                'data' => $data,
                'tables' => $tables,
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load table: ' . $e->getMessage());
        }
    }

    /**
     * Create a new database
     */
    public function createDatabase(Request $request)
    {
        if (!session('db_connected')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'database_name' => 'required|string|max:64|regex:/^[a-zA-Z0-9_]+$/',
            'charset' => 'nullable|string',
            'collation' => 'nullable|string',
        ]);

        try {
            $this->setupConnection();
            
            $dbName = $request->input('database_name');
            $charset = $request->input('charset', 'utf8mb4');
            $collation = $request->input('collation', 'utf8mb4_unicode_ci');
            
            // Escape database name
            $escapedDbName = str_replace('`', '``', $dbName);
            
            DB::connection('temp_mysql')->statement(
                "CREATE DATABASE `{$escapedDbName}` CHARACTER SET {$charset} COLLATE {$collation}"
            );
            
            return back()->with('success', "Database '{$dbName}' created successfully!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to create database: ' . $e->getMessage());
        }
    }

    /**
     * Drop a database
     */
    public function dropDatabase($database)
    {
        if (!session('db_connected')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $this->setupConnection();
            
            // Escape database name
            $escapedDbName = str_replace('`', '``', $database);
            
            DB::connection('temp_mysql')->statement("DROP DATABASE `{$escapedDbName}`");
            
            // Clear current database session if it was dropped
            if (session('current_database') === $database) {
                session()->forget('current_database');
            }
            
            return redirect()->route('database.main')->with('success', "Database '{$database}' dropped successfully!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to drop database: ' . $e->getMessage());
        }
    }

    /**
     * List all users
     */
    public function listUsers()
    {
        if (!session('db_connected')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $this->setupConnection();
            
            // Get all users
            $users = DB::connection('temp_mysql')->select('SELECT User, Host FROM mysql.user ORDER BY User, Host');
            
            // Get privileges for each user
            $usersWithPrivileges = [];
            foreach ($users as $user) {
                $grants = DB::connection('temp_mysql')->select(
                    "SHOW GRANTS FOR '{$user->User}'@'{$user->Host}'"
                );
                
                $usersWithPrivileges[] = [
                    'user' => $user->User,
                    'host' => $user->Host,
                    'grants' => $grants,
                ];
            }
            
            // Get list of databases for permissions modal
            $databases = DB::connection('temp_mysql')->select('SHOW DATABASES');
            $databaseList = array_map(function($db) {
                return array_values((array)$db)[0];
            }, $databases);
            
            return view('users', [
                'users' => $usersWithPrivileges,
                'databases' => $databaseList,
            ]);
        } catch (Exception $e) {
            return redirect()->route('database.main')->with('error', 'Failed to load users: ' . $e->getMessage());
        }
    }

    /**
     * Create a new user
     */
    public function createUser(Request $request)
    {
        if (!session('db_connected')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'username' => 'required|string|max:32',
            'host' => 'required|string|max:255',
            'password' => 'required|string|min:1',
        ]);

        try {
            $this->setupConnection();
            
            $username = $request->input('username');
            $host = $request->input('host');
            $password = $request->input('password');
            
            DB::connection('temp_mysql')->statement(
                "CREATE USER '{$username}'@'{$host}' IDENTIFIED BY '{$password}'"
            );
            
            return back()->with('success', "User '{$username}'@'{$host}' created successfully!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Update user password
     */
    public function updateUser(Request $request, $user)
    {
        if (!session('db_connected')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'host' => 'required|string',
            'password' => 'required|string|min:1',
        ]);

        try {
            $this->setupConnection();
            
            $host = $request->input('host');
            $password = $request->input('password');
            
            DB::connection('temp_mysql')->statement(
                "ALTER USER '{$user}'@'{$host}' IDENTIFIED BY '{$password}'"
            );
            
            return back()->with('success', "Password updated for '{$user}'@'{$host}'!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Delete a user
     */
    public function deleteUser(Request $request, $user)
    {
        if (!session('db_connected')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'host' => 'required|string',
        ]);

        try {
            $this->setupConnection();
            
            $host = $request->input('host');
            
            DB::connection('temp_mysql')->statement(
                "DROP USER '{$user}'@'{$host}'"
            );
            
            return back()->with('success', "User '{$user}'@'{$host}' deleted successfully!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Update user permissions
     */
    public function updatePermissions(Request $request, $user)
    {
        if (!session('db_connected')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'host' => 'required|string',
            'database' => 'required|string',
            'privileges' => 'required|array',
        ]);

        try {
            $this->setupConnection();
            
            $host = $request->input('host');
            $database = $request->input('database');
            $privileges = implode(', ', $request->input('privileges'));
            
            // Escape database name
            $escapedDbName = str_replace('`', '``', $database);
            
            DB::connection('temp_mysql')->statement(
                "GRANT {$privileges} ON `{$escapedDbName}`.* TO '{$user}'@'{$host}'"
            );
            
            DB::connection('temp_mysql')->statement('FLUSH PRIVILEGES');
            
            return back()->with('success', "Permissions updated for '{$user}'@'{$host}'!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update permissions: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect from database
     */
    public function disconnect()
    {
        session()->forget([
            'db_host',
            'db_port',
            'db_username',
            'db_password',
            'db_connected',
            'current_database',
        ]);

        return redirect()->route('login')->with('success', 'Disconnected successfully');
    }

    /**
     * Create a new table
     */
    public function createTable(Request $request, $database)
    {
        if (!session('db_connected')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'table_name' => 'required|string|max:64|regex:/^[a-zA-Z0-9_]+$/',
            'columns' => 'required|array|min:1',
            'columns.*.name' => 'required|string|max:64',
            'columns.*.type' => 'required|string',
            'columns.*.length' => 'nullable|string',
            'columns.*.nullable' => 'boolean',
            'columns.*.primary' => 'boolean',
            'columns.*.auto_increment' => 'boolean',
        ]);

        try {
            $this->setupConnection();
            
            // Ensure the database in URL matches session or update session
            if (session('current_database') !== $database) {
                session(['current_database' => $database]);
            }
            
            $dbName = $database;
            DB::connection('temp_mysql')->statement('USE `' . str_replace('`', '``', $dbName) . '`');
            
            $tableName = $request->input('table_name');
            $columns = $request->input('columns');
            
            // Build column definitions
            $columnDefinitions = [];
            $primaryKeys = [];
            
            foreach ($columns as $column) {
                $def = '`' . str_replace('`', '``', $column['name']) . '` ' . $column['type'];
                
                if (!empty($column['length'])) {
                    $def .= '(' . $column['length'] . ')';
                }
                
                if (empty($column['nullable'])) {
                    $def .= ' NOT NULL';
                }
                
                if (!empty($column['auto_increment'])) {
                    $def .= ' AUTO_INCREMENT';
                }
                
                $columnDefinitions[] = $def;
                
                if (!empty($column['primary'])) {
                    $primaryKeys[] = '`' . str_replace('`', '``', $column['name']) . '`';
                }
            }
            
            $sql = 'CREATE TABLE `' . str_replace('`', '``', $tableName) . '` (' . implode(', ', $columnDefinitions);
            
            if (!empty($primaryKeys)) {
                $sql .= ', PRIMARY KEY (' . implode(', ', $primaryKeys) . ')';
            }
            
            $sql .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
            
            DB::connection('temp_mysql')->statement($sql);
            
            return redirect()->route('database.table', ['database' => $dbName, 'table' => $tableName])
                ->with('success', "Table '{$tableName}' created successfully!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to create table: ' . $e->getMessage());
        }
    }

    /**
     * Rename a table
     */
    public function renameTable(Request $request, $database, $table)
    {
        if (!session('db_connected') || !session('current_database')) {
            return redirect()->route('login')->with('error', 'Please login and select a database first');
        }

        $request->validate([
            'new_name' => 'required|string|max:64|regex:/^[a-zA-Z0-9_]+$/',
        ]);

        try {
            $this->setupConnection();
            $dbName = session('current_database');
            DB::connection('temp_mysql')->statement('USE `' . str_replace('`', '``', $dbName) . '`');
            
            $newName = $request->input('new_name');
            $escapedOldTable = str_replace('`', '``', $table);
            $escapedNewTable = str_replace('`', '``', $newName);
            
            DB::connection('temp_mysql')->statement("RENAME TABLE `{$escapedOldTable}` TO `{$escapedNewTable}`");
            
            return redirect()->route('database.table', ['database' => $database, 'table' => $newName])
                ->with('success', "Table renamed from '{$table}' to '{$newName}'!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to rename table: ' . $e->getMessage());
        }
    }

    /**
     * Drop a table
     */
    public function dropTable($database, $table)
    {
        if (!session('db_connected') || !session('current_database')) {
            return redirect()->route('login')->with('error', 'Please login and select a database first');
        }

        try {
            $this->setupConnection();
            $dbName = session('current_database');
            DB::connection('temp_mysql')->statement('USE `' . str_replace('`', '``', $dbName) . '`');
            
            $escapedTable = str_replace('`', '``', $table);
            DB::connection('temp_mysql')->statement("DROP TABLE `{$escapedTable}`");
            
            return redirect()->route('database.main')
                ->with('success', "Table '{$table}' dropped successfully!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to drop table: ' . $e->getMessage());
        }
    }

    /**
     * Add column to table
     */
    public function addColumn(Request $request, $database, $table)
    {
        if (!session('db_connected') || !session('current_database')) {
            return redirect()->route('login')->with('error', 'Please login and select a database first');
        }

        $request->validate([
            'column_name' => 'required|string|max:64|regex:/^[a-zA-Z0-9_]+$/',
            'column_type' => 'required|string',
            'column_length' => 'nullable|string',
            'nullable' => 'boolean',
        ]);

        try {
            $this->setupConnection();
            $dbName = session('current_database');
            DB::connection('temp_mysql')->statement('USE `' . str_replace('`', '``', $dbName) . '`');
            
            $columnName = $request->input('column_name');
            $columnType = $request->input('column_type');
            $columnLength = $request->input('column_length');
            $nullable = $request->input('nullable', false);
            
            $escapedTable = str_replace('`', '``', $table);
            $escapedColumn = str_replace('`', '``', $columnName);
            
            $sql = "ALTER TABLE `{$escapedTable}` ADD COLUMN `{$escapedColumn}` {$columnType}";
            
            if (!empty($columnLength)) {
                $sql .= "({$columnLength})";
            }
            
            if (!$nullable) {
                $sql .= ' NOT NULL';
            }
            
            DB::connection('temp_mysql')->statement($sql);
            
            return back()->with('success', "Column '{$columnName}' added successfully!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to add column: ' . $e->getMessage());
        }
    }

    /**
     * Modify column in table
     */
    public function modifyColumn(Request $request, $database, $table)
    {
        if (!session('db_connected') || !session('current_database')) {
            return redirect()->route('login')->with('error', 'Please login and select a database first');
        }

        $request->validate([
            'column_name' => 'required|string|max:64',
            'new_name' => 'required|string|max:64|regex:/^[a-zA-Z0-9_]+$/',
            'column_type' => 'required|string',
            'column_length' => 'nullable|string',
            'nullable' => 'boolean',
        ]);

        try {
            $this->setupConnection();
            $dbName = session('current_database');
            DB::connection('temp_mysql')->statement('USE `' . str_replace('`', '``', $dbName) . '`');
            
            $columnName = $request->input('column_name');
            $newName = $request->input('new_name');
            $columnType = $request->input('column_type');
            $columnLength = $request->input('column_length');
            $nullable = $request->input('nullable', false);
            
            $escapedTable = str_replace('`', '``', $table);
            $escapedColumn = str_replace('`', '``', $columnName);
            $escapedNewName = str_replace('`', '``', $newName);
            
            $sql = "ALTER TABLE `{$escapedTable}` CHANGE COLUMN `{$escapedColumn}` `{$escapedNewName}` {$columnType}";
            
            if (!empty($columnLength)) {
                $sql .= "({$columnLength})";
            }
            
            if (!$nullable) {
                $sql .= ' NOT NULL';
            }
            
            DB::connection('temp_mysql')->statement($sql);
            
            return back()->with('success', "Column modified successfully!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to modify column: ' . $e->getMessage());
        }
    }

    /**
     * Drop column from table
     */
    public function dropColumn(Request $request, $database, $table)
    {
        if (!session('db_connected') || !session('current_database')) {
            return redirect()->route('login')->with('error', 'Please login and select a database first');
        }

        $request->validate([
            'column_name' => 'required|string|max:64',
        ]);

        try {
            $this->setupConnection();
            $dbName = session('current_database');
            DB::connection('temp_mysql')->statement('USE `' . str_replace('`', '``', $dbName) . '`');
            
            $columnName = $request->input('column_name');
            $escapedTable = str_replace('`', '``', $table);
            $escapedColumn = str_replace('`', '``', $columnName);
            
            DB::connection('temp_mysql')->statement("ALTER TABLE `{$escapedTable}` DROP COLUMN `{$escapedColumn}`");
            
            return back()->with('success', "Column '{$columnName}' dropped successfully!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to drop column: ' . $e->getMessage());
        }
    }

    /**
     * Setup the temporary database connection
     */
    private function setupConnection()
    {
        config([
            'database.connections.temp_mysql' => [
                'driver' => 'mysql',
                'host' => session('db_host'),
                'port' => session('db_port'),
                'database' => null,
                'username' => session('db_username'),
                'password' => session('db_password'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => false,
            ]
        ]);
    }
}

