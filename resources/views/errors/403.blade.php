@extends('layouts.app')

@section('title', '403 Access Denied - Omkaar Logistics')

@section('content')
<div style="max-width: 600px; margin: 60px auto; background: #fff; border: 2px solid #c0392b; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15); font-family: 'Segoe UI', Tahoma, sans-serif;">
    <div style="background: #c0392b; color: #fff; padding: 12px 20px; font-weight: bold; font-size: 14px; display: flex; align-items: center; gap: 10px;">
        <span>🚫 Access Restricted</span>
    </div>
    <div style="padding: 30px; text-align: center;">
        <div style="font-size: 48px; margin-bottom: 10px;">🔒</div>
        <h2 style="color: #c0392b; font-size: 20px; margin-bottom: 10px;">403 - Permission Denied</h2>
        <p style="color: #555; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
            Your account does not have authorization to access this feature
            @if(isset($permissionKey))
                (<code>{{ $permissionKey }}</code>)
            @endif.
            <br>Please contact your Administrator or System Manager to request permission access.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <a href="{{ route('dashboard') }}" style="background: #003087; color: #fff; text-decoration: none; padding: 8px 18px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                🏠 Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
