{{-- resources/views/errors/419.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="refresh" content="0;url={{ route('login') }}">
</head>
<body>
    <script>
        window.location.href = "{{ route('login') }}";
    </script>
</body>
</html>
