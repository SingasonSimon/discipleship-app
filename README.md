# Enhanced Follow-up & Discipleship System

A comprehensive web-based Church Follow-up & Discipleship Management System built with Laravel 12, designed to help churches register members, run discipleship classes, track attendance and spiritual progress, automate SMS/email reminders, and provide dashboards for pastors/administrators.

## 🚀 Quick Start

```bash
# Clone and setup
git clone <repository-url>
cd discipleship-app
cp .env.example .env

# Install dependencies
composer install
npm install

# Setup database
php artisan migrate
php artisan db:seed --class=DemoSeeder

# Start development server
php artisan serve
```

**Demo Login:** `admin@discipleship.local` / `password`

**Google Sign In:** Configure Google OAuth credentials in `.env` to enable Google Sign In

**API Base URL:** `http://localhost:8000/api/v1`

## Features

### Core Functionality
- **Member Registration**: Manual registration and optional CSV import with queued processing
- **Discipleship Classes**: Create and manage classes with mentor assignment and scheduling
- **Attendance Tracking**: Mark attendance for class sessions with status tracking
- **Progress Monitoring**: Track spiritual milestones and mentorship relationships
- **Automated Messaging**: Schedule email reminders with template support
- **Dashboard Analytics**: Real-time insights on attendance trends, completion rates, and member engagement
- **Advanced Reporting**: Comprehensive analytics for attendance trends, member engagement, class performance, and mentorship success
- **RESTful API**: Complete API for mobile/web clients with token-based authentication

### User Roles & Permissions
- **Admin**: Full system access, user management, and configuration
- **Pastor**: Oversight of discipleship programs and member progress
- **Coordinator**: Class management and attendance tracking
- **Member**: View personal progress and receive communications

## Tech Stack

- **Framework**: Laravel 12 (PHP 8.1+)
- **Database**: MySQL 8+
- **Frontend**: Blade templating with TailwindCSS 3
- **Authentication**: Laravel Breeze with email verification
- **API**: Laravel Sanctum for token-based authentication
- **Queue System**: Database driver (Redis optional)
- **Mail**: SMTP with Mailhog for development
- **OAuth**: Google Sign In support via Laravel Socialite

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8+
- (Optional) Docker and Docker Compose for containerized development

### Native Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd discipleship-app
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   ```

   Update the `.env` file with your configuration:
   ```env
   APP_NAME="Enhanced Follow-up & Discipleship"
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_DATABASE=discipleship_app
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   MAIL_MAILER=smtp
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="Discipleship System"

   # Google OAuth Configuration (optional)
   GOOGLE_CLIENT_ID=your_google_client_id
   GOOGLE_CLIENT_SECRET=your_google_client_secret
   GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   # Create database and run migrations
   php artisan migrate

   # Seed with demo data
   php artisan db:seed --class=DemoSeeder
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

8. **Process queued jobs** (in a separate terminal)
   ```bash
   php artisan queue:work
   ```

### Docker Installation

1. **Clone and setup**
   ```bash
   git clone <repository-url>
   cd discipleship-app
   cp .env.example .env
   ```

2. **Start services**
   ```bash
   docker-compose up -d --build
   ```

3. **Install dependencies and setup database**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate
   docker-compose exec app php artisan db:seed --class=DemoSeeder
   ```

4. **Access the application**
   - Web: http://localhost:8000
   - Mailhog: http://localhost:8025 (for development email testing)

## Usage

### First Login
After seeding, you can log in with these demo credentials:
- **Admin**: admin@discipleship.local / password
- **Pastor**: pastor@discipleship.local / password
- **Coordinator**: coordinator@discipleship.local / password

### Google Sign In Setup

1. **Create Google OAuth Credentials:**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select an existing one
   - Enable Google+ API
   - Go to "Credentials" → "Create Credentials" → "OAuth client ID"
   - Choose "Web application"
   - Add authorized redirect URI: `http://localhost:8000/auth/google/callback` (or your production URL)
   - Copy the Client ID and Client Secret

2. **Update `.env` file:**
   ```env
   GOOGLE_CLIENT_ID=your_client_id_here
   GOOGLE_CLIENT_SECRET=your_client_secret_here
   GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
   ```

3. **Install Laravel Socialite:**
   ```bash
   composer require laravel/socialite
   ```

4. **Run migrations:**
   ```bash
   php artisan migrate
   ```

Users can now sign in with Google by clicking the "Sign in with Google" button on the login page.

### API Usage

The system provides a complete REST API at `/api/v1/`. All API endpoints require authentication via Sanctum tokens.

**Authentication:**
```bash
# Login to get token
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@discipleship.local","password":"password"}'
```

**Example API calls:**
```bash
# Get members (requires authentication)
curl -X GET http://localhost:8000/api/v1/members \
  -H "Authorization: Bearer YOUR_TOKEN"

# Create a new member
curl -X POST http://localhost:8000/api/v1/members \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "John Doe",
    "phone": "0712345678",
    "email": "john@example.com",
    "date_of_conversion": "2024-01-01",
    "preferred_contact": "sms"
  }'

# Get dashboard summary
curl -X GET http://localhost:8000/api/v1/dashboard/summary \
  -H "Authorization: Bearer YOUR_TOKEN"
```

📖 **Complete API Documentation:** [docs/API.md](docs/API.md)

## Environment Variables

### Required
- `APP_NAME`: Application name
- `APP_URL`: Base URL of the application
- `DB_*`: Database connection settings
- `MAIL_*`: SMTP configuration for email sending

### Optional
- `GOOGLE_CLIENT_ID`: Google OAuth Client ID (for Google Sign In)
- `GOOGLE_CLIENT_SECRET`: Google OAuth Client Secret
- `GOOGLE_REDIRECT_URI`: Google OAuth redirect URI (default: `http://localhost:8000/auth/google/callback`)
- `REDIS_*`: Redis configuration (for caching and queues)
- `QUEUE_CONNECTION`: Queue driver (`database`, `redis`, `sqs`)

## Development

### Available Commands

**Database:**
```bash
php artisan migrate              # Run migrations
php artisan migrate:rollback     # Rollback migrations
php artisan db:seed              # Run all seeders
php artisan db:seed --class=DemoSeeder  # Run demo seeder only
```

**Queue Management:**
```bash
php artisan queue:work           # Process queued jobs
php artisan queue:failed         # List failed jobs
php artisan queue:retry          # Retry failed jobs
```

**Custom Commands:**
```bash
php artisan messages:send-scheduled     # Send scheduled messages (manual)
php artisan messages:send-scheduled --dry-run  # Test without sending
php artisan schedule:list               # View all scheduled tasks
php artisan schedule:work               # Run scheduler (for development)
php artisan make:model Member           # Generate new model
php artisan make:migration create_members_table  # Generate migration
```

**Testing:**
```bash
php artisan test                 # Run PHPUnit tests
./vendor/bin/phpstan analyse     # Run static analysis
./vendor/bin/php-cs-fixer fix    # Fix code style issues
```

### Code Structure

```
app/
├── Models/              # Eloquent models
├── Http/Controllers/    # Web and API controllers
├── Http/Requests/       # Form request validation
├── Policies/           # Authorization policies
├── Services/           # Business logic services
├── Jobs/               # Queue jobs
├── Console/Commands/   # Artisan commands
└── Notifications/      # Email/SMS notifications

database/
├── migrations/         # Database migrations
├── factories/          # Model factories
└── seeders/           # Database seeders

resources/
├── views/             # Blade templates
├── css/               # Stylesheets
└── js/                # JavaScript files

routes/
├── web.php            # Web routes
└── api.php            # API routes
```

## Deployment

### Production Requirements
- PHP 8.1+ with required extensions
- MySQL 8+ database
- Web server (Nginx/Apache)
- SSL certificate for HTTPS
- Queue worker (via supervisor or systemd)

### Docker Production
```bash
# Build production image
docker build -t discipleship-app .

# Run with production compose file
docker-compose -f docker-compose.prod.yml up -d
```

### Manual Deployment
1. Upload files to server
2. Run `composer install --optimize-autoloader --no-dev`
3. Configure `.env` file
4. Run `php artisan config:cache`
5. Run `php artisan migrate --force`
6. Set up queue worker via supervisor
7. Configure web server (Nginx example provided)

## Testing

Run the full test suite:
```bash
php artisan test
```

The application includes:
- **34 Unit tests** for models and business logic
- **Feature tests** for user flows and authentication
- **API integration tests** for all endpoints
- **Static analysis** with PHPStan (Level 5)
- **Code style checking** with PHP CS Fixer (PSR-12)

### Test Coverage
- ✅ User authentication and role-based access
- ✅ Member registration and management
- ✅ Class creation and scheduling
- ✅ Attendance tracking and bulk operations
- ✅ Mentorship management
- ✅ API authentication and authorization
- ✅ CSV import functionality
- ✅ Database relationships and constraints

### Code Quality
- **PHPStan**: Static analysis with Larastan for Laravel-specific checks
- **PHP CS Fixer**: Automated code style fixes (PSR-12 compliant)
- **GitHub Actions**: Automated CI/CD pipeline with tests and quality checks

### CI/CD Pipeline
The project includes a comprehensive GitHub Actions workflow (`.github/workflows/ci.yml`) that runs on every push and pull request:

- **Tests**: PHPUnit test suite with SQLite database
- **Static Analysis**: PHPStan level 5 analysis
- **Code Style**: PHP CS Fixer PSR-12 compliance check
- **Security**: Dependency vulnerability scanning
- **Multi-PHP**: Tests on PHP 8.3 with all required extensions

To run the same checks locally:
```bash
# Run tests
php artisan test

# Static analysis
./vendor/bin/phpstan analyse

# Code style check
./vendor/bin/php-cs-fixer fix --dry-run --diff

# Apply code style fixes
./vendor/bin/php-cs-fixer fix
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Run tests and static analysis
6. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions:
- Create an issue in the repository
- Check the documentation
- Review the Laravel documentation for framework-specific questions

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes and version history.
