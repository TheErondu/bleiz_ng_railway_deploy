@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
         @include('components.logo')
        <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Verify your email</h2>
        <p class="mt-2 text-sm text-gray-600">
            Thanks for signing up! Before getting started, please verify your email address.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-md rounded-lg sm:px-10">
            @if (session('resent'))
                <div class="mb-4 rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            ✅
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                A new verification link has been sent to your email.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <p class="text-sm text-gray-700">
                Didn’t get the email? No stress. You can request a new one below.
            </p>

            <form class="mt-6 space-y-4" method="POST" action="{{ route('verification.send') }}">
                @csrf
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                        Request Another Verification Email
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500">
                    Return to login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
