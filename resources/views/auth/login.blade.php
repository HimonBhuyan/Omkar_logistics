<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Omkaar Logistics</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #090e17 0%, #162032 50%, #060a10 100%);
            position: relative;
            overflow: hidden;
        }

        /* Ambient light effects in background */
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(233, 69, 96, 0.12) 0%, rgba(233, 69, 96, 0) 70%);
            top: -100px;
            left: -100px;
            z-index: 1;
        }

        body::after {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(15, 52, 96, 0.25) 0%, rgba(15, 52, 96, 0) 70%);
            bottom: -150px;
            right: -100px;
            z-index: 1;
        }

        .login-window {
            position: relative;
            z-index: 2;
            width: 750px;
            max-width: 90%;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-radius: 16px;
            box-shadow: 0 25px 65px rgba(0, 0, 0, 0.55), inset 0 0 0 1px rgba(255, 255, 255, 0.12);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Legacy-inspired Red Header */
        .window-header {
            background: #e3001b; /* Vivid red */
            color: #fff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            font-weight: 500;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .window-controls {
            display: flex;
            gap: 8px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        .dot-red { background: #ff5f56; }
        .dot-yellow { background: #ffbd2e; }
        .dot-green { background: #27c93f; }

        .window-body {
            display: flex;
            padding: 30px;
            gap: 30px;
        }

        /* Left Side Brand Logo Container */
        .brand-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-right: 1px solid rgba(255, 255, 255, 0.12);
            padding-right: 25px;
        }

        .brand-logo-img {
            max-width: 180px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
            background: #fff;
            padding: 8px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .brand-logo-img:hover {
            transform: scale(1.05);
        }

        .brand-title {
            font-size: 18px;
            font-weight: 600;
            color: #fff; /* White title */
            text-align: center;
            margin-top: 5px;
            letter-spacing: 0.5px;
        }

        .brand-subtitle {
            font-size: 12px;
            color: #b0b8c6; /* Lighter subtitle */
            text-align: center;
            margin-top: 2px;
        }

        /* Right Side Form fields */
        .form-section {
            flex: 1.4;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-group {
            margin-bottom: 18px;
            display: grid;
            grid-template-columns: 100px 1fr;
            align-items: center;
            gap: 15px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #e2e8f0; /* White/grey label */
            text-align: left;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            font-size: 13px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.08); /* Glassmorphic inputs */
            color: #fff;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-control option {
            background-color: #1a2332; /* Match dropdown list background */
            color: #fff;
        }

        .form-control:focus {
            border-color: #ff5f56;
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 10px rgba(255, 95, 86, 0.25);
        }

        select.form-control {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%23fff' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 8px 10px;
            padding-right: 30px;
        }

        /* Form submission button bar */
        .form-actions {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 40px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-submit {
            background: #27c93f; /* Green */
            color: #fff;
        }

        .btn-submit:hover {
            background: #20a833;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(39, 201, 63, 0.3);
        }

        .btn-cancel {
            background: #ff5f56; /* Red */
            color: #fff;
        }

        .btn-cancel:hover {
            background: #e04b43;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(255, 95, 86, 0.3);
        }

        .btn-action svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: currentColor;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .error-message {
            grid-column: 2;
            color: #ff5f56;
            font-size: 11px;
            margin-top: 2px;
        }
        
        .alert-error {
            background: #ffebe9;
            color: #ff5f56;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 95, 86, 0.2);
        }

        /* Responsive styling */
        @media (max-width: 600px) {
            .window-body {
                flex-direction: column;
            }
            .brand-section {
                border-right: none;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                padding-right: 0;
                padding-bottom: 20px;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="login-window">
        <!-- Windows titlebar style -->
        <div class="window-header">
            <span>Login</span>
            <div class="window-controls">
                <span class="dot dot-green"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-red"></span>
            </div>
        </div>

        <div class="window-body">
            <!-- Left Side Logo/Branding -->
            <div class="brand-section">
                <img src="{{ asset('assets/logo.jpg') }}" alt="Omkaar Logistics Logo" class="brand-logo-img">
                <div class="brand-subtitle" style="font-weight: 600; color: #475569;">Billing & ERP System</div>
            </div>

            <!-- Right Side Login Form -->
            <form action="{{ route('login.post') }}" method="POST" class="form-section">
                @csrf
                
                @if ($errors->any())
                    <div class="alert-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Company select -->
                <div class="form-group">
                    <label for="company">Company</label>
                    <select name="company" id="company" class="form-control" required>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Financial Year select -->
                <div class="form-group">
                    <label for="financial_year">Fin. Year</label>
                    <select name="financial_year" id="financial_year" class="form-control" required>
                        @foreach ($financialYears as $year)
                            <option value="{{ $year->year_string }}" {{ old('financial_year') == $year->year_string ? 'selected' : ($year->is_active ? 'selected' : '') }}>
                                {{ $year->year_string }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- User Name select/input -->
                <div class="form-group">
                    <label for="username">User Name</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="e.g. admin" value="{{ old('username', 'admin') }}" required>
                </div>

                <!-- Password input -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>

                <!-- Form Action Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn-action btn-submit" title="Login">
                        <svg viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </button>
                    <button type="reset" class="btn-action btn-cancel" title="Clear">
                        <svg viewBox="0 0 24 24">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
