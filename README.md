# Task Management System

This is a Task Management System built with Laravel, designed to manage users, roles, tasks, and permissions. The system allows users to register, assign tasks, and manage permissions for different roles.

## Features
- **User Registration**: Users can register via the API.
- **Role Management**: Roles (Admin, Manager, User) are assigned to users.
- **Task Management**: Create, update, view, and delete tasks.
- **Permission Management**: Manage permissions for tasks, including view, create, and assign.
- **API Security**: Secure API endpoints using Laravel Sanctum for authentication.

## Installation

### Prerequisites
- PHP >= 8.1
- Composer
- Laravel 10.x
- MySQL or another database system

### Steps to Set Up

1. **Clone the repository:**
   ```bash
   git clone https://github.com/amrawni33/daniaair_task.git
   cd daniaair_task
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   php artisan serve
