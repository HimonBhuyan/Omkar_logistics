@extends('layouts.app')

@section('title', 'Measurement Unit Master - Omkaar Logistics')

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
        position: sticky;
        top: 135px;
        z-index: 10;
    }

    .master-list-search {
        padding: 6px 10px;
        background: #f4f6f9;
        border-bottom: 1px solid #ddd;
        position: sticky;
        top: 173px;
        z-index: 9;
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
        padding: 20px 30px;
        max-width: 600px;
    }

    .form-group-custom {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
    }

    .form-group-custom label {
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }

    .form-group-custom input[type="text"],
    .form-group-custom select {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 12px;
        width: 100%;
        box-sizing: border-box;
    }

    .form-group-custom input:focus,
    .form-group-custom select:focus {
        border-color: #003087;
        outline: none;
        box-shadow: 0 0 3px rgba(0, 48, 135, 0.3);
    }

    .btn-action {
        background: #003087;
        color: #fff;
        border: 1px solid #002060;
        padding: 6px 16px;
        font-weight: bold;
        font-size: 12px;
        cursor: pointer;
        border-radius: 3px;
        transition: background 0.2s;
    }

    .btn-action:hover {
        background: #002060;
    }

    .btn-delete {
        background: none;
        border: none;
        color: #d32f2f;
        cursor: pointer;
        font-size: 13px;
        padding: 2px 4px;
    }

    .badge-type {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 3px;
        text-transform: uppercase;
    }
    .badge-weight {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .badge-fixed {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-system {
        background: #f3e8ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
        font-size: 9px;
    }
</style>
@endsection

@section('content')
<div class="master-wrapper">
    {{-- Left List Panel --}}
    <div class="master-list-panel">
        <form id="bulkDeleteForm" method="POST" action="{{ route('master.measurement-unit.bulk_destroy') }}" data-confirm="Delete selected custom units? System units (KG, FIXED) will be preserved." style="display:flex; flex-direction:column; flex:1; min-height:100%;">
            @csrf @method('DELETE')
            <div class="master-list-header" style="display:flex; justify-content:space-between; align-items:center;">
                <span>Measurement Units ({{ count($units) }})</span>
                <div style="display:flex; gap:6px; align-items:center;">
                    <input type="checkbox" id="selectAll" title="Select All Custom Units" style="cursor:pointer; width:14px; height:14px; margin:0;">
                    <button type="submit" class="btn-delete" style="color:#fff; padding:2px 4px; background:#d32f2f; border-radius:2px; font-size:10px;" title="Delete Selected Custom Units">🗑 Bulk</button>
                </div>
            </div>
            <div class="master-list-search">
                <input type="text" id="listSearch" placeholder="🔍 Filter Units..." oninput="filterRows(this.value)">
            </div>
            <div class="master-list-items" id="masterItemsContainer">
                @foreach($units as $u)
                    @php
                        $isSys = $u->is_system || in_array(strtoupper($u->unit_code), ['KG', 'FIXED']);
                        $isSelected = ($selected->id == $u->id);
                    @endphp
                    <div class="master-item-link master-row-item {{ $isSelected ? 'active' : '' }}" style="gap:10px;">
                        @if(!$isSys)
                            <input type="checkbox" name="ids[]" value="{{ $u->id }}" class="row-checkbox" style="width:14px; height:14px; margin:0;" onclick="event.stopPropagation();">
                        @else
                            <span style="width:14px; display:inline-block;"></span>
                        @endif

                        <a href="{{ route('master.measurement-unit.load', $u->id) }}" style="text-decoration:none; color:inherit; flex:1; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <span class="item-name" style="font-weight:700;">{{ $u->unit_code }}</span>
                                <small style="color:#666; font-size:10px; margin-left:4px;">({{ $u->unit_name }})</small>
                            </div>
                            <div style="display:flex; gap:4px; align-items:center;">
                                <span class="badge-type {{ $u->unit_type === 'weight' ? 'badge-weight' : 'badge-fixed' }}">
                                    {{ $u->unit_type }}
                                </span>
                                @if($isSys)
                                    <span class="badge-type badge-system">🔒 System</span>
                                @endif
                            </div>
                        </a>

                        @if(!$isSys)
                            <form method="POST" action="{{ route('master.measurement-unit.destroy', $u->id) }}" data-confirm="Delete unit '{{ $u->unit_code }}'?" onclick="event.stopPropagation();" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete" title="Delete Unit">❌</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </form>
    </div>

    {{-- Right Form Panel --}}
    <div class="master-form-panel">
        <div class="master-title-bar" style="display:flex; justify-content:space-between; align-items:center;">
            <span>Measurement Unit Master {{ $selected->id ? '- Edit Unit #' . $selected->id : '- Create Unit' }}</span>
            @if($selected->id)
                <a href="{{ route('master.measurement-unit') }}" class="btn-action" style="text-decoration:none; background:#e8f5e9; color:#1b5e20; border-color:#81c784; font-size:11px; padding:3px 10px;">➕ New Unit</a>
            @endif
        </div>

        <form method="POST" action="{{ route('master.measurement-unit.store') }}" class="master-form-body">
            @csrf
            <input type="hidden" name="id" value="{{ $selected->id }}">

            @if($errors->any())
                <div style="background:#ffebee; color:#c62828; border:1px solid #ef9a9a; padding:8px 12px; margin-bottom:15px; border-radius:4px; font-size:12px;">
                    @foreach($errors->all() as $error)
                        <div>⚠️ {{ $error }}</div>
                    @endforeach
                </div>
            @endif
            @if(session('success'))
                <div style="background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; padding:8px 12px; margin-bottom:15px; border-radius:4px; font-size:12px;">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="background:#ffebee; color:#c62828; border:1px solid #ef9a9a; padding:8px 12px; margin-bottom:15px; border-radius:4px; font-size:12px;">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            @php
                $isSystemUnit = $selected->id && ($selected->is_system || in_array(strtoupper($selected->unit_code), ['KG', 'FIXED']));
            @endphp

            @if($isSystemUnit)
                <div style="background:#f3e8ff; color:#6b21a8; border:1px solid #d8b4fe; padding:8px 12px; margin-bottom:15px; border-radius:4px; font-size:11px; font-weight:600;">
                    🔒 <strong>Protected System Unit:</strong> The Unit Code for standard system units ({{ $selected->unit_code }}) cannot be modified or deleted.
                </div>
            @endif

            <div class="form-group-custom">
                <label for="unit_code">Unit Code <span style="color:red;">*</span></label>
                <input type="text" name="unit_code" id="unit_code" value="{{ old('unit_code', $selected->unit_code) }}" required maxlength="50" placeholder="e.g. KG, TON, BOX, PCS" {{ $isSystemUnit ? 'readonly style=background-color:#f0f0f0;' : '' }}>
            </div>

            <div class="form-group-custom">
                <label for="unit_name">Unit Name <span style="color:red;">*</span></label>
                <input type="text" name="unit_name" id="unit_name" value="{{ old('unit_name', $selected->unit_name) }}" required maxlength="100" placeholder="e.g. Kilogram, Metric Ton, Box / Carton">
            </div>

            <div class="form-group-custom">
                <label for="unit_type">Unit Behavior Type <span style="color:red;">*</span></label>
                <select name="unit_type" id="unit_type" required>
                    <option value="weight" {{ old('unit_type', $selected->unit_type) == 'weight' ? 'selected' : '' }}>
                        Weight-based (e.g. KG, Ton) - Hides extra Weight column
                    </option>
                    <option value="fixed" {{ old('unit_type', $selected->unit_type) == 'fixed' ? 'selected' : '' }}>
                        Fixed / Package-based (e.g. Fixed, Box, Pcs) - Opens extra Weight column
                    </option>
                </select>
            <div class="form-group-custom">
                <label for="package_label">Package Column Label</label>
                <input type="text" name="package_label" id="package_label" value="{{ old('package_label', $selected->package_label ?: 'NoOfPkgs') }}" maxlength="50" placeholder="e.g. NoOfBoxes, NoOfCases, NoOfPcs, NoOfPkgs">
                <small style="color:#666; margin-top:4px; font-size:11px;">
                    💡 Changes the first column header label dynamically on C.N. Entry form when this unit is selected (e.g. <code>NoOfBoxes</code> for BOX, <code>NoOfCases</code> for CASE, <code>NoOfPcs</code> for PCS).
                </small>
            </div>

            <div class="form-group-custom" style="flex-direction:row; align-items:center; gap:8px;">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $selected->id ? $selected->is_active : true) ? 'checked' : '' }} style="width:16px; height:16px;">
                <label for="is_active" style="margin:0; cursor:pointer;">Active Status</label>
            </div>

            <div style="margin-top: 25px; display:flex; gap:10px;">
                <button type="submit" class="btn-action">
                    {{ $selected->id ? '💾 Update Unit' : '➕ Save Unit' }}
                </button>
                @if($selected->id)
                    <a href="{{ route('master.measurement-unit') }}" class="btn-action" style="text-decoration:none; background:#757575; border-color:#616161;">Cancel</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterRows(q) {
        q = q.toLowerCase().trim();
        document.querySelectorAll('.master-row-item').forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(q) ? 'flex' : 'none';
        });
    }

    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
        });
    }
</script>
@endsection
