<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
@include('components.css')
</head>
<body>
{{-- side bar  --}}
@include('components.sidebar')


@yield('content')

@include('components.script')
</body>
</html>
