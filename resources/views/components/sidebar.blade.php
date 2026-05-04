<div class="h-full w-64 bg-white rounded-2xl shadow-lg border border-gray-100 flex flex-col">
    <div class="p-6">
        <h2 class="text-xl font-bold mb-6">Menu</h2>
        <ul class="space-y-4">
            @if(auth()->user()->role === \App\Enums\UserEnums\UserRole::ADMIN)
                <li><a href="/dashboard" class="hover:text-gray-300">Dashboard</a></li>
                <li><a href="/projects" class="hover:text-gray-300">Projects</a></li>

                <li><a href="/users" class="hover:text-gray-300">Management Users</a></li>
                <li><a href="/skills" class="hover:text-gray-300">Management Skills</a></li>
                <li><a href="/profiles" class="hover:text-gray-300">My Profile</a></li>
            @endif

            @if(auth()->user()->role === \App\Enums\UserEnums\UserRole::FREELANCER)
                <li><a href="/dashboard" class="hover:text-gray-300">Dashboard</a></li>
                <li><a href="/projects" class="hover:text-gray-300">Projects</a></li>
                <li><a href="/profiles" class="hover:text-gray-300">My Profile</a></li>
            @endif

            @if(auth()->user()->role === \App\Enums\UserEnums\UserRole::CLIENT)
                <li><a href="/dashboard" class="hover:text-gray-300">Dashboard</a></li>
                <li><a href="/projects" class="hover:text-gray-300">My Commission</a></li>
                <li><a href="/profiles" class="hover:text-gray-300">My Profile</a></li>
            @endif
        </ul>
    </div>
</aside>