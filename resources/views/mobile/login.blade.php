@extends('mobile.layout')

@section('title', 'Login - GrowTrack Worker')

@section('content')
    <div class="header">
        <h1>Welcome Back! 👋</h1>
        <p>Sign in to start working</p>
    </div>
    
    <form method="POST" action="{{ route('mobile.login.submit') }}">
        @csrf
        
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" 
                   placeholder="worker@greenhouse.com" value="{{ old('email') }}" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-input" 
                   placeholder="••••••••" required>
        </div>
        
        <button type="submit" class="btn-primary">Sign In</button>
    </form>
    
    <div style="margin-top: 30px; padding: 20px; background: #f1f5f9; border-radius: 15px;">
        <p style="font-size: 12px; color: #64748b; margin-bottom: 10px;">Demo Credentials:</p>
        <p style="font-size: 14px; margin-bottom: 5px;">📧 worker@greenhouse.com</p>
        <p style="font-size: 14px;">🔑 password</p>
    </div>
@endsection