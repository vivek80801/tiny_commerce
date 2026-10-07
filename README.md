# Tiny Commerce

A lightweight e-commerce application built with php 8.3, laravel 13 and filament 5. 

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

git clone https://github.com/vivek80801/tiny_commerce.git && cd tiny_commerce
```
```sh
# In Case if you  are forking the repo
# git clone https://github.com/your_user_name/tiny_commerce.git && cd tiny_commerce

```
```sh
cp .env.example .env

```

```sh
composer install && npm install
```

```sh
npm run build
```

```sh
mkdir -p storage/app/public/uploads
```

```sh
php artisan storage:link
```

```sh
php artisan migrate:fresh --seed
```

```sh
composer dump-autoload
```

```sh
php artisan serve

```

                        OR

```sh

git clone https://github.com/vivek80801/tiny_commerce.git && cd tiny_commerce

# In Case if you  are forking the repo
# git clone https://github.com/your_user_name/tiny_commerce.git && cd tiny_commerce

cp .env.example .env
composer install && npm install
npm run build
mkdir -p storage/app/public/uploads
php artisan storage:link
php artisan migrate:fresh --seed
composer dump-autoload
php artisan serve


```
                    OR
```sh

git clone https://github.com/vivek80801/tiny_commerce.git && \\
cd tiny_commerce && \\
cp .env.example .env && \\
composer install && npm install && \\
npm run build && \\
mkdir -p storage/app/public/uploads && \\
php artisan storage:link && \\
php artisan migrate:fresh --seed && \\
composer dump-autoload && \\
php artisan serve

```

## Workflow
if you are not using tmux. You can define your own aliases as well

```sh
# These are not required to run the project

cp alias_example.sh alias.sh
source alias.sh
```

If you have tmux installed

```sh
source tmux.sh

# for shell where alias is not available. You know. If you need just in case.
# or better modify tmux.sh with your liking. 
source alias.sh
```

Check alias.sh for aliases. You can add or remove it based on your liking.

## Background Services

This application uses a scheduled job to clean up temporary guest carts every minute. Run these two commands in separate terminal sessions

```sh
php artisan schedule:work
```
```sh
php artisan queue:work
```
                        OR

```sh
php artisan schedule:work
php artisan queue:work
```


You can adjust the schedule frequency in `routes/console.php`:

```php
Schedule::job(new CleanTmpCarts)->everyMinute();
```
