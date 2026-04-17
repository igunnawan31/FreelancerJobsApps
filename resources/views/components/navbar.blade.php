<nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <h1 class="font-bold text-lg">Mizumates</h1>
    <div class="flex items-center gap-4">
        <span class="text-gray-600">
            {{ auth()->user()->name }}
        </span>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="text-red-500 hover:underline">Logout</button>
        </form>
    </div>
</nav>