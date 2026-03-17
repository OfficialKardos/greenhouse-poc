# Greenhouse Operations Management System

A comprehensive system for greenhouse workers to record operational activities including chemical applications, crop inspections, soil sampling, and plant growth tracking.

## Architecture

- **Backend API**: Laravel 12
- **Web Admin Panel**: Vue.js 3 with Tailwind CSS
- **Mobile App**: NativePHP (iOS/Android)
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission

## Features

### Admin Panel
- ✅ Complete CRUD for Greenhouses, Bays, Job Types
- ✅ Dynamic form builder with custom fields
- ✅ Dropdown list management
- ✅ User management with role-based access (Admin/Supervisor/Worker)
- ✅ Comprehensive reporting with CSV export
- ✅ Photo upload and management
- ✅ Responsive design with mobile hamburger menu

### Mobile App (Worker)
- ✅ Native iOS and Android apps
- ✅ Offline-first architecture
- ✅ Dynamic forms based on admin configuration
- ✅ Photo capture and upload
- ✅ Job history with filters
- ✅ Edit submitted jobs
- ✅ Background sync

## Screenshots

*Add screenshots here*

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL
- Git

### Backend Setup
```bash
git clone https://github.com/YOUR_USERNAME/greenhouse-poc.git
cd greenhouse-poc
composer install
npm install
cp .env.example .env
php artisan key:generate
# Configure your .env file with database credentials
php artisan migrate --seed
php artisan storage:link
