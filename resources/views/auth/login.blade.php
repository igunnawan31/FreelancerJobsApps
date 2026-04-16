@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center mb-6">
                Login
            </h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm mb-1">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                        required
                    >
                </div>
                <div class="mb-6">
                    <label class="block text-sm mb-1">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                        required
                    >
                </div>
                @if($errors->any())
                    <div class="mb-4 text-red-500 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif
                <button 
                    type="submit"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
@endsection