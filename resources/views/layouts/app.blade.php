<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>

        @vite('resources/css/app.css')

        <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <style>
            body {
                font-family: 'Poppins';
            }
        </style>
    </head>
    <body class="h-screen overflow-hidden bg-gradient-to-br from-purple-200 via-blue-200 to-yellow-100 dark:bg-gray-900">
        @auth
            <div class="flex h-full w-full gap-4 box-border font-poppins"> 
                <aside class="w-64 shrink-0 h-full p-4">
                    @include('components.sidebar')
                </aside>

                <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto">
                    <div class="mb-4 shrink-0 pt-4 px-4">
                        @include('components.navbar')
                    </div>
                    <div class="px-4 pb-4">
                        <main class="flex-1 bg-white/50 backdrop-blur-md rounded-2xl shadow-sm border border-white">
                            @yield('content')
                        </main>
                    </div>
                </div>
            </div>
        @endauth
    </body>
</html>