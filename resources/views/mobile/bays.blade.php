@extends('mobile.layout')

@section('title', 'Select Bay')

@section('content')
<div class="btn-back" onclick="window.history.back()">
        <span>←</span> Back
    </div>
    <div class="header">
        <h1>Select Bay</h1>
        <p>Choose specific location</p>
    </div>
    
    @forelse($bays as $bay)
        <div class="menu-item" onclick="window.location.href='/mobile/form/{{ $greenhouseId }}/{{ $bay['id'] }}/{{ $jobTypeId }}'">
            <div class="menu-icon">🧩</div>
            <div class="menu-content">
                <div class="menu-title">{{ $bay['name'] }}</div>
                <div class="menu-subtitle">{{ $bay['description'] ?? 'Bay' }}</div>
            </div>
            <div style="font-size: 20px; color: #a0aec0;">›</div>
        </div>
    @empty
        <div style="text-align: center; padding: 40px; color: #94a3b8;">
            <span style="font-size: 48px; display: block; margin-bottom: 10px;">🧩</span>
            <p>No bays available</p>
        </div>
    @endforelse
@endsection

@section('nav')
    <div class="nav-bar">
        <div class="nav-item" onclick="window.location.href='/mobile/dashboard'">
            <span>🏠</span>
            Home
        </div>
        <div class="nav-item active">
            <span>🧩</span>
            Bays
        </div>
        <div class="nav-item" onclick="document.getElementById('logout-form').submit()">
            <span>🚪</span>
            Logout
        </div>
    </div>
    <form id="logout-form" action="{{ route('mobile.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
@endsection