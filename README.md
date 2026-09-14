# Tiny Commerce

A lightweight e-commerce application built with PHP, Laravel, and Filament. 

## Features
* Product catalog with search and price filtering
* Guest cart system with automatic migration to user carts upon login/registration
* Secure checkout flow with address management
* Filament-powered admin panel for managing products, users, orders, and carts
* Background queue worker and scheduler for cleaning up temporary guest carts

## Requirements
* PHP 8.3
* Composer
* Node.js and npm
* SQLite

## Get Started

```sh

git clone [https://github.com/vivek80801/tiny_commerce.git](https://github.com/vivek80801/tiny_commerce.git) && cd tiny_commerce

cp .env.example .env

composer install && npm install

npm run build

mkdir -p storage/app/public/uploads

php artisan storage:link

php artisan migrate:fresh --seed

composer dump-autoload

php artisan serve

```

## Background Services

This application uses a scheduled job to clean up temporary guest carts every minute. Run these two commands in separate terminal sessions

```sh

php artisan schedule:work

php artisan queue:work
```

You can adjust the schedule frequency in `routes/console.php`:

```php
Schedule::job(new CleanTmpCarts)->everyMinute();
```
