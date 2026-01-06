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
     * Show table data
     */
    public function showTable($table)
    {
        if (!session('db_connected') || !session('current_database')) {
            return redirect()->route('login')->with('error', 'Please login and select a database first');
        }

        try {
            $this->setupConnection();
            $dbName = session('current_database');
            DB::connection('temp_mysql')->statement('USE `' . str_replace('`', '``', $dbName) . '`');
            
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
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load table: ' . $e->getMessage());
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

