<aside class="w-64 bg-[#D8F1FF] min-h-screen p-6">
    <h2 class="text-xl font-bold mb-6">Menu</h2>
    <div class="p-6 w-full bg-white min-h-screen rounded-lg">
        <ul class="space-y-4">
            @if(auth()->user()->role === \App\Enums\UserEnums\UserRole::ADMIN)
                <li><a href="/dashboard" class="hover:text-gray-300">Dashboard</a></li>
                <li><a href="/project" class="hover:text-gray-300">Projects</a></li>
                <li><a href="#" class="hover:text-gray-300">Freelancers</a></li>
                <li><a href="#" class="hover:text-gray-300">Payments</a></li>
                <li><a href="#" class="hover:text-gray-300">Profile</a></li>
            @endif

            @if(auth()->user()->role === \App\Enums\UserEnums\UserRole::FREELANCER)
                <li><a href="/dashboard" class="hover:text-gray-300">Dashboard</a></li>
                <li><a href="/project" class="hover:text-gray-300">Projects</a></li>
                <li><a href="#" class="hover:text-gray-300">Payments</a></li>
                <li><a href="#" class="hover:text-gray-300">Profile</a></li>
            @endif

            @if(auth()->user()->role === \App\Enums\UserEnums\UserRole::CLIENT)
                <li><a href="/dashboard" class="hover:text-gray-300">My Commission</a></li>
                <li><a href="#" class="hover:text-gray-300">Profile</a></li>
            @endif
        </ul>
    </div>
</aside>