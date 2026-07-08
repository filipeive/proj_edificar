@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.showSuccess(@json(session('success')));
        });
    </script>
@endif
@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.showError(@json(session('error')));
        });
    </script>
@endif
@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.showWarning(@json(session('warning')));
        });
    </script>
@endif
@if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.showInfo(@json(session('info')));
        });
    </script>
@endif
