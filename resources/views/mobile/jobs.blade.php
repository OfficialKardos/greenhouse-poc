@extends('mobile.layout')

@section('title', 'Select Job Type')

@section('content')
<div class="btn-back" onclick="window.history.back()">
    <span>←</span> Back
</div>

<div class="header">
    <h1>Select Job Type</h1>
    <p>What are you doing today?</p>
</div>

@forelse($jobTypes as $job)
    <div class="menu-item" onclick="window.location.href='/mobile/bays/{{ $greenhouseId }}/{{ $job['id'] }}'">
        <div class="menu-icon">
            @php
                // Convert Font Awesome classes to emoji or simple icons
                $icon = $job['icon'] ?? '📋';
                $displayIcon = $icon;
                
                // Map common Font Awesome classes to emoji
                $iconMap = [
                    'fas fa-flask' => '🧪',
                    'fas fa-clipboard-list' => '📋',
                    'fas fa-seedling' => '🌱',
                    'fas fa-chart-line' => '📈',
                    'fas fa-microscope' => '🔬',
                    'fas fa-tint' => '💧',
                    'fas fa-leaf' => '🌿',
                    'fas fa-bug' => '🐛',
                    'fas fa-vial' => '🧪',
                    'fas fa-ruler' => '📏',
                    'fas fa-camera' => '📷',
                    'fas fa-pen' => '✏️',
                    'fas fa-stopwatch' => '⏱️',
                    'fas fa-thermometer-half' => '🌡️',
                    'fas fa-pills' => '💊',
                    'fas fa-tools' => '🔧',
                    'fas fa-tasks' => '✅',
                    'fas fa-question' => '❓',
                    'fas fa-calendar' => '📅',
                    'fas fa-clock' => '⏰',
                    'fas fa-bolt' => '⚡',
                    'fas fa-toggle-on' => '🔘',
                    'fas fa-font' => 'Aa',
                    'fas fa-align-left' => '📄',
                    'fas fa-hashtag' => '#️⃣',
                ];
                
                if (isset($iconMap[$icon])) {
                    $displayIcon = $iconMap[$icon];
                } elseif (strpos($icon, 'fa-') !== false) {
                    // If it's a Font Awesome class but not in map, use default
                    $displayIcon = '📋';
                }
            @endphp
            {{ $displayIcon }}
        </div>
        <div class="menu-content">
            <div class="menu-title">{{ $job['name'] }}</div>
            <div class="menu-subtitle">{{ $job['description'] ?? 'Record activity' }}</div>
        </div>
        <div class="menu-arrow">›</div>
    </div>
@empty
    <div class="empty-state">
        <div class="empty-icon">📋</div>
        <p>No job types available</p>
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
            <span>📋</span>
            Jobs
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