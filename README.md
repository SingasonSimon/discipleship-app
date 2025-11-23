# Enhanced Follow-up & Discipleship System

A comprehensive web-based Church Follow-up & Discipleship Management System built with Laravel 12, designed to help churches register members, run discipleship classes, track attendance and spiritual progress, automate SMS/email reminders, and provide dashboards for pastors and administrators.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [API Documentation](#api-documentation)
- [Architecture](#architecture)
- [Development](#development)
- [Testing](#testing)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## Overview

The Enhanced Follow-up & Discipleship System is a modern, feature-rich application designed to streamline church discipleship programs. It provides comprehensive tools for member management, class scheduling, attendance tracking, mentorship programs, and automated communication.

### Key Capabilities

- **Member Management**: Complete member registration, profile management, and tracking
- **Class Management**: Create and manage discipleship classes with scheduling and content
- **Attendance Tracking**: Real-time attendance tracking with detailed statistics
- **Mentorship Programs**: Manage mentor-mentee relationships and track progress
- **Automated Messaging**: Schedule and send email/SMS reminders and notifications
- **Analytics & Reporting**: Comprehensive dashboards and reports for decision-making
- **RESTful API**: Full API support for mobile and third-party integrations

## Features

### Core Functionality

- **Member Registration**: Manual registration and CSV import with queued processing
- **Discipleship Classes**: Create and manage classes with mentor assignment and scheduling
- **Class Content**: Organize and deliver class materials with progress tracking
- **Session Management**: Schedule class sessions with Google Meet integration
- **Attendance Tracking**: Mark attendance for class sessions with status tracking (present, absent, excused)
- **Bulk Attendance**: Efficient bulk attendance marking for multiple members
- **Progress Monitoring**: Track spiritual milestones and class completion
- **Mentorship Management**: Create and manage mentor-mentee relationships
- **Automated Messaging**: Schedule email reminders with template support
- **Message Logging**: Complete audit trail of all sent messages
- **Dashboard Analytics**: Real-time insights on attendance trends, completion rates, and member engagement
- **Advanced Reporting**: Comprehensive analytics for attendance trends, member engagement, class performance, and mentorship success
- **RESTful API**: Complete API for mobile/web clients with token-based authentication
- **Role-Based Access Control**: Granular permissions based on user roles

### User Roles & Permissions

The system supports four user roles with distinct permissions:

- **Admin**: Full system access, user management, system configuration, and all administrative functions
- **Pastor**: Oversight of discipleship programs, member progress tracking, class management, and mentorship oversight
- **Mentor**: Class management, attendance tracking, member progress monitoring, and mentorship responsibilities
- **Member**: View personal progress, enroll in classes, view class content, and receive communications

## System Requirements

### Server Requirements

- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Node.js**: 18+ and npm
- **Database**: MySQL 8.0+ or MariaDB 10.3+
- **Web Server**: Nginx or Apache with mod_rewrite
- **Extensions**: 
  - PDO MySQL
  - OpenSSL
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath
  - Fileinfo
  - GD (for image processing)

### Optional Requirements

- **Redis**: For caching and queue management (recommended for production)
- **Docker & Docker Compose**: For containerized development and deployment
- **Mailhog**: For email testing in development

## Installation

### Quick Start

```bash
# Clone the repository
git clone <repository-url>
cd discipleship-app

# Copy environment file
cp .env.example .env

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Generate application key
php artisan key:generate

# Configure database in .env file
# Edit .env and set your database credentials

# Run migrations
php artisan migrate

# Seed database with demo data
php artisan db:seed --class=DemoSeeder

# Build frontend assets
npm run build

# Start development server
php artisan serve

# In a separate terminal, start queue worker
php artisan queue:work
```

**Default Demo Credentials:**
- **Admin**: `admin@discipleship.local` / `password`
- **Pastor**: `pastor@discipleship.local` / `password`
- **Mentor**: `mentor@discipleship.local` / `password`
- **Member**: `member@discipleship.local` / `password`

### Native Installation (Detailed)

#### Step 1: Clone Repository

```bash
git clone <repository-url>
cd discipleship-app
```

#### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

#### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env
```

Edit the `.env` file with your configuration:

```env
APP_NAME="Enhanced Follow-up & Discipleship"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=discipleship_app
DB_USERNAME=root
DB_PASSWORD=your_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_FROM_ADDRESS="noreply@discipleship.local"
MAIL_FROM_NAME="${APP_NAME}"

# Queue Configuration
QUEUE_CONNECTION=database
```

#### Step 4: Generate Application Key

```bash
php artisan key:generate
```

#### Step 5: Database Setup

```bash
# Create the database (MySQL)
mysql -u root -p
CREATE DATABASE discipleship_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Run migrations
php artisan migrate

# Seed with demo data (optional)
php artisan db:seed --class=DemoSeeder
```

#### Step 6: Build Assets

```bash
# Production build
npm run build

# Or for development with hot reload
npm run dev
```

#### Step 7: Create Storage Link

```bash
php artisan storage:link
```

#### Step 8: Set Permissions

```bash
# Set proper permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Step 9: Start Services

```bash
# Start development server
php artisan serve

# In a separate terminal, start queue worker
php artisan queue:work

# Optional: Start scheduler (for scheduled tasks)
php artisan schedule:work
```

### Docker Installation

#### Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+

#### Installation Steps

1. **Clone and Setup**

```bash
git clone <repository-url>
cd discipleship-app
cp .env.example .env
```

2. **Start Services**

```bash
docker-compose up -d --build
```

3. **Install Dependencies and Setup Database**

```bash
# Install Composer dependencies
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

# Seed database
docker-compose exec app php artisan db:seed --class=DemoSeeder

# Build assets
docker-compose exec app npm install
docker-compose exec app npm run build

# Create storage link
docker-compose exec app php artisan storage:link
```

4. **Access the Application**

- **Web Application**: http://localhost:8000
- **Mailhog (Email Testing)**: http://localhost:8025
- **phpMyAdmin**: http://localhost:8080

## Configuration

### Environment Variables

#### Required Variables

- `APP_NAME`: Application name
- `APP_URL`: Base URL of the application
- `APP_KEY`: Application encryption key (generated automatically)
- `DB_CONNECTION`: Database driver (`mysql`, `sqlite`, etc.)
- `DB_HOST`: Database host
- `DB_DATABASE`: Database name
- `DB_USERNAME`: Database username
- `DB_PASSWORD`: Database password
- `MAIL_MAILER`: Mail driver (`smtp`, `mailhog`, etc.)
- `MAIL_FROM_ADDRESS`: Default sender email address

#### Optional Variables

- `DEFAULT_SHARED_PASSWORD`: Default password for imported members (default: `password`)
- `REDIS_HOST`: Redis server host (for caching/queues)
- `REDIS_PORT`: Redis server port
- `QUEUE_CONNECTION`: Queue driver (`database`, `redis`, `sqs`)
- `ANALYTICS_RECENT_ACTIVITY_DAYS`: Days for recent activity (default: 30)
- `ANALYTICS_RECENT_MESSAGES_DAYS`: Days for recent messages (default: 7)
- `ANALYTICS_ATTENDANCE_TRENDS_MONTHS`: Months for attendance trends (default: 6)

### Mail Configuration

For production, configure SMTP settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

For Gmail, use an [App Password](https://support.google.com/accounts/answer/185833) instead of your regular password.

### Queue Configuration

The system uses database queues by default. For better performance, use Redis:

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

## Usage

### First Login

After seeding the database, use these demo credentials:

- **Admin**: `admin@discipleship.local` / `password`
- **Pastor**: `pastor@discipleship.local` / `password`
- **Mentor**: `mentor@discipleship.local` / `password`
- **Member**: `member@discipleship.local` / `password`

### Basic Workflow

1. **Register Members**: Add members manually or import via CSV
2. **Create Classes**: Set up discipleship classes with mentors
3. **Schedule Sessions**: Create class sessions with dates and times
4. **Track Attendance**: Mark attendance for each session
5. **Manage Mentorships**: Assign mentors to members
6. **Send Messages**: Schedule or send immediate messages to members
7. **View Reports**: Access analytics and reports from the dashboard

### Member Management

- **Manual Registration**: Use the member registration form
- **CSV Import**: Import multiple members via CSV file
- **Member Profiles**: View and edit member information
- **Member Statistics**: Track attendance and progress per member

### Class Management

- **Create Classes**: Set up new discipleship classes
- **Assign Mentors**: Assign mentors to classes
- **Class Content**: Add and organize class materials
- **Session Scheduling**: Create and manage class sessions
- **Attendance Tracking**: Mark and track attendance

### Mentorship Management

- **Create Mentorships**: Assign mentors to members
- **Track Progress**: Monitor mentorship relationships
- **Mentorship Statistics**: View mentorship success metrics

## API Documentation

The system provides a complete RESTful API at `/api/v1/`. All API endpoints require authentication via Laravel Sanctum tokens.

### Authentication

**POST** `/api/v1/auth/login`

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@discipleship.local",
    "password": "password"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@discipleship.local",
      "role": "admin"
    },
    "token": "1|...",
    "token_type": "Bearer"
  }
}
```

### Using the API

Include the token in the Authorization header:

```bash
curl -X GET http://localhost:8000/api/v1/members \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Complete API Documentation

For complete API documentation, see [docs/API.md](docs/API.md).

## Architecture

### Technology Stack

- **Backend Framework**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL 8.0+ / MariaDB 10.3+
- **Frontend**: Blade templates with TailwindCSS 3
- **JavaScript**: Alpine.js for interactivity, Chart.js for analytics
- **Authentication**: Laravel Breeze with email verification
- **API Authentication**: Laravel Sanctum
- **Queue System**: Database driver (Redis optional)
- **Mail**: SMTP with Mailhog for development

### Application Structure

```
app/
├── Console/Commands/     # Artisan commands
├── Events/              # Application events
├── Http/
│   ├── Controllers/     # Web and API controllers
│   ├── Middleware/      # HTTP middleware
│   ├── Requests/        # Form request validation
│   └── Resources/       # API resources
├── Listeners/           # Event listeners
├── Models/              # Eloquent models
├── Notifications/       # Email/SMS notifications
├── Policies/           # Authorization policies
├── Providers/           # Service providers
└── Services/           # Business logic services

database/
├── migrations/          # Database migrations
├── factories/           # Model factories
└── seeders/            # Database seeders

resources/
├── views/              # Blade templates
├── css/                # Stylesheets
└── js/                 # JavaScript files

routes/
├── web.php             # Web routes
├── api.php             # API routes
└── console.php         # Console routes
```

### Key Components

- **Models**: Eloquent ORM models for database interactions
- **Controllers**: Handle HTTP requests and responses
- **Services**: Business logic and complex operations
- **Policies**: Authorization rules for resources
- **Notifications**: Email and SMS notification classes
- **Jobs**: Queue jobs for background processing
- **Commands**: Artisan commands for scheduled tasks

## Development

### Development Workflow

1. Create a feature branch
2. Make your changes
3. Write tests
4. Run tests and code quality checks
5. Submit a pull request

### Available Commands

#### Database

```bash
php artisan migrate              # Run migrations
php artisan migrate:rollback     # Rollback last migration
php artisan migrate:refresh      # Rollback and re-run migrations
php artisan db:seed              # Run all seeders
php artisan db:seed --class=DemoSeeder  # Run specific seeder
```

#### Queue Management

```bash
php artisan queue:work           # Process queued jobs
php artisan queue:listen          # Listen for queued jobs
php artisan queue:failed          # List failed jobs
php artisan queue:retry all       # Retry all failed jobs
php artisan queue:retry {id}      # Retry specific failed job
```

#### Custom Commands

```bash
php artisan messages:send-scheduled           # Send scheduled messages
php artisan messages:send-scheduled --dry-run # Test without sending
php artisan schedule:list                     # View scheduled tasks
php artisan schedule:work                     # Run scheduler (dev)
```

#### Code Generation

```bash
php artisan make:model Member                 # Create model
php artisan make:controller MemberController # Create controller
php artisan make:migration create_members_table # Create migration
php artisan make:request MemberRequest       # Create form request
php artisan make:policy MemberPolicy         # Create policy
```

### Code Quality

#### PHP CS Fixer

```bash
# Check code style
./vendor/bin/php-cs-fixer fix --dry-run --diff

# Fix code style issues
./vendor/bin/php-cs-fixer fix
```

#### PHPStan

```bash
# Run static analysis
./vendor/bin/phpstan analyse
```

## Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/MemberRegistrationTest.php

# Run with coverage
php artisan test --coverage
```

### Test Coverage

The application includes comprehensive test coverage:

- ✅ User authentication and role-based access
- ✅ Member registration and management
- ✅ Class creation and scheduling
- ✅ Attendance tracking and bulk operations
- ✅ Mentorship management
- ✅ API authentication and authorization
- ✅ CSV import functionality
- ✅ Database relationships and constraints

### Test Structure

```
tests/
├── Feature/          # Feature tests (HTTP requests)
│   ├── Api/          # API endpoint tests
│   └── Auth/         # Authentication tests
└── Unit/             # Unit tests (isolated components)
    ├── Models/       # Model tests
    └── Services/     # Service tests
```

## Deployment

### Production Requirements

- PHP 8.2+ with required extensions
- MySQL 8.0+ or MariaDB 10.3+
- Web server (Nginx recommended)
- SSL certificate for HTTPS
- Queue worker (supervisor or systemd)
- Redis (recommended for caching and queues)

### Pre-Deployment Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure production database
- [ ] Set up production mail server
- [ ] Configure Redis for caching/queues
- [ ] Set up SSL certificate
- [ ] Configure queue worker
- [ ] Set up backup system
- [ ] Configure log rotation

### Deployment Steps

1. **Upload Files**

```bash
# Clone or upload files to server
git clone <repository-url>
cd discipleship-app
```

2. **Install Dependencies**

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

3. **Configure Environment**

```bash
cp .env.example .env
# Edit .env with production settings
php artisan key:generate
```

4. **Database Setup**

```bash
php artisan migrate --force
php artisan db:seed --class=DemoSeeder  # Optional
```

5. **Optimize Application**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

6. **Set Permissions**

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

7. **Setup Queue Worker**

Create `/etc/supervisor/conf.d/discipleship-worker.conf`:

```ini
[program:discipleship-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/discipleship-app/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/discipleship-app/storage/logs/worker.log
```

8. **Setup Scheduler**

Add to crontab:

```bash
* * * * * cd /path/to/discipleship-app && php artisan schedule:run >> /dev/null 2>&1
```

### Docker Production

```bash
# Build production image
docker build -t discipleship-app .

# Run with production compose
docker-compose -f docker-compose.prod.yml up -d
```

## Troubleshooting

### Common Issues

#### Database Connection Error

**Problem**: Cannot connect to database

**Solutions**:
- Verify database credentials in `.env`
- Ensure database server is running
- Check database user has proper permissions
- Verify database exists

#### Permission Denied Errors

**Problem**: Cannot write to storage or cache directories

**Solutions**:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Queue Jobs Not Processing

**Problem**: Queued jobs are not being processed

**Solutions**:
- Ensure queue worker is running: `php artisan queue:work`
- Check queue connection in `.env`
- Verify database jobs table exists
- Check queue logs

#### Assets Not Loading

**Problem**: CSS/JS files not loading

**Solutions**:
- Run `npm run build`
- Clear view cache: `php artisan view:clear`
- Verify `public/build` directory exists
- Check file permissions

#### Email Not Sending

**Problem**: Emails are not being sent

**Solutions**:
- Verify mail configuration in `.env`
- Check mail server credentials
- Ensure queue worker is running (if using queues)
- Check mail logs in `storage/logs`

#### API Authentication Failing

**Problem**: API requests return 401 Unauthorized

**Solutions**:
- Verify token is included in Authorization header
- Check token hasn't expired
- Ensure `SANCTUM_STATEFUL_DOMAINS` is configured
- Verify CORS settings

### Getting Help

- Check the [Laravel Documentation](https://laravel.com/docs)
- Review application logs in `storage/logs`
- Check GitHub issues
- Contact system administrator

## Contributing

We welcome contributions! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Write or update tests
5. Ensure all tests pass (`php artisan test`)
6. Run code quality checks (`./vendor/bin/php-cs-fixer fix --dry-run`)
7. Commit your changes (`git commit -m 'Add amazing feature'`)
8. Push to the branch (`git push origin feature/amazing-feature`)
9. Open a Pull Request

### Code Style

- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add comments for complex logic
- Update documentation as needed

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:

- Create an issue in the repository
- Check the [API Documentation](docs/API.md)
- Review the [Laravel Documentation](https://laravel.com/docs)
- Contact the development team

---

**Version**: 1.0.0  
**Last Updated**: 2025
