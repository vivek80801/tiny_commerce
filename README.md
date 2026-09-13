# Ecommerce website

This is a very tiny ecommerce website source code. It is written in php and laravel. It uses filament for admin panel. 
User can see products, add to carts products and then  checkout. it will autometically create order. admin can manage products, users, orders, carts. as a user you also add address  when you do checkout.

## Requirements

php8.3
composer
node
sqlite

## Get Started

clean the repo

```sh

git clone https://github.com/vivek80801/tiny_commerce.git && cd tiny_commerce

cp .env.example .env

composer install && npm install

mkdir -p storage/app/public/uploads

php artisan storage:link

php artisan migrate:fresh --seed

php artisan serve

```

This application also have  schedular for cleaning up Cart. you would need to run schedular and queue worker both
Run these two commands in two  terminal sessions

```sh
php artisan schedule:work

php artisan queue:work
```
currently, Cart clean up runs every minute. but, you can change that in `routes/console.php`. change this line to your liking

```php
    Schedule::job(new CleanTmpCarts)->everyMinute();
```

