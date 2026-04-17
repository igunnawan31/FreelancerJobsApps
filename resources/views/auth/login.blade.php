@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-200 via-blue-200 to-yellow-100">

        <!-- Card -->
        <div class="w-full max-w-md p-8 bg-white/40 backdrop-blur-xl rounded-2xl shadow-xl text-center">

            <!-- Badge -->
            <span class="px-4 py-1 text-sm bg-color-# rounded-full">
                Mizumates Login
            </span>

            <!-- Title -->
            <h1 class="text-2xl font-semibold mt-4 mb-6">
                Welcome Mizumates!
            </h1>

            <!-- Form -->
            <form class="space-y-4 text-left" method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Email -->
                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="w-full mt-1 px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-400 outline-none"
                        placeholder="name@gmail.com"
                        required>
                </div>

                <!-- Password -->
                <div>
                    <label class="text-sm font-medium">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="w-full mt-1 px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-400 outline-none"
                        placeholder="••••••••"
                        required>
                </div>

                <!-- Remember & Forgot -->
                <!-- <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="rounded">
                        Remember me
                    </label>
                    <a href="#" class="hover:underline">Forgot password?</a>
                </div> -->

                @if($errors->any())
                    <div class="mb-4 text-red-500 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Button -->
                <button
                    type="submit"
                    class="w-full bg-[#051233] text-white py-2 rounded-xl mt-2 hover:opacity-90">
                    Login
                </button>

                <!-- OR -->
                <!-- <p class="text-center text-sm text-gray-500">or</p> -->

                <!-- Google
                <button type="button"
                    class="w-full flex items-center justify-center gap-2 border py-2 rounded-xl bg-white hover:bg-gray-50">
                    Create an Account
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5">
                    Continue with Google
                </button> -->

            </form>
        </div>
    </div>
@endsection