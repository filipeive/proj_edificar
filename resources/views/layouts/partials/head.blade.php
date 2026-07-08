<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Life Church')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <!-- PWA & Mobile Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#000000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Life Church">

    <!-- Lazy load Chart.js queue placeholder -->
    <script>
        (function() {
            window.ChartQueue = [];
            window.ChartDefaults = {
                font: {
                    family: '',
                    weight: ''
                },
                color: ''
            };
            window.Chart = function(ctx, config) {
                window.ChartQueue.push({ctx: ctx, config: config});
                if (window.loadChartJS) {
                    window.loadChartJS();
                }
            };
            window.Chart.defaults = window.ChartDefaults;
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Configurações de Rotas e Globais do Laravel para o JS Externo -->
    <script>
        window.AppConfig = {
            routes: {
                search: "{{ route('api.search') }}",
                notificationsIndex: "{{ route('notifications.api.index') }}",
                notificationsRead: "{{ route('notifications.read') }}",
                notificationsUnreadCount: "{{ route('notifications.unread-count') }}",
                usersShowTemplate: "{{ route('users.show', ['user' => '__ID__']) }}",
                contributionsShowTemplate: "{{ route('contributions.show', ['contribution' => '__ID__']) }}"
            }
        };
    </script>
</head>
