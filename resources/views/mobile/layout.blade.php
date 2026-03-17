<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'GrowTrack Worker')</title>

    <!-- Use the correct built asset filenames -->
    <link rel="stylesheet" href="{{ asset('build/assets/app-t63l0VUN.css') }}">
    <script src="{{ asset('build/assets/app-BlGawQ9s.js') }}" defer></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #48c774 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .app-container {
            width: 100%;
            max-width: 380px;
            height: 92vh;
            max-height: 750px;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            position: relative;
            margin-top: -50px !important;
        }

        .status-bar {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 12px 16px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .status-title {
            font-weight: 600;
            font-size: 16px;
        }

        .online-badge,
        .offline-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .online-badge {
            background: #27ae60;
        }

        .offline-badge {
            background: #f39c12;
        }

        .content {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: #f9fafb;
            -webkit-overflow-scrolling: touch;
        }

        /* Scrollbar styling */
        .content::-webkit-scrollbar {
            width: 4px;
        }

        .content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .content::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .nav-bar {
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex;
            padding: 8px 0;
            flex-shrink: 0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.03);
        }

        .nav-item {
            flex: 1;
            text-align: center;
            color: #6b7280;
            font-size: 11px;
            cursor: pointer;
            padding: 4px 0;
        }

        .nav-item.active {
            color: #10b981;
        }

        .nav-item span {
            display: block;
            font-size: 20px;
            margin-bottom: 2px;
        }

        .header {
            margin-bottom: 16px;
        }

        .header h1 {
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .header p {
            color: #6b7280;
            font-size: 13px;
        }

        .menu-item {
            background: white;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            border: 1px solid #f3f4f6;
            cursor: pointer;
            transition: all 0.2s;
        }

        .menu-item:active {
            background: #f9fafb;
            transform: scale(0.98);
        }

        .menu-icon {
            width: 44px;
            height: 44px;
            background: #d1fae5;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-right: 12px;
        }

        .menu-content {
            flex: 1;
        }

        .menu-title {
            font-weight: 500;
            color: #1f2937;
            font-size: 15px;
        }

        .menu-subtitle {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            color: #4b5563;
            font-size: 14px;
            margin-bottom: 12px;
            cursor: pointer;
            padding: 6px 0;
        }

        .btn-back span {
            font-size: 20px;
            margin-right: 4px;
        }

        .btn-primary {
            background: #10b981;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            width: 100%;
            cursor: pointer;
            margin-top: 8px;
        }

        .btn-primary:active {
            background: #059669;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #4b5563;
            margin-bottom: 6px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            background: white;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: #10b981;
            ring: 2px solid #d1fae5;
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }

        .photo-upload {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            margin-bottom: 8px;
            color: #6b7280;
        }

        .photo-preview {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 8px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .radio-group {
            display: flex;
            gap: 15px;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        .alert {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .menu-arrow {
            font-size: 20px;
            color: #a0aec0;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
        }

        .empty-icon {
            font-size: 48px;
            display: block;
            margin-bottom: 10px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            color: #4b5563;
            font-size: 14px;
            margin-bottom: 16px;
            cursor: pointer;
            padding: 8px 0;
        }

        .btn-back span {
            font-size: 20px;
            margin-right: 4px;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <div class="status-bar">
            <span class="status-title">🌿 GrowTrack</span>
            @if(isset($offline) && $offline)
                <span class="offline-badge">○ Offline</span>
            @else
                <span class="online-badge">● Online</span>
            @endif
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>

        @yield('nav')
    </div>
</body>

</html>