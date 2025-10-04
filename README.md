# Laravel 12 API Boilerplate

A **clean, production-ready starter kit for Laravel 12 APIs**, built for scalability, maintainability, and fast development. Includes authentication, file handling, global exception handling, and popular integrations like Telescope, Sentry, and Cloudinary.

Focus on building business logic, not boilerplate.

---

## **Why Use This Starter Kit**

Developing APIs from scratch is repetitive and error-prone. This starter kit provides:

-   Standardized API responses via **ApiResponse / ResponseTrait**
-   Token-based authentication using **Laravel Sanctum**
-   File uploads (local and Cloudinary, S3 will be implemented later)
-   Global exception handling for API endpoints
-   Rate limiting, CORS, and validation middleware
-   Ready-to-use feature tests
-   Popular integrations: **Telescope**, **Sentry**, **Cloudinary**

---

## **Features / Extensions**

### **1. Routing & Middleware**

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->appendToGroup('api', [
            HandleCors::class,
            EnsureAcceptJsonMiddleware::class,
        ]);
    });
```

-   CORS handling
-   Accept JSON enforcement for API routes

---

### **2. Global Exception Handling**

Handles API-specific exceptions with standardized responses, including the custom **BusinessRuleException**:

-   404 Not Found
-   405 Method Not Allowed
-   401 Unauthenticated
-   403 Unauthorized
-   429 Too Many Requests
-   **BusinessRuleException** – Custom exceptions for business logic with optional detailed errors
-   Validation errors
-   Catch-all exceptions with environment-aware messages

Example of throwing a business rule exception in your service:

```php
use App\Exceptions\BusinessRuleException;

if ($someConditionFails) {
    throw new BusinessRuleException(
        'Cannot process this action due to business rules.',
        ['field_name' => 'Detailed reason or validation info']
    );
}
```

API Response:

```json
{
    "success": false,
    "message": "Cannot process this action due to business rules.",
    "errors": {
        "field_name": "Detailed reason or validation info"
    }
}
```

---

### **3. Traits**

#### **ApiResponse / ResponseTrait**

Centralized standardized API responses for success, error, pagination, and created resources.

#### **FileTrait**

-   Local file uploads
-   Cloudinary image, video, and PDF uploads
-   Multiple file uploads
-   Automatic MIME type handling

#### **HasCreator**

-   Automatically assigns `user_id` on model creation
-   Relationship to `creator` for tracking ownership

---

### **4. Integrations**

-   **Laravel Telescope** – Debugging & monitoring

    ```env
    TELESCOPE_ENABLED=true
    TELESCOPE_PATH=telescope
    ```

    Scheduled pruning:

    ```php
    Schedule::command('telescope:prune')->daily();
    ```

-   **Sentry** – Error tracking

    ```env
    SENTRY_LARAVEL_DSN=
    SENTRY_TRACES_SAMPLE_RATE=1.0
    ```

-   **Cloudinary** – File & media uploads

    ```env
    CLOUDINARY_NOTIFICATION_URL=
    CLOUDINARY_UPLOAD_PRESET=
    CLOUDINARY_URL=cloudinary://...
    ```

---

### **5. Packages**

-   **tijanidevit/query-filter**: `"^0.02.0"` – Flexible query filtering

---

### **6. Configurable Environment Variables**

```env
PAGINATE_COUNT=20
FRONTEND_URL=http://localhost:3000
TELESCOPE_ENABLED=true
TELESCOPE_PATH=debug-path
CLOUDINARY_URL=cloudinary://...
SENTRY_LARAVEL_DSN=
SENTRY_TRACES_SAMPLE_RATE=1.0
```

---

### **Usage in `config/app.php`**

You can access environment variables via `env()` in your configuration files. For example, in `config/app.php`:

```php
return [

    'frontend_url' => env('FRONTEND_URL'),

    'pagination_count' => env('PAGINATE_COUNT'),

    // Other config...
];
```

---

### **How to Use in Your Code**

You can now access these config values anywhere in your Laravel application using `config()`:

```php
$frontendUrl = config('app.frontend_url'); // returns http://localhost:3000
$perPage = config('app.pagination_count'); // returns 20
```

This makes it easy to keep configuration centralized and changeable without touching your code.

---

## **Sample Authentication**

### Routes

**Public Routes** (`auth.` group):

| Method | Endpoint      | Description         |
| ------ | ------------- | ------------------- |
| POST   | /api/register | Register a new user |
| POST   | /api/login    | Login and get token |

**Protected Routes** (`auth:sanctum` middleware)

| Method | Endpoint    | Description           |
| ------ | ----------- | --------------------- |
| POST   | /api/logout | Logout current user   |
| GET    | /api/me     | Get current user info |

---

### Example: Register User

```bash
POST /api/register
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

### Example: Login

```bash
POST /api/login
{
    "email": "john@example.com",
    "password": "password"
}
```

Response:

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
        "token": "sanctum-token-string"
    }
}
```

---

## **Testing**

-   Feature tests for authentication and API responses
-   Uses **in-memory SQLite database** for fast, isolated tests

Run tests:

```bash
php artisan test
```

---

## **Installation**

```bash
git clone https://github.com/tijanidevit/laravel-api-boilerplate.git
cd laravel-api-boilerplate
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

---

## **Planned Extensions**

-   Role-based access control
-   Rate limiting per user
-   Enhanced logging with Sentry
-   API versioning support
-   Additional resource endpoints
-   Auth notifications
-   Password reset
