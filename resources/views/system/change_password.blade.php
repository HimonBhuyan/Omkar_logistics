@extends('layouts.app')

@section('title', 'Change Password - Omkaar Logistics')

@section('styles')
<style>
    .card-box {
        max-width: 550px;
        margin: 40px auto;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(15, 52, 96, 0.1);
        overflow: hidden;
    }

    .card-header-bar {
        background: var(--secondary-color, #8b0000);
        color: white;
        padding: 12px 20px;
        font-weight: 700;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body-content {
        padding: 30px;
        background: #f8fafc;
    }

    .form-group-custom {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 20px;
    }

    .form-group-custom label {
        font-size: 12px;
        font-weight: 700;
        color: #334155;
    }

    .form-group-custom input {
        padding: 8px 12px;
        font-size: 13px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: #ffffff;
        color: #1e293b;
        outline: none;
        transition: all 0.2s ease;
    }

    .form-group-custom input:focus {
        border-color: var(--primary-color, #0f3460);
        box-shadow: 0 0 6px rgba(15, 52, 96, 0.15);
    }

    .btn-submit-pw {
        background: var(--primary-color, #0f3460);
        color: white;
        border: none;
        padding: 10px 24px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(15, 52, 96, 0.2);
    }

    .btn-submit-pw:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .btn-cancel {
        background: #e2e8f0;
        color: #475569;
        text-decoration: none;
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .btn-cancel:hover {
        background: #cbd5e1;
        color: #1e293b;
    }
</style>
@endsection

@section('content')
<div class="card-box">
    <div class="card-header-bar">
        <span>🔒 Change Password</span>
    </div>

    <form method="POST" action="{{ route('system.change_password.update') }}" class="card-body-content">
        @csrf
        <input type="hidden" name="previous_url" value="{{ old('previous_url', $previousUrl ?? route('dashboard')) }}">

        <div class="form-group-custom">
            <label for="current_password">Current Password <span style="color:red">*</span></label>
            <input type="password" name="current_password" id="current_password" required placeholder="Enter current password">
        </div>

        <div class="form-group-custom">
            <label for="new_password">New Password <span style="color:red">*</span></label>
            <input type="password" name="new_password" id="new_password" required placeholder="Enter new password (min. 4 characters)">
        </div>

        <div class="form-group-custom">
            <label for="new_password_confirmation">Confirm New Password <span style="color:red">*</span></label>
            <input type="password" name="new_password_confirmation" id="new_password_confirmation" required placeholder="Re-enter new password">
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:25px;">
            <a href="{{ old('previous_url', $previousUrl ?? route('dashboard')) }}" class="btn-cancel">
                Cancel
            </a>
            <button type="submit" class="btn-submit-pw">
                💾 Update Password
            </button>
        </div>
    </form>
</div>
@endsection
