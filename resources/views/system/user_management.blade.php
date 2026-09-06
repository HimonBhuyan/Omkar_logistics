@extends('layouts.app')

@section('title', 'User Management - Omkaar Logistics')

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
        width: 330px;
        min-width: 330px;
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

    .form-group-custom input[type="text"], .form-group-custom input[type="email"], .form-group-custom input[type="password"], .form-group-custom select {
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

    .badge-status {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: bold;
    }

    .badge-active { background: #e8f5e9; color: #2e7d32; }
    .badge-inactive { background: #ffebee; color: #c62828; }

    /* Override Matrix Table */
    .override-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 11px;
    }

    .override-table th {
        background: #003087;
        color: #fff;
        padding: 6px 10px;
        text-align: left;
    }

    .override-table td {
        padding: 6px 10px;
        border-bottom: 1px solid #eee;
    }

    .override-radio-group {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .radio-label {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        cursor: pointer;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .radio-inherit { background: #f0f0f0; color: #555; }
    .radio-allow { background: #e8f5e9; color: #2e7d32; }
    .radio-deny { background: #ffebee; color: #c62828; }
</style>
@endsection

@section('content')
<div class="master-wrapper">
    {{-- Left Panel --}}
    <div class="master-list-panel">
        <div class="master-list-header" style="display:flex; justify-content:space-between; align-items:center;">
            <span>👥 User Accounts</span>
            <button type="button" onclick="document.getElementById('bulkDeleteForm').submit()" style="background:#d32f2f; color:#fff; border:none; padding:2px 8px; font-size:10px; border-radius:3px; font-weight:bold; cursor:pointer;">Delete Selected</button>
        </div>
        <div class="master-list-search">
            <input type="text" id="userSearch" placeholder="🔍 Filter Users..." oninput="filterUsers(this.value)">
        </div>

        <form id="bulkDeleteForm" method="POST" action="{{ route('system.user.bulk_destroy') }}" data-confirm="Delete selected users?">
            @csrf @method('DELETE')
            <div class="master-list-items">
                @foreach($users as $u)
                    @php
                        $isDeletable = $u->isDeletable();
                        $isSelected = ($selected->id == $u->id);
                        $rolesCount = $u->roles->count();
                    @endphp
                    <div class="master-item-link user-row-item {{ $isSelected ? 'active' : '' }}" style="gap:8px;">
                        @if($isDeletable)
                            <input type="checkbox" name="ids[]" value="{{ $u->id }}" class="row-checkbox" style="margin:0;" onclick="event.stopPropagation();">
                        @else
                            <span style="width:14px; display:inline-block;"></span>
                        @endif

                        <a href="{{ route('system.user.load', $u->id) }}" style="text-decoration:none; color:inherit; flex:1; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <span style="font-weight:700;">{{ $u->username }}</span>
                                <small style="color:#666; font-size:10px; margin-left:4px;">({{ $u->name }})</small>
                                @if($u->phone_number)
                                    <small style="color:#003087; font-size:10px; margin-left:4px; font-weight:600;">📞 {{ $u->phone_number }}</small>
                                @endif
                            </div>
                            <div style="display:flex; gap:4px; align-items:center;">
                                <span class="badge-status {{ $u->is_active ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $u->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                </span>
                            </div>
                        </a>

                        @if($isDeletable)
                            <form method="POST" action="{{ route('system.user.destroy', $u->id) }}" data-confirm="Delete user '{{ $u->username }}'?" onclick="event.stopPropagation();" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete" title="Delete User">❌</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </form>
    </div>

    {{-- Right Panel --}}
    <div class="master-form-panel">
        <div class="master-title-bar" style="display:flex; justify-content:space-between; align-items:center;">
            <span>User Management {{ $selected->id ? '- Edit User #' . $selected->id . ' (' . $selected->username . ')' : '- Create User' }}</span>
            @if($selected->id)
                <a href="{{ route('system.user') }}" class="btn-action" style="text-decoration:none; background:#e8f5e9; color:#1b5e20; border-color:#81c784; font-size:11px; padding:3px 10px;">➕ New User</a>
            @endif
        </div>

        <form method="POST" action="{{ route('system.user.store') }}" class="master-form-body">
            @csrf
            <input type="hidden" name="id" value="{{ $selected->id }}">

            @if($errors->any())
                <div style="background:#ffebee; color:#c62828; border:1px solid #ef9a9a; padding:8px 12px; margin-bottom:15px; border-radius:4px;">
                    @foreach($errors->all() as $error) <div>⚠️ {{ $error }}</div> @endforeach
                </div>
            @endif

            @if(session('success'))
                <div style="background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; padding:8px 12px; margin-bottom:15px; border-radius:4px;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @php
                $isAdminUser = $selected->id && (strtoupper($selected->username) === 'ADMIN');
            @endphp

            @if($isAdminUser)
                <div style="background:#f3e8ff; color:#6b21a8; border:1px solid #d8b4fe; padding:8px 12px; margin-bottom:15px; border-radius:4px; font-weight:600;">
                    🔒 <strong>Protected Admin Account:</strong> Primary system administrator user (ADMIN) cannot be renamed, deleted, or deactivated.
                </div>
            @endif

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:15px;">
                <!-- 1. Full Name first -->
                <div class="form-group-custom">
                    <label for="name">Full Name <span style="color:red;">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $selected->name) }}" required maxlength="100" placeholder="e.g. John Doe">
                </div>

                <!-- 2. Username second with smart suggestions (Create Only) -->
                <div class="form-group-custom">
                    <label for="username">Username <span style="color:red;">*</span></label>
                    <input type="text" name="username" id="username" value="{{ old('username', $selected->username) }}" required maxlength="50" placeholder="e.g. johndoe" {{ $selected->id ? 'readonly style=background-color:#f0f0f0;' : '' }}>
                    @if($selected->id)
                        <small style="color:#666; font-size:10px; display:block; margin-top:2px;">🔒 Username cannot be changed once created.</small>
                    @else
                        <div id="usernameSuggestions" style="margin-top:4px; display:flex; flex-wrap:wrap; gap:4px; align-items:center;"></div>
                        <div id="usernameStatus" style="font-size:11px; margin-top:2px; font-weight:600;"></div>
                    @endif
                </div>

                <!-- 3. Phone Number -->
                <div class="form-group-custom">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $selected->phone_number) }}" maxlength="20" placeholder="e.g. +91 9876543210">
                </div>

                <!-- 4. Email Address -->
                <div class="form-group-custom">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $selected->email) }}" maxlength="100" placeholder="e.g. user@omkaarlogistics.com">
                </div>

                <!-- 5. Password -->
                <div class="form-group-custom" style="grid-column: span 2;">
                    <label for="password">Password {{ $selected->id ? '(Leave blank to keep unchanged)' : '*' }}</label>
                    <input type="password" name="password" id="password" {{ $selected->id ? '' : 'required' }} minlength="4" placeholder="••••••••">
                </div>
            </div>

            <div class="form-group-custom" style="flex-direction:row; align-items:center; gap:8px; margin-bottom:15px;">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $selected->id ? $selected->is_active : true) ? 'checked' : '' }} {{ $isAdminUser ? 'disabled' : '' }} style="width:16px; height:16px;">
                <label for="is_active" style="margin:0; cursor:pointer;">Account Active Status</label>
            </div>

            {{-- Multi-Role Assignment --}}
            <div style="background:#f8f9fa; border:1px solid #ddd; padding:12px; border-radius:4px; margin-bottom:15px;">
                <label style="font-weight:bold; color:#003087; display:block; margin-bottom:6px;">Assign Roles (Multiple Roles allowed):</label>
                <div style="display:flex; flex-wrap:wrap; gap:12px;">
                    @foreach($roles as $r)
                        @php
                            $isChecked = in_array($r->id, old('roles', $selectedRoleIds));
                        @endphp
                        <label style="display:flex; align-items:center; gap:4px; cursor:pointer; background:#fff; padding:4px 8px; border:1px solid #ccc; border-radius:3px;">
                            <input type="checkbox" name="roles[]" value="{{ $r->id }}" {{ $isChecked ? 'checked' : '' }}>
                            <span style="font-weight:600;">{{ $r->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Permitted Companies Assignment --}}
            <div style="background:#eef4ff; border:1px solid #90caf9; padding:12px; border-radius:4px; margin-bottom:15px;">
                <label style="font-weight:bold; color:#003087; display:block; margin-bottom:6px;">🏢 Permitted Companies (Login Access Control):</label>
                <small style="color:#555; display:block; margin-bottom:8px;">
                    Users can ONLY log in to companies checked below. (System ADMIN has full access to all companies).
                </small>
                <div style="display:flex; flex-wrap:wrap; gap:12px;">
                    @foreach($companies as $comp)
                        @php
                            $isCompChecked = in_array($comp->id, old('companies', $selectedCompanyIds));
                        @endphp
                        <label style="display:flex; align-items:center; gap:4px; cursor:pointer; background:#fff; padding:4px 8px; border:1px solid #90caf9; border-radius:3px;">
                            <input type="checkbox" name="companies[]" value="{{ $comp->id }}" {{ $isCompChecked ? 'checked' : '' }}>
                            <span style="font-weight:600; color:#0d47a1;">{{ $comp->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 3-State Direct Permission Overrides --}}
            <div style="border:1px solid #c0392b; border-radius:4px; overflow:hidden; margin-top:15px;">
                <div style="background:#c0392b; color:#fff; padding:6px 12px; font-weight:bold; display:flex; justify-space-between; align-items:center;">
                    <span>⚡ Direct Permission Overrides (Allow / Restrict specific features per user)</span>
                </div>
                <div style="padding:10px; background:#fff;">
                    <small style="color:#666; display:block; margin-bottom:8px;">
                        💡 <strong>⚪ Inherit Roles</strong>: Follows permissions assigned to user's roles.<br>
                        🟢 <strong>Explicit Allow</strong>: Grants access even if role doesn't have it.<br>
                        🔴 <strong>Explicit Restrict</strong>: <strong>Blocks/Restricts</strong> access even if user's role has it!
                    </small>

                    @foreach($permissions as $module => $modulePerms)
                        <div style="margin-bottom:10px;">
                            <div style="font-weight:bold; color:#003087; background:#e8f0fe; padding:4px 8px; border-radius:3px; margin-bottom:4px;">
                                📂 {{ $module }} Module
                            </div>
                            <table class="override-table">
                                <thead>
                                    <tr>
                                        <th width="45%">Feature / Permission</th>
                                        <th width="55%">Access Override Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modulePerms as $p)
                                        @php
                                            $overrideState = isset($userOverrides[$p->id]) ? $userOverrides[$p->id] : 'inherit';
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $p->display_name }}</strong> <code style="color:#666; font-size:10px;">({{ $p->name }})</code></td>
                                            <td>
                                                <div class="override-radio-group">
                                                    <label class="radio-label radio-inherit">
                                                        <input type="radio" name="user_permissions[{{ $p->id }}]" value="inherit" {{ $overrideState === 'inherit' ? 'checked' : '' }}>
                                                        ⚪ Inherit Role
                                                    </label>
                                                    <label class="radio-label radio-allow">
                                                        <input type="radio" name="user_permissions[{{ $p->id }}]" value="allow" {{ $overrideState === 'allow' ? 'checked' : '' }}>
                                                        🟢 Allow
                                                    </label>
                                                    <label class="radio-label radio-deny">
                                                        <input type="radio" name="user_permissions[{{ $p->id }}]" value="deny" {{ $overrideState === 'deny' ? 'checked' : '' }}>
                                                        🔴 Restrict
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>

            <div style="margin-top:20px; display:flex; gap:10px;">
                <button type="submit" class="btn-action">
                    {{ $selected->id ? '💾 Update User' : '➕ Save User' }}
                </button>
                @if($selected->id)
                    <a href="{{ route('system.user') }}" class="btn-action" style="text-decoration:none; background:#757575; border-color:#616161;">Cancel</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterUsers(q) {
        q = q.toLowerCase().trim();
        document.querySelectorAll('.user-row-item').forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(q) ? 'flex' : 'none';
        });
    }

    const existingUsers = @json($users->map(fn($u) => ['id' => $u->id, 'username' => strtolower($u->username)]));
    const currentUserId = {{ $selected->id ? $selected->id : 'null' }};

    function getExistingUsernames() {
        return existingUsers
            .filter(u => currentUserId === null || u.id !== currentUserId)
            .map(u => u.username);
    }

    function generateUsernameSuggestions(fullName) {
        if (!fullName || fullName.trim().length < 2) {
            return [];
        }

        const clean = fullName.trim().toLowerCase().replace(/[^a-z0-9\s]/g, '');
        const parts = clean.split(/\s+/).filter(Boolean);

        if (parts.length === 0) return [];

        const firstName = parts[0];
        const lastName = parts.length > 1 ? parts[parts.length - 1] : '';

        const candidates = [];

        if (parts.length === 1) {
            candidates.push(firstName);
            candidates.push(firstName + '1');
            candidates.push(firstName + '123');
            candidates.push(firstName + '2026');
        } else {
            candidates.push(firstName + lastName);
            candidates.push(firstName);
            candidates.push(firstName[0] + lastName);
            candidates.push(firstName + '.' + lastName[0]);
            candidates.push(firstName + '_' + lastName);
            candidates.push(firstName + '1');
        }

        const takenUsernames = getExistingUsernames();
        const uniqueCandidates = [];

        candidates.forEach(c => {
            if (!takenUsernames.includes(c) && !uniqueCandidates.includes(c) && c.length >= 3) {
                uniqueCandidates.push(c);
            }
        });

        return uniqueCandidates.slice(0, 5);
    }

    function updateUsernameSuggestions() {
        if (currentUserId !== null) return;
        const fullNameInput = document.getElementById('name');
        const suggestionsBox = document.getElementById('usernameSuggestions');
        if (!fullNameInput || !suggestionsBox) return;

        const suggestions = generateUsernameSuggestions(fullNameInput.value);

        if (suggestions.length === 0) {
            suggestionsBox.innerHTML = '';
            return;
        }

        let html = '<span style="font-size:10px; color:#555; font-weight:600;">Suggestions:</span>';
        suggestions.forEach(sug => {
            html += `<button type="button" onclick="applyUsernameSuggestion('${sug}')" style="background:#e3f2fd; color:#0d47a1; border:1px solid #90caf9; padding:2px 6px; font-size:10px; border-radius:3px; font-weight:600; cursor:pointer; transition:all 0.2s;">${sug}</button>`;
        });

        suggestionsBox.innerHTML = html;
    }

    function applyUsernameSuggestion(val) {
        if (currentUserId !== null) return;
        const usernameInput = document.getElementById('username');
        if (usernameInput) {
            usernameInput.value = val;
            usernameInput.dataset.autoFilled = 'false';
            validateUsernameUniqueness();
        }
    }

    function validateUsernameUniqueness() {
        if (currentUserId !== null) return;
        const usernameInput = document.getElementById('username');
        const statusBox = document.getElementById('usernameStatus');
        if (!usernameInput || !statusBox) return;

        const val = usernameInput.value.trim().toLowerCase();
        if (!val) {
            statusBox.innerHTML = '';
            return;
        }

        const takenUsernames = getExistingUsernames();
        if (takenUsernames.includes(val)) {
            statusBox.innerHTML = '<span style="color:#d32f2f;">⚠️ Username already taken!</span>';
        } else {
            statusBox.innerHTML = '<span style="color:#2e7d32;">✓ Username available</span>';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const usernameInput = document.getElementById('username');

        if (nameInput && currentUserId === null) {
            nameInput.addEventListener('input', function() {
                updateUsernameSuggestions();
                if (usernameInput && (!usernameInput.value.trim() || usernameInput.dataset.autoFilled === 'true')) {
                    const suggestions = generateUsernameSuggestions(this.value);
                    if (suggestions.length > 0) {
                        usernameInput.value = suggestions[0];
                        usernameInput.dataset.autoFilled = 'true';
                        validateUsernameUniqueness();
                    }
                }
            });
        }

        if (usernameInput && currentUserId === null) {
            usernameInput.addEventListener('input', function() {
                this.dataset.autoFilled = 'false';
                validateUsernameUniqueness();
            });
            validateUsernameUniqueness();
        }

        if (currentUserId === null) {
            updateUsernameSuggestions();
        }
    });
</script>
@endsection
