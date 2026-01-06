<style>
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
</style>

<!-- Sidebar - Tables List -->
<aside class="sidebar">
    @if(session('current_database'))
        <div class="sidebar-section">
            <div class="sidebar-title">Tables in {{ session('current_database') }}</div>
            @if(isset($tables) && count($tables) > 0)
                @foreach($tables as $tableItem)
                    <a href="{{ route('database.table', ['table' => $tableItem]) }}" class="sidebar-item {{ isset($currentTable) && $tableItem === $currentTable ? 'active' : '' }}">
                        <i class="material-icons">table_chart</i>
                        <span>{{ $tableItem }}</span>
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

