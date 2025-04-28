# Psychology Tools - Blog Test

## Features

- User authentication
- Post creation and management
- Like/unlike posts
- Author listing
- Search and filter posts
- Sort posts by title and date

## Tech Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Pest for testing
- Laravel Herd for local development
- TablePlus for database management

## Installation

1. Clone the repository:
```bash
git clone https://github.com/cristian-sudo/psychology-tools.git
cd psychology-tools
```

2. Install dependencies:
```bash
composer install
```

3. Create and configure your `.env` file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Run migrations:
```bash
php artisan migrate
```

6. Start the development server:
```bash
php artisan serve
```
Or if you use Herd make sure the project is parked.

## Development Tools

### Laravel Herd
I use Laravel Herd for local development. It provides a fast and easy way to run Laravel applications locally.

### TablePlus
TablePlus is used for database management. It offers a clean interface to manage your MySQL database.

### Makefile
The project includes a Makefile for common tasks:
```bash
make test    # Run Pest tests
```

## Testing

The project uses Pest for testing. Some of the tests are listed bellow:
- Post creation and validation
- Like/unlike functionality
- Author listing
- Search and filtering

The tests were developed using TDD (Test-Driven Development) approach, which took approximately 4 hours to complete.

Thank you once again for this opportunity. I look forward to the next steps in the process.


