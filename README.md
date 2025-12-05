# Project Manager

A modern Laravel + Vue 3 project management application with role-based authentication, task management, and client/project tracking.

## Quick Start

Follow these steps to set up and run the application:

### 1. Clone the Repository

```bash
git clone https://github.com/monicse09ku/ProjectManager.git
cd ProjectManager
```

### 2. Update Environment File

Copy the example environment file and configure it with your database settings:

```bash
cp .env.example .env
```

Update the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_manager
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Install Dependencies

```bash
composer install
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Seed the Database

This will create sample data including clients, projects, users, and tasks:

```bash
php artisan db:seed
```

### 6. Start Development Server

```bash
composer run dev
```

The application will be available at `http://localhost:8000`

## Login Credentials

After seeding the database, use these credentials to log in:

### Admin User
- **Email**: admin@example.com
- **Password**: password
- **Role**: Admin (full access to all features)

### Regular Users
- **Email**: john@example.com
- **Password**: password
- **Role**: User (can only view assigned tasks)

- **Email**: jane@example.com
- **Password**: password
- **Role**: User

- **Email**: bob@example.com
- **Password**: password
- **Role**: User

## Features

- **Role-Based Authentication**: Separate dashboards for Admin and User roles
- **Client Management**: Create, read, update, and delete clients
- **Project Management**: Manage projects associated with clients
- **Task Management**: Assign tasks to users with deadline tracking
- **Dashboard Analytics**: View client, project, and task statistics
- **User Task View**: Regular users can view only their assigned tasks
- **Email Notifications**: Automated notifications for task assignments
- **Queue Jobs**: Background job processing for notifications

## What's Included in the Seeder

The database seeder includes:

- **1 Admin User** with full system access
- **3 Regular Users** who can view their assigned tasks
- **Sample Clients** for testing client management
- **Sample Projects** associated with clients
- **Sample Tasks** assigned to users

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Vue 3 + TypeScript + Tailwind CSS
- **Database**: MySQL/MariaDB
- **Authentication**: Laravel Fortify
