@extends('layouts.app')

@section('title', 'Role & Permission Management - Omkaar Logistics')

@section('styles')
<style>
    .master-wrapper {
        display: flex;
        min-height: calc(100vh - 148px);
        height: auto;
        gap: 0;
        border: 1px solid #aaa;
        background: #f0f0f0;
        font-size: 12px;
        font-family: 'Segoe UI', Tahoma, sans-serif;
        align-items: stretch;
    }

    .master-list-panel {
        width: 320px;
        min-width: 320px;
        border-right: 2px solid #999;
        display: flex;
        flex-direction: column;
        background: #fff;
        min-height: 100%;
    }

    .master-list-header {
        background: #003087;
        color: #fff;
        padding: 8px 10px;
        font-weight: bold;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    .master-list-search {
        padding: 6px 10px;
        background: #f4f6f9;
        border-bottom: 1px solid #ddd;
    }

    .master-list-search input {
        width: 100%;
        padding: 5px 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 11px;
        box-sizing: border-box;
    }

    .master-list-items {
        flex: 1;
        overflow-y: auto;
        background: #fff;
    }

    .master-item-link {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 10px;
        border-bottom: 1px solid #eee;
        color: #333;
        text-decoration: none;
        transition: background 0.15s;
    }

    .master-item-link:hover {
        background: #eef4ff;
    }

    .master-item-link.active {
        background: #d0e1fd;
        font-weight: bold;
        border-left: 4px solid #003087;
    }

    .master-form-panel {
        flex: 1;
        background: #fff;
        display: flex;
        flex-direction: column;
    }

    .master-title-bar {
        background: #003087;
        color: #fff;
        padding: 8px 15px;
        font-weight: bold;
        font-size: 13px;
        letter-spacing: 0.5px;
    }

    .master-form-body {
        padding: 20px;
        overflow-y: auto;
    }

    .form-group-custom {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
    }

    .form-group-custom label {
        font-weight: bold;
        margin-bottom: 4px;
        color: #333;
    }

    .form-group-custom input[type="text"], .form-group-custom textarea {
        padding: 6px 10px;
        border: 1px solid #aaa;
        border-radius: 3px;
        font-size: 12px;
    }

    .btn-action {
        background: #003087;
        color: #fff;
        border: 1px solid #002060;
        padding: 6px 16px;
        font-size: 12px;
        font-weight: bold;
        border-radius: 3px;
        cursor: pointer;
    }

    .btn-delete {
        background: none;
        border: none;
        color: #d32f2f;
        cursor: pointer;
        font-size: 14px;
        padding: 2px 6px;
    }

    .permission-group {
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 12px;
        background: #fafafa;
    }

    .permission-group-header {
        background: #e8f0fe;
        padding: 6px 12px;
        font-weight: bold;
        color: #003087;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 8px;
        padding: 10px;
    }

    .permission-item {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        padding: 5px 8px;
        border: 1px solid #e0e0e0;
        border-radius: 3px;
    }
</style>
@endsection

@section('content')
<div class="master-wrapper">
    {{-- Left Panel --}}
    <div class="master-list-panel">
        <div class="master-list-header">🛡 Roles</div>
        <div class="master-list-search">
            <input type="text" id="roleSearch" placeholder="🔍 Filter Roles..." oninput="filterRoles(this.value)">
        </div>
        <div class="master-list-items">
            @foreach($roles as $r)
                @php
                    $isSys = $r->is_system || strtoupper($r->name) === 'ADMIN';
                    $isSelected = ($selected->id == $r->id);
                @endphp
                <div class="master-item-link role-row-item {{ $isSelected ? 'active' : '' }}">
                    <a href="{{ route('system.role.load', $r->id) }}" style="text-decoration:none; color:inherit; flex:1; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <span style="font-weight:700;">{{ $r->name }}</span>
                            @if($isSys)
                                <span style="font-size:10px; background:#e1f5fe; color:#0288d1; padding:1px 5px; border-radius:3px; margin-left:4px;">🔒 System</span>
                            @endif
                        </div>
                        <span style="font-size:11px; color:#666; background:#f0f0f0; padding:1px 6px; border-radius:10px;">
                            {{ $r->permissions->count() }} Perms
                        </span>
                    </a>
                    @if(!$isSys)
                        <form method="POST" action="{{ route('system.role.destroy', $r->id) }}" data-confirm="Delete role '{{ $r->name }}'?" style="display:inline; margin-left:6px;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-delete" title="Delete Role">❌</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="master-form-panel">
        <div class="master-title-bar" style="display:flex; justify-content:space-between; align-items:center;">
            <span>Role Management {{ $selected->id ? '- Edit Role #' . $selected->id . ' (' . $selected->name . ')' : '- Create Role' }}</span>
            @if($selected->id)
                <a href="{{ route('system.role') }}" class="btn-action" style="text-decoration:none; background:#e8f5e9; color:#1b5e20; border-color:#81c784; font-size:11px; padding:3px 10px;">➕ New Role</a>
            @endif
        </div>

        <form method="POST" action="{{ route('system.role.store') }}" class="master-form-body">
            @csrf
            <input type="hidden" name="id" value="{{ $selected->id }}">

            @if($errors->any())
                <div style="background:#ffebee; color:#c62828; border:1px solid #ef9a9a; padding:8px 12px; margin-bottom:15px; border-radius:4px;">
                    @foreach($errors->all() as $error) <div>⚠️ {{ $error }}</div> @endforeach
                </div>
            @endif

            @php
                $isSystemRole = $selected->id && ($selected->is_system || strtoupper($selected->name) === 'ADMIN');
            @endphp

            @if($isSystemRole)
                <div style="background:#f3e8ff; color:#6b21a8; border:1px solid #d8b4fe; padding:8px 12px; margin-bottom:15px; border-radius:4px; font-weight:600;">
                    🔒 <strong>Protected System Role:</strong> System Administrator role contains full unrestricted system access.
                </div>
            @endif

            <div style="display:flex; gap:15px;">
                <div class="form-group-custom" style="flex:1;">
                    <label for="name">Role Name <span style="color:red;">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $selected->name) }}" required maxlength="50" placeholder="e.g. MANAGER, OPERATOR, ACCOUNTANT" {{ $isSystemRole ? 'readonly style=background-color:#f0f0f0;' : '' }} style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                </div>
                <div class="form-group-custom" style="flex:2;">
                    <label for="description">Description</label>
                    <input type="text" name="description" id="description" value="{{ old('description', $selected->description) }}" maxlength="255" placeholder="Role duties & privileges...">
                </div>
            </div>

            <div style="margin-top:15px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <label style="font-weight:bold; font-size:13px; color:#003087;">Assign Permissions to Role:</label>
                    <div>
                        <button type="button" onclick="toggleAllPerms(true)" style="background:#e3f2fd; border:1px solid #90caf9; color:#0d47a1; padding:2px 8px; border-radius:3px; font-size:11px; font-weight:bold; cursor:pointer;">Select All</button>
                        <button type="button" onclick="toggleAllPerms(false)" style="background:#f5f5f5; border:1px solid #ccc; color:#333; padding:2px 8px; border-radius:3px; font-size:11px; font-weight:bold; cursor:pointer;">Clear All</button>
                    </div>
                </div>

                @foreach($permissions as $module => $modulePerms)
                    <div class="permission-group">
                        <div class="permission-group-header">
                            <span>📂 {{ $module }} Module</span>
                            <small style="cursor:pointer; color:#003087; text-decoration:underline;" onclick="toggleModulePerms('{{ Str::slug($module) }}')">Toggle Group</small>
                        </div>
                        <div class="permission-grid">
                            @foreach($modulePerms as $p)
                                @php
                                    $checked = in_array($p->id, old('permissions', $selectedPermissionIds));
                                @endphp
                                <label class="permission-item" style="cursor:pointer;">
                                    <input type="checkbox" name="permissions[]" value="{{ $p->id }}" class="perm-cb module-cb-{{ Str::slug($module) }}" {{ $checked ? 'checked' : '' }}>
                                    <span style="font-weight:600;">{{ $p->display_name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top:20px; display:flex; gap:10px;">
                <button type="submit" class="btn-action">
                    {{ $selected->id ? '💾 Update Role' : '➕ Save Role' }}
                </button>
                @if($selected->id)
                    <a href="{{ route('system.role') }}" class="btn-action" style="text-decoration:none; background:#757575; border-color:#616161;">Cancel</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterRoles(q) {
        q = q.toLowerCase().trim();
        document.querySelectorAll('.role-row-item').forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(q) ? 'flex' : 'none';
        });
    }

    function toggleAllPerms(status) {
        document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = status);
    }

    function toggleModulePerms(slug) {
        const cbs = document.querySelectorAll('.module-cb-' + slug);
        const allChecked = Array.from(cbs).every(cb => cb.checked);
        cbs.forEach(cb => cb.checked = !allChecked);
    }
</script>
@endsection
