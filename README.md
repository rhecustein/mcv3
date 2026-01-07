# MCv3 - Multi-Tenant Healthcare Platform

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

## 📋 Overview

MCv3 adalah platform manajemen kesehatan berbasis Laravel 12 dengan arsitektur multi-tenant untuk mengelola klinik kesehatan, medical checkup (MCU), dan konsultasi psikologi. Platform ini mendukung model bisnis B2B (corporate) dan B2C (individual).

## ✨ Features

### 🏥 Core Features
- **Multi-Tenant Architecture** - Subdomain-based tenant isolation
- **Medical Checkup Management** - Package management, booking, dan reporting
- **Psychology Consultation** - Video/audio/chat sessions dengan psikolog berlisensi
- **Corporate Health Management** - Employee wellness programs
- **Payment Integration** - Midtrans payment gateway
- **Role-Based Access Control** - Spatie Permission untuk granular permissions

### 🧠 Psychology Module
- Psychologist marketplace dengan filtering (expertise, city, specialization)
- Session booking dengan availability checking
- Multiple session types (video, audio, chat, onsite)
- Emergency session support
- Session rating & feedback
- Automated reminders

### 👥 Corporate Features
- Employee enrollment & management
- Health report analytics
- Voucher system
- Aggregated wellness insights (anonymous)
- Compliance reporting

### 🔒 Security Features
- Laravel Sanctum - API authentication
- Session management & auto-logout
- IP locking
- Activity logging (Spatie Activity Log)
- Multi-factor authentication support
- Rate limiting

## 🛠️ Tech Stack

### Backend
- **Laravel 12** - Latest PHP framework
- **PHP 8.2+** - Modern PHP features
- **MySQL 8** - Primary database
- **Redis** - Caching & queue management
- **Laravel Sanctum** - API authentication
- **Laravel Telescope** - Debugging & monitoring

### Frontend
- **Vite 6** - Modern build tool
- **Tailwind CSS 4** - Utility-first CSS
- **Alpine.js** - Lightweight JavaScript framework
- **Chart.js** - Data visualization
- **CKEditor 5** - Rich text editing

### Testing
- **Pest PHP** - Modern testing framework
- **PHPUnit** - Unit testing foundation

### DevOps
- **Laravel Pint** - Code formatting
- **Laravel Sail** - Docker development environment

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ & NPM
- MySQL 8.0+
- Redis (optional but recommended)

### Step 1: Clone Repository
```bash
git clone https://github.com/your-org/mcv3.git
cd mcv3
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### Step 3: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mcv3
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 5: Run Migrations & Seeders
```bash
# Run migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed
```

### Step 6: Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### Step 7: Start Development Server
```bash
# Option 1: Using Laravel Artisan
php artisan serve

# Option 2: Using Composer script (recommended - runs server, queue, and vite)
composer dev
```

Visit: `http://localhost:8000`

## 🧪 Testing

```bash
# Run all tests
composer test

# Run tests with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/Api/V1/PsychologySessionApiTest.php

# Run tests in watch mode (auto-reload on file changes)
php artisan test --watch
```

### Current Test Coverage
- **Model Tests**: Psychologist, PsychologySession
- **API Tests**: Psychology Sessions, Psychologists, Patients
- **Feature Tests**: Booking flow, cancellation, rating
- **Target Coverage**: 80%+

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper `APP_URL`
- [ ] Setup SSL certificate
- [ ] Configure Redis for cache & sessions
- [ ] Setup queue workers
- [ ] Enable OPcache
- [ ] Configure backup strategy
- [ ] Setup monitoring (Sentry, Laravel Pulse)
- [ ] Configure CORS settings
- [ ] Setup CDN for static assets

### Deployment Script
```bash
# Pull latest code
git pull origin main

# Install dependencies (production)
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
php artisan queue:restart

# Reload PHP-FPM
sudo systemctl reload php8.2-fpm
```

## 📚 API Documentation

### Base URL
```
Production: https://api.yourdomain.com
Development: http://localhost:8000/api
```

### Authentication
MCv3 uses Laravel Sanctum for API authentication.

#### Obtaining API Token
```bash
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}

# Response
{
  "token": "1|xxxxxxxxxxxxxxxxxxxxx",
  "user": { ... }
}
```

#### Using API Token
```bash
GET /api/v1/psychology/sessions
Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxx
```

### API Endpoints

#### Psychology Module

**List Psychologists**
```http
GET /api/v1/psychologists
Query Parameters:
  - expertise: string (e.g., "anxiety", "depression")
  - city: string
  - specialization: string
  - accepts_emergency: boolean
  - per_page: integer (1-100)
```

**Get Psychologist Details**
```http
GET /api/v1/psychologists/{id}
```

**Check Availability**
```http
GET /api/v1/psychologists/{id}/availability?date=2025-11-20
```

**Book Session**
```http
POST /api/v1/psychology/sessions
Authorization: Bearer {token}
Content-Type: application/json

{
  "psychologist_id": 1,
  "session_type": "video",
  "scheduled_at": "2025-11-20T10:00:00Z",
  "client_concern": "Stress management"
}
```

**List My Sessions**
```http
GET /api/v1/psychology/sessions
Authorization: Bearer {token}
Query Parameters:
  - status: string (scheduled, completed, cancelled)
  - per_page: integer
```

**Cancel Session**
```http
POST /api/v1/psychology/sessions/{id}/cancel
Authorization: Bearer {token}
Content-Type: application/json

{
  "reason": "Personal emergency"
}
```

**Rate Session**
```http
POST /api/v1/psychology/sessions/{id}/rate
Authorization: Bearer {token}
Content-Type: application/json

{
  "rating": 5,
  "feedback": "Excellent session!"
}
```

### Rate Limiting
- Default: 60 requests per minute per IP
- Authenticated: 1000 requests per minute per user

### Response Format
All API responses follow this structure:

**Success (200-299)**
```json
{
  "data": { ... },
  "meta": { ... },
  "links": { ... }
}
```

**Error (400-599)**
```json
{
  "message": "Error message",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

## 🏗️ Architecture

### Multi-Tenant Architecture
MCv3 menggunakan subdomain-based multi-tenancy:
- Main domain: `mcv3.com`
- Tenant: `kimiafarma.mcv3.com`
- Platform admin: `platform.mcv3.com`

### Directory Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/V1/              # API V1 controllers
│   │   ├── Platform/            # Platform admin controllers
│   │   └── Corporate/           # Corporate portal controllers
│   ├── Middleware/              # Custom middleware
│   ├── Requests/Api/V1/         # Form requests
│   └── Resources/Api/V1/        # API resources
├── Models/                      # Eloquent models
├── Services/                    # Business logic services
└── Policies/                    # Authorization policies

database/
├── factories/                   # Model factories
├── migrations/                  # Database migrations
└── seeders/                     # Database seeders

routes/
├── api.php                      # API routes
├── web.php                      # Web routes
├── tenant.php                   # Tenant-specific routes
└── platform.php                 # Platform admin routes

tests/
├── Feature/
│   └── Api/V1/                  # API feature tests
└── Unit/
    └── Models/                  # Model unit tests
```

### Security Best Practices
1. **Input Validation** - Form Requests untuk semua API endpoints
2. **SQL Injection Prevention** - Eloquent ORM & parameter binding
3. **XSS Protection** - Blade templating auto-escape
4. **CSRF Protection** - Laravel CSRF token
5. **Rate Limiting** - Throttle middleware
6. **Authorization** - Policies & Gates
7. **Tenant Isolation** - Automatic tenant scoping

## 🔧 Configuration

### Environment Variables
```env
# Application
APP_NAME="MCv3 Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mcv3
DB_USERNAME=root
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=

# Payment
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false

# Monitoring
SENTRY_LARAVEL_DSN=
TELESCOPE_ENABLED=true
```

## 📖 Common Tasks

### Creating a New Tenant
```bash
php artisan tenant:create --name="PT Kimia Farma" --slug=kimiafarma --email=admin@kimiafarma.com
```

### Running Queue Worker
```bash
# Development
php artisan queue:listen

# Production (with Supervisor)
php artisan queue:work --tries=3 --timeout=90
```

### Clearing Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Backup
```bash
php artisan backup:run
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Code Style
```bash
# Format code using Laravel Pint
./vendor/bin/pint

# Check code style
./vendor/bin/pint --test
```

## 📄 License

This project is licensed under the MIT License.

## 👥 Team

- **Backend Team** - Laravel development
- **Frontend Team** - UI/UX implementation
- **DevOps Team** - Infrastructure & deployment

## 📞 Support

- **Documentation**: [docs.yourdomain.com](https://docs.yourdomain.com)
- **Issues**: [GitHub Issues](https://github.com/your-org/mcv3/issues)
- **Email**: support@yourdomain.com

## 🗺️ Roadmap

### Q1 2026
- [ ] Mobile app (React Native)
- [ ] Real-time notifications (WebSocket)
- [ ] Advanced analytics dashboard
- [ ] Export features (PDF, Excel)

### Q2 2026
- [ ] Telemedicine video call integration
- [ ] AI-powered health insights
- [ ] Integration dengan HRIS platforms
- [ ] Multi-language support

---

**Built with ❤️ using Laravel 12**
