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
