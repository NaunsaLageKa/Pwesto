@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-800">
    @include('partials.dashboard-navbar', ['active' => 'about'])

    @include('partials.about-content')
</div>

<style>
.nav-link {
    @apply text-gray-700 hover:text-teal-600 font-medium transition-colors;
}

.nav-link.active {
    @apply text-teal-600 border-b-2 border-teal-600 pb-1;
}

.admin-button {
    @apply bg-teal-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-teal-700 transition-colors;
}
</style>
@endsection
