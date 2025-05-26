# Laravel-Inertia Medical Records Application

A **sample medical records management application** built with **Laravel 12**, **Vue 3**, **Inertia.js**, and **PostgreSQL** for **demonstration and showcase purposes**. This project demonstrates enterprise-level architecture patterns and modern full-stack development best practices.

> **⚠️ Important**: This is a **demo application** designed for educational and portfolio showcase purposes only. It is not intended for use in real medical environments or with actual patient data.

## 🏗️ Architecture Overview

### Backend Architecture
- **Laravel 12** with clean service-oriented architecture
- **Service Layer Pattern**: Business logic separated from controllers
- **Repository Pattern**: Data access abstraction through Eloquent
- **Filter Pattern**: Advanced query filtering with reusable filter classes
- **Form Requests**: Validation logic separation

### Tech Stack
- **Backend**: Laravel 12.x, PHP 8.2+
- **Frontend**: Vue 3.5, Inertia.js 2.0, TypeScript 5.2
- **Build Tools**: Vite 6.2, TailwindCSS 4.1
- **Database**: PostgreSQL 13+
- **Testing**: Pest 3.8, PHPUnit
- **State Management**: Pinia 3.0
- **UI Components**: Flowbite Vue, Lucide Icons

### 🎯 Architectural Consistency & Standards
**Pattern Uniformity Across All Entities (Patient, MedicalRecord, Anamnesis):**

- **Interface-Driven Repositories**: All repositories implement consistent interfaces
  ```php
  // Example: PatientRepositoryInterface, MedicalRecordRepositoryInterface, AnamnesisRepositoryInterface
  public function getAll(int $perPage = 10): LengthAwarePaginator;
  public function getById(int $id): ?Model;
  public function create(array $data): Model;
  ```

- **Standardized Request Namespacing**: 
  ```
  App\Http\Requests\Patient\{StorePatientRequest, UpdatePatientRequest}
  App\Http\Requests\MedicalRecord\{StoreMedicalRecordRequest, UpdateMedicalRecordRequest}
  App\Http\Requests\Anamnesis\{StoreAnamnesisRequest, UpdateAnamnesisRequest}
  ```

- **Uniform Service Layer Contracts**: Consistent method signatures and return types
- **Controller Pattern Standardization**: Identical dependency injection and structure
- **Database Relationship Consistency**: Foreign key constraints and indexing patterns

### Frontend Architecture  
- **Vue 3 Composition API**: Modern reactive component patterns with TypeScript
- **Inertia.js 2.0**: Seamless SPA experience without API complexity
- **Pinia Store**: Centralized state management with persistence
- **Component Composition**: Reusable, modular component design
- **TailwindCSS 4.1**: Modern utility-first CSS framework

### Key Architectural Decisions

#### 1. Server-Side Data Management
```php
// Filter Pattern for complex queries
class MedicalRecordFilter {
    public function apply(): Builder {
        foreach ($this->filters as $name => $value) {
            if (method_exists($this, $name) && !empty($value)) {
                $this->$name($value);
            }
        }
        return $this->builder;
    }
}
```

#### 2. URL-Based State Management
```typescript
// Form step state persisted in URL for refresh-safe navigation
function updateStepInUrl(step: number) {
    const url = new URL(window.location)
    url.searchParams.set('step', step.toString())
    router.visit(url.toString(), {
        preserveState: true,
        preserveScroll: true,
        replace: true
    })
}
```

#### 3. Hybrid State Persistence
- **URL**: Current step/page state
- **LocalStorage**: Form data persistence  
- **Server**: Paginated data with filtering

## 🚀 Features

### Core Functionality
- **Multi-step Medical Form**: Patient → Anamnesis → Medical Record
- **Medical Record Status System**: Complete consultation workflow management
  - **Automatic Status Assignment**: New records start with "Pending" status
  - **Consultation Workflow**: Pending → Attending → Finalized/Needs Follow-up
  - **Status History Tracking**: Complete audit trail of all status changes
  - **Status-Based Actions**: Context-aware buttons and workflows
- **Server-side Pagination**: Efficient data loading with filtering
- **Advanced Search**: Case-insensitive patient name filtering
- **Real-time Statistics**: Dashboard metrics with caching
- **CRUD Operations**: Full lifecycle management for all entities

### Technical Features
- **Desktop-First Design**: Clean, professional Tailwind CSS implementation
- **Loading States**: Comprehensive UX feedback
- **Error Handling**: Graceful error management with user notifications
- **Data Validation**: Server-side validation with Form Requests
- **Performance Optimization**: Query optimization and caching
- **TypeScript Support**: Full type safety across the frontend
- **Modern Build Pipeline**: Vite 6.2 with HMR and optimized builds

## 🛠️ Installation

### Prerequisites
- **PHP 8.2+** (Required for Laravel 12)
- **Node.js 18+** 
- **PostgreSQL 13+**
- **Composer 2.x**

### Setup Instructions

1. **Clone and Install Dependencies**
```bash
git clone <repository-url>
cd laravel-inertia-sample
composer install
npm install
```

2. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Database Setup**
```bash
# Configure .env with PostgreSQL credentials
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=medical_records
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations and seed data
php artisan migrate
php artisan db:seed
```

4. **Build and Serve**
```bash
# Development (with HMR)
npm run dev
php artisan serve

# Or use the combined dev script
composer run dev

# Production build
npm run build
php artisan serve
```

## 🚀 Development Commands

Laravel 12 includes modern development scripts for enhanced productivity:

### Quick Development Start
```bash
# Single command to start all development services
composer run dev
# This runs: PHP server + Queue worker + Logs (Pail) + Vite HMR

# Individual commands
php artisan serve          # Laravel development server
npm run dev               # Vite HMR for frontend
php artisan queue:listen  # Background job processing  
php artisan pail          # Real-time log monitoring
```

### Production Deployment
```bash
# Build optimized assets
npm run build

# Or with SSR support
composer run dev:ssr
npm run build:ssr
```

### Code Quality Tools
```bash
# PHP formatting with Laravel Pint
./vendor/bin/pint

# Frontend formatting with Prettier
npm run format
npm run format:check

# ESLint for TypeScript/Vue
npm run lint
```

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/Web/          # Inertia controllers
│   ├── Requests/{Entity}/        # Form validation by entity
│   └── Middleware/
├── Services/                     # Business logic layer
├── Filters/                      # Query filtering classes
├── Repositories/                 # Data access layer
│   └── Interfaces/              # Repository contracts
├── Models/                       # Eloquent models
└── Console/Commands/             # Artisan commands

resources/
├── js/
│   ├── Components/MedicalForm/   # Reusable Vue components
│   ├── Pages/                    # Inertia page components
│   ├── Stores/                   # Pinia state stores
│   ├── Layouts/                  # Layout components
│   └── types/                    # TypeScript definitions
└── css/                          # Tailwind CSS

database/
├── migrations/                   # Database migrations
├── factories/                    # Model factories
└── seeders/                      # Database seeders

tests/
├── Feature/                      # Integration tests (Pest)
└── Unit/                         # Unit tests (Pest)

routes/
└── web.php                       # Application routes
```

## 🔧 Key Components

### Backend Services

#### MedicalRecordQueryService
Handles complex querying with filtering, pagination, and caching:
```php
public function getPaginatedRecords(array $filters): LengthAwarePaginator
{
    return MedicalRecord::with(['patient'])
        ->select('medical_records.*')
        ->filter($filters)
        ->paginate($filters['per_page'])
        ->appends(request()->query());
}
```

#### Filter Pattern Implementation
```php
// Reusable, testable filtering logic
public function patient_filter($value): void
{
    $this->builder->whereHas('patient', function ($query) use ($value) {
        $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($value) . '%']);
    });
}
```

### Frontend Components

#### Multi-Step Form with URL State
```vue
<script setup>
const { currentStep, updateStep, saveFormData } = useMedicalFormStore()

// URL-based step management
watch(() => route.query.step, (newStep) => {
    if (newStep) updateStep(parseInt(newStep))
}, { immediate: true })
</script>
```

#### Server-Side Data Table
```vue
<script setup>
// Debounced search with server-side filtering
const debouncedSearch = debounce((filters) => {
    router.visit('/medical-records', {
        data: filters,
        preserveState: true,
        replace: true
    })
}, 300)
</script>
```

## 🔍 Performance Optimizations

### Database Optimizations
- **Eager Loading**: Prevents N+1 queries
- **Server-side Pagination**: Efficient large dataset handling
- **Query Caching**: Statistics cached for 5 minutes
- **Proper Indexing**: Optimized query performance

### Frontend Optimizations  
- **Debounced Search**: Reduces server requests
- **Component Lazy Loading**: Improved initial load time
- **State Persistence**: Seamless user experience
- **Loading States**: Progressive UI feedback

## 🧪 Testing

### Running Tests
```bash
# Run all tests with Pest
php artisan test

# Or directly with Pest
./vendor/bin/pest

# Run specific test suites
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run tests with coverage
./vendor/bin/pest --coverage
```

### Test Coverage
The application includes comprehensive testing covering:

#### Feature Tests (30 tests) - **Pest Framework**
- **Patient CRUD Operations**: Complete lifecycle testing (7 tests)
  - Create, read, update, delete patients
  - Validation and error handling
- **Medical Record Management**: Full functionality testing (8 tests)
  - Multi-step form creation process
  - CRUD operations with proper validation
  - Database integrity checks
- **Medical Record Status System**: Complete workflow testing (8 tests)
  - Consultation workflow from Pending → Attending → Finalized
  - Status transitions and business logic validation
  - Form validation for consultation data
  - Error handling for invalid operations
- **Anamnesis Operations**: Vital signs management (4 tests)
  - Create and update patient vital signs
  - Validation for numeric fields
  - Patient relationship handling
- **Data Generation Command**: Batch processing (3 tests)
  - Bulk data creation for testing
  - Transaction handling
  - Data integrity validation

#### Unit Tests (8 tests) - **Pest Framework**
- **AnamnesisService**: Business logic testing (2 tests)
  - BMI calculation accuracy
  - Data validation and processing
- **Medical Record Status Logic**: Core functionality testing (6 tests)
  - Automatic status assignment on record creation
  - Status transitions between all states (Pending → Attending → Finalized/Needs Follow-up)
  - Available statuses enumeration
  - Duplicate status prevention
  - Status history tracking and integrity

**Total: 38 tests, 129 assertions - All passing ✅**

### Medical Record Status Testing
The status system includes comprehensive test coverage for:

#### Status Workflow Tests
- **Automatic Assignment**: New medical records automatically receive "Pending" status
- **Consultation Flow**: Starting consultation changes status to "Attending"
- **Completion Options**: Consultations can be completed as "Finalized" or "Needs Follow-up"
- **Status History**: Complete audit trail of all status changes
- **Data Integrity**: Proper validation and error handling throughout the workflow

#### Business Logic Validation
- **Status Transitions**: Only valid status changes are allowed
- **Form Validation**: Required fields enforced during consultation
- **Error Handling**: Graceful handling of invalid records or operations
- **Data Persistence**: Status changes properly saved and retrievable

### Testing Features
- **Pest 3.8**: Modern PHP testing framework
- **Laravel Testing**: Built-in database factories and seeders
- **Feature Testing**: Full HTTP request/response testing with Inertia.js
- **Unit Testing**: Isolated component and business logic testing
- **Database Testing**: RefreshDatabase trait for clean test state
- **Status System Testing**: Complete workflow validation from creation to completion

## 🔒 Security Considerations

### Implemented Security Measures
- **CSRF Protection**: Laravel default middleware
- **Input Validation**: Form Request validation
- **SQL Injection Prevention**: Eloquent ORM usage
- **XSS Protection**: Vue.js automatic escaping

### Development Configuration
- **Authentication Disabled**: For demo purposes only
- **Direct Access**: All routes publicly accessible for demonstration

### Production Recommendations
- Implement authentication middleware around all routes
- Add proper authorization policies
- Implement rate limiting
- Add request logging and monitoring

## 🚦 Application Routes

Since this is an Inertia.js application, we use server-side routing:

### Core Application
- `GET /` - Dashboard with medical records overview

### Medical Records
- `GET /medical-records` - Index with filtering and pagination
- `GET /medical-form` - Multi-step creation form
- `POST /medical-records` - Store new record
- `GET /medical-records/{id}` - Show record details
- `GET /medical-records/{id}/edit` - Edit form
- `PUT /medical-records/{id}` - Update record
- `DELETE /medical-records/{id}` - Delete record

### Consultation Workflow (Status System)
- `POST /medical-records/{id}/start-consultation` - Start consultation (Pending → Attending)
- `GET /medical-records/{id}/consultation` - Consultation interface with patient data
- `PUT /medical-records/{id}/consultation` - Complete consultation (Attending → Finalized/Needs Follow-up)

### Patients
- `GET /patients` - Index with search functionality
- `GET /patients/create` - Create form  
- `POST /patients` - Store new patient
- `GET /patients/{id}` - Show patient details
- `GET /patients/{id}/edit` - Edit form
- `PUT /patients/{id}` - Update patient
- `DELETE /patients/{id}` - Delete patient

### Anamnesis (Vital Signs)
- `POST /patients/{patientId}/anamneses` - Store/update patient vital signs

## 📈 Future Enhancements

### Immediate Improvements
- [ ] Add comprehensive component testing with Vue Test Utils
- [ ] Implement Redis caching layer for better performance
- [ ] Add database indexes for optimized queries
- [ ] Implement soft deletes for data recovery
- [ ] Add CSV/PDF export functionality for reports

### Advanced Features
- [ ] Real-time notifications with WebSockets
- [ ] Advanced reporting and analytics dashboard
- [ ] Document upload and management for medical records
- [ ] Appointment scheduling system
- [ ] Medical imaging integration
- [ ] Multi-language support

### Production Readiness
- [ ] Implement authentication and authorization system
- [ ] Add comprehensive API documentation
- [ ] Set up monitoring and logging infrastructure
- [ ] Implement backup and disaster recovery
- [ ] Add comprehensive error tracking

## 🎯 Demo Purpose

This application is built for **demonstration purposes** to showcase:

- **Modern Full-Stack Architecture**: Laravel + Vue 3 + Inertia.js integration
- **Enterprise Patterns**: Service layers, repositories, and clean code principles
- **Advanced Frontend Techniques**: Multi-step forms, state management, and real-time UI updates
- **Database Design**: Proper relationships and data modeling for medical systems
- **Testing Best Practices**: Comprehensive test coverage with meaningful assertions

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.