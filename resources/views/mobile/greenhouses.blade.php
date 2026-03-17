@extends('mobile.layout')

@section('title', 'Select Greenhouse')

@section('content')
    <div class="header">
        <h1>Select Greenhouse</h1>
        <p>Choose where to work today</p>
    </div>
    
    @if(isset($offline) && $offline)
        <div class="alert alert-warning" style="background: #fef3c7; color: #92400e; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
            ⚡ Offline mode - showing cached data
        </div>
    @endif
    
    @forelse($greenhouses as $greenhouse)
        <div class="menu-item" onclick="window.location.href='/mobile/jobs/{{ $greenhouse['id'] }}'">
            <div class="menu-icon">🌿</div>
            <div class="menu-content">
                <div class="menu-title">{{ $greenhouse['name'] }}</div>
                <div class="menu-subtitle">{{ $greenhouse['bays_count'] ?? count($greenhouse['bays'] ?? []) }} bays</div>
            </div>
            <div style="font-size: 20px; color: #a0aec0;">›</div>
        </div>
    @empty
        <div style="text-align: center; padding: 40px; color: #94a3b8;">
            <span style="font-size: 48px; display: block; margin-bottom: 10px;">🏢</span>
            <p>No greenhouses found</p>
        </div>
    @endforelse
@endsection

@section('nav')
    <div class="nav-bar">
        <div class="nav-item active">
            <span>🏠</span>
            Home
        </div>
        <div class="nav-item" onclick="window.location.href='/mobile/submitted'">
            <span>📋</span>
            Submitted Jobs
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