# Laravel 12 + Vue.js Full SPA Starter

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

## Table of Contents

-   [Requirements](#requirements)
-   [Installation](#installation)
-   [Setup Database & Seeder](#setup-database--seeder)
-   [Job Queue](#job-queue)
-   [Vue.js SPA](#vuejs-spa)
-   [Passport / OAuth Token](#passport--oauth-token)
-   [Unit Testing](#unit-testing)
-   [License](#license)

---

## Requirements

-   PHP >= 8.2
-   Composer
-   Node.js >= 20 & npm/yarn
-   MySQL / MariaDB
-   Redis (optional for queue)

---

## Installation

1. **Clone repository**

```bash
git clone https://github.com/username/laravel-vue-spa.git
cd laravel-vue-spa
```

2. **Install PHP dependencies**

```bash
composer install
```

3. **Install Node dependencies**

```bash
npm install
# or yarn
```

4. **Copy `.env` file**

```bash
cp .env.example .env
```

5. **Generate app key**

```bash
php artisan key:generate
```

6. **Configure `.env`**

```dotenv
APP_NAME=LaravelSPA
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_vue
DB_USERNAME=root
DB_PASSWORD=secret

QUEUE_CONNECTION=database
MAIL_MAILER=log
```

---

## Setup Database & Seeder

1. **Install Passport**

```bash
composer require laravel/passport
php artisan migrate
php artisan passport:install
```

2. **Create sample seeder**

`database/seeders/AdminUserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_verified' => true,
        ]);

        User::factory(10)->create();
    }
}
```

3. **Run seeder**

```bash
php artisan db:seed --class=AdminUserSeeder
```

---

## Job Queue

1. **Create a job**

```bash
php artisan make:job SendVerifyEmailJob
```

`app/Jobs/SendVerifyEmailJob.php`:

```php
<?php

namespace App\Jobs;

use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendVerifyEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        Mail::to($this->user->email)->send(new VerifyEmail($this->user));
    }
}
```

2. **Start queue worker**

```bash
php artisan queue:work
```

3. **Dispatch job**

```php
SendVerifyEmailJob::dispatch($user);
```

---

## Vue.js SPA

1. **Compile assets**

```bash
npm run dev
# or production build
npm run build
```

2. **Serve Laravel**

```bash
php artisan serve
```

3. **Access SPA**

```
http://localhost:8000
```

---

## Unit Testing

1. **Example: Auth API Test**

`tests/Feature/AuthTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected $password = 'password123';

    /** @test */
    public function register_success()
    {
        $payload = [
            'role' => 'partner',
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password' => $this->password
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'code',
                     'result' => ['id', 'email', 'isVerified', 'pin']
                 ]);
    }

    /** @test */
    public function login_success()
    {
        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => bcrypt($this->password),
            'is_verified' => true
        ]);

        Passport::actingAs($user); // bypass auth token

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'code',
                     'result' => ['id', 'email', 'role']
                 ]);
    }
}
```

2. **Run tests**

```bash
php artisan test
```

---

## License

MIT
