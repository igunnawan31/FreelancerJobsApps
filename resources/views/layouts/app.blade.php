<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>

        @vite('resources/css/app.css')

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    </head>
    <body class="bg-gradient-to-br from-purple-200 via-blue-200 to-yellow-100 dark:bg-gray-900">
        @auth
            <div class="flex">
                @include('components.sidebar')
                <div class="flex-1 flex flex-col">
                    @include('components.navbar')
                    <main class="p-6">
                        @yield('content')
                    </main>
                </div>
            </div>
        @endauth
    </body>
</html>