## About Sunrise API

Sunrise API is a Laravel 9 RESTFul starter kit for SPA and mobile clients. This kit includes the following implementation and features:
- Token-based Authentication with [Sanctum](https://laravel.com/docs/9.x/sanctum)
- Role-based Access Control with [Spatie](https://spatie.be/docs/laravel-permission/v5/introduction)
- Admin CRUD actions for users with profile picture upload
- User profile management with profile picture upload
- Forgot and Reset Password with Email Notification
- AWS S3-bucket pre-signed URL implementation
- Composer and Git hooks automation: code auto-format, package security check, etc.
- Pipeline implementation for HTTP query filters
- Repository pattern implementation
- International mobile and line number format check
- Unit and Feature tests

## Set up your local development environment
- Minimum of PHP 8.1 installed and a relational database (e.g. MySQL8, MariaDB 10.5)
- Create a **.env** file from the **.env.example** that came with this project
- In the **.env** file, update the **APP_NAME**, **APP_URL**, the **DB_** variables, and the MAIL variables if you decide to use a different test mailing service or account. 
- You may also change the **SPA_RESET_PASSWORD_URL** if you're spinning a different URL for your SPA.
- Locate your **php.ini** file and change the value **upload_max_filesize** to **8M**. See this [guide](https://devanswers.co/ubuntu-php-php-ini-configuration-file/) if you're having trouble finding the directory of your php.ini file
- Run the command `composer install`  to install all the project and dev dependencies
- Run the command `php artisan app:init` to set up the project. This will run app key generation, migrations, seeders, etc. 
- You may now run `php artisan serve` to serve the API locally. Use [Postman](https://www.postman.com/downloads/) and this [documentation](https://google.com) for testing the endpoints

## Tools ready for you
- `php artisan app:styler -i` Runs a [code styler](https://github.com/stechstudio/Laravel-PHP-CS-Fixer) for consistency and generate [IDE helper PHP Docs](https://github.com/barryvdh/laravel-ide-helper). See the command at `app/Console/Commands/StyleFixer.php`
- `php artisan user:create` Create a user with role. See the command at `app/Console/Commands/CreateUser.php`
- Running `composer install`, `composer update`, `git commit` will trigger automated tasks specified in `grumphp.yml`
  - PSR-compliant code auto-format
  - Package security checks whenever a package is added or updated in composer.json
  - Unit and Feature tests
- [Clockwork](https://underground.works/clockwork) is installed for local development. Install the browser extension from their website
- This project comes with a [Github PR template](https://docs.github.com/en/communities/using-templates-to-encourage-useful-issues-and-pull-requests/about-issue-and-pull-request-templates) inside `docs/`

## Style Guide Ver. 0.1
- Use **FormRequest** validators when available
- Favor single quotes over double quotes
- Extend the **ApiController** for all your API controllers
- Use `snake_case` for DB table columns, request inputs, and resource views
- Use `PascalCase` for class names
- User `camelCase` for variable, method, and function names
- Create separate API route files per resource/feature. Load all of them in `routes/api.php`
- Follow and implement the [PHPDoc](https://docs.phpdoc.org/3.0/guide/guides/docblocks.html) style guide
- Use `app\Exceptions\Handler.php` for centralized error handling

## Note
- All dates and timestamps are stored and compared in UTC timezone. Clients must convert their local dates to UTC before submitting to the API

## Authors
- Jego Carlo Ramos (JegRamos)
