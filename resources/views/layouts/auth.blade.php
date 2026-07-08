<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Life Church')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .auth-bg {
            background: #020617;
            min-height: 100vh;
            color: white;
            position: relative;
        }

        .auth-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #020617 100%);
            z-index: -1;
            overflow: hidden;
        }

        .auth-overlay::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -20%;
            width: 80%;
            height: 80%;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.1) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }

        .auth-overlay::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -20%;
            width: 80%;
            height: 80%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite reverse;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .floating-shapes {
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 20s infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f97316, #fb923c);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            top: 60%;
            right: 15%;
            animation-delay: 3s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #f97316, #fb923c);
            bottom: 20%;
            left: 20%;
            animation-delay: 6s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-30px) rotate(180deg);
            }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="auth-bg">
    <div class="auth-overlay"></div>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="relative z-10 w-full">
        @yield('content')
    </div>
</body>

</html>