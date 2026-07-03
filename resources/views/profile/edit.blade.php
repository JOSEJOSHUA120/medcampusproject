@extends('layouts.app')

@section('title', 'Profile - MEDCAMPUS')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
            @include('profile.partials.update-password-form')
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
