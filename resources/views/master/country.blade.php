@extends('layouts.app')

@section('title', 'Country Master - Omkaar Logistics')

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
        width: 280px;
        min-width: 280px;
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
        padding: 4px 8px;
        font-size: 11px;
        border: 1px solid #bbb;
        border-radius: 3px;
        outline: none;
        box-sizing: border-box;
    }

    .master-list-search input:focus {
        border-color: #003087;
        background: #fffffc;
    }

    .master-list-items {
        flex: 1;
    }

    .master-item-link {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 10px;
        border-bottom: 1px solid #eee;
        color: #333;
        text-decoration: none;
        cursor: pointer;
    }

    .master-item-link:hover {
        background: #e3f2fd;
    }

    .master-form-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #dbdbdb;
        position: relative;
        min-height: 100%;
    }

    .master-title-bar {
        background: #8b0000;
        color: #fff;
        font-weight: bold;
        font-size: 13px;
        padding: 7px 10px;
        text-align: center;
        letter-spacing: 0.5px;
        position: sticky;
        top: 135px;
        z-index: 8;
    }

    .master-form-body {
        padding: 30px;
        flex: 1;
    }

    .f-row {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }

    .f-row label {
        width: 120px;
        text-align: right;
        margin-right: 12px;
        font-weight: bold;
        color: #333;
    }

    .f-row input, .f-row select {
        flex: 1;
        max-width: 320px;
        border: 1px solid #999;
        padding: 3px 6px;
        font-size: 12px;
        background: #fff;
        height: 24px;
        outline: none;
    }

    .f-row input:focus, .f-row select:focus {
        border-color: #003087;
        background: #fffff0;
    }

    .btn-delete {
        background: none;
        border: none;
        color: #d32f2f;
        font-weight: bold;
        cursor: pointer;
        font-size: 11px;
    }

    .btn-delete:hover {
        text-decoration: underline;
    }

    .master-action-bar {
        background: #dbdbdb;
        border-top: 2px solid #bbb;
        padding: 12px 24px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        position: sticky;
        bottom: 60px;
        z-index: 8;
    }

    .btn-action {
        height: 38px;
        min-width: 90px;
        border: 1px solid #777;
        background: linear-gradient(180deg, #fefefe, #dcdcdc);
        color: #222;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        padding: 0 16px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        background: linear-gradient(180deg, #f0f0f0, #c8c8c8);
        border-color: #555;
        box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    }

    .btn-save {
        background: linear-gradient(180deg, #66bb6a, #388e3c) !important;
        color: #fff !important;
        border-color: #2e7d32 !important;
    }

    .btn-save:hover {
        background: linear-gradient(180deg, #4caf50, #2e7d32) !important;
        box-shadow: 0 3px 8px rgba(76, 175, 80, 0.4);
    }
</style>
@endsection

@section('content')
<div class="master-wrapper">
    {{-- Left List --}}
    <div class="master-list-panel">
        <form id="bulkDeleteForm" method="POST" action="{{ route('master.country.bulk_destroy') }}" onsubmit="return confirm('Delete selected countries?')" style="display:flex; flex-direction:column; flex:1; min-height:100%;">
            @csrf @method('DELETE')
            <div class="master-list-header" style="display:flex; justify-content:space-between; align-items:center;">
                <span>Countries ({{ count($countries) }})</span>
                <div style="display:flex; gap:6px; align-items:center;">
                    <input type="checkbox" id="selectAll" title="Select All" style="cursor:pointer; width:14px; height:14px; margin:0;">
                    <button type="submit" class="btn-delete" style="color:#fff; padding:2px 4px; background:#d32f2f; border-radius:2px; font-size:10px;" title="Delete Selected">🗑 Bulk</button>
                </div>
            </div>
            <div class="master-list-search">
                <input type="text" id="listSearch" placeholder="🔍 Filter Countries..." oninput="filterRows(this.value)">
            </div>
            <div class="master-list-items" id="masterItemsContainer">
                @foreach($countries as $c)
                    <div class="master-item-link master-row-item" style="gap:10px;">
                        <input type="checkbox" name="ids[]" value="{{ $c->id }}" class="row-checkbox" style="width:14px; height:14px; margin:0;" onclick="event.stopPropagation();">
                        <a href="{{ route('master.country.load', $c->id) }}" style="text-decoration:none; color:inherit; flex:1;">
                            <span class="item-name">{{ $c->name }}</span>
                        </a>
                        <form method="POST" action="{{ route('master.country.destroy', $c->id) }}" onsubmit="return confirm('Delete this Country?'); event.stopPropagation();" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-delete">❌</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </form>
    </div>

    {{-- Right Form --}}
    <div class="master-form-panel">
        <div class="master-title-bar">Country Master</div>
        
        <form method="POST" action="{{ route('master.country.store') }}" class="master-form-body">
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
                    ✔ {{ session('success') }}
                </div>
            @endif

            <div class="f-row">
                <label>Country <span style="color:#d32f2f; font-weight:bold;">*</span></label>
                <input type="text" name="name" required placeholder="Enter Country Name" style="background:#ffffcc; text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()" value="{{ old('name', $selected->name) }}">
            </div>
            <div class="f-row">
                <label>Country Code</label>
                <input type="text" name="code" placeholder="Enter Country Code" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()" value="{{ old('code', $selected->code) }}">
            </div>

            <button type="submit" style="display:none;" id="submitBtn"></button>
        </form>

        <div class="master-action-bar">
            <a href="{{ route('master.country') }}" class="btn-action" style="text-decoration:none; background:#e8f5e9; border-color:#81c784;">➕ New</a>
            <button type="button" class="btn-action" onclick="window.history.back()">🔙</button>
            <button type="button" class="btn-action btn-save" onclick="document.getElementById('submitBtn').click()">💾 Save</button>
        </div>
    </div>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    function filterRows(q) {
        const term = (q || '').trim().toUpperCase();
        const rows = document.querySelectorAll('.master-row-item');
        rows.forEach(r => {
            const nameEl = r.querySelector('.item-name');
            const txt = (nameEl ? nameEl.textContent : r.textContent).toUpperCase();
            r.style.display = (!term || txt.includes(term)) ? 'flex' : 'none';
        });
    }
</script>
@endsection
