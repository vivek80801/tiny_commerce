# Tiny Commerce

A Laravel-based e-commerce application with product and category management, shopping cart functionality, checkout, order management, invoice generation, and an administrative dashboard.

The project focuses on clean application architecture, reliable order processing, automated testing, and maintainable code. It also includes idempotency protection for critical user actions, email notifications, audit trails, and static code analysis.

## Features

### Product and Category Management

1. Create, view, update, and delete products.
1. Manage product categories.
1. Associate images with products and categories.
1. Search products from the home page.
1. Filter products by price.
1. Seed the database with sample categories, products, and images.

### User Management and Authentication

1. User registration and login.
1. Administrative user management with CRUD operations.
1. Support for identifying administrator accounts through an is_admin attribute.
1. Allow administrators to create other administrator accounts through the admin panel.
1. Send a welcome email when a user registers.

### Shopping Cart

1. Add products to the shopping cart.
1. Transfer cart contents when necessary.
1. Optimize cart transfer queries, including scenarios where a cart does not exist.
1. Use database transactions in cart operations to improve data consistency.
1. Protect add-to-cart operations with idempotency keys to prevent unintended duplicate actions.

### Checkout and Orders

1. Allow authenticated users to purchase products online.
1. Select an existing address during checkout.
1. Create a new address during checkout.
1. Create and manage orders.
1. Allow administrators to create orders.
1. Display order details in the administrative interface.
1. Send email notifications when an order is created.
1. Use dedicated cart and checkout services to keep business logic organized.

### Invoice Management

1. Generate invoices when orders are created.
1. Generate invoices for existing orders that do not yet have one.
1. Generate invoices for multiple orders in bulk.

### Administration and Reporting

1. Administrative dashboard.
1. User chart for visualizing user-related information.
1. Audit trails for tracking relevant application activities.
1. Administrative access control.

### Reliability and Code Quality
1. Idempotency keys for selected user actions, registration, cart operations, and purchase flows.
1. Database transactions for cart operations.
1. Automated tests for controllers, services, resources, and models.
1. Larastan for static analysis.
1. Laravel Pint for code formatting.
1. Reduced database query counts on user-facing pages.

## Technology Stack

### The project uses the following tools and technologies:

1. Laravel — application framework.
1. PHP — server-side programming language.
1. Filament — administrative interface.
1. Larastan — static analysis.
1. Laravel Pint — code formatting.
1. PHPUnit or the project's configured test runner — automated testing.


## Testing

### The project includes automated tests covering important application behavior.

#### Current test coverage includes:

1. Controller feature tests.
1. Cart service tests.
1. Model tests.
1. Product resource tests.
1. User resource tests.
1. Order resource tests.

Run the test suite using:

```sh
    php artisan test
```



Alternatively, if the project is configured to use PHPUnit directly:
```sh
vendor/bin/phpunit
```


Use a dedicated testing database or the test environment configured for the project. Avoid running tests against production data.

## Code Quality

Laravel Pint

Format the codebase using Laravel Pint:

```sh
vendor/bin/pint
```

To check formatting without modifying files:

```sh
vendor/bin/pint --test
```

Larastan

Run static analysis using the project's installed Larastan configuration:

```sh
vendor/bin/phpstan analyse
```

If the repository defines a dedicated analysis configuration or Composer script, use that configuration instead.

## Application Architecture

The application separates responsibilities across controllers, services, models, resources, and administrative interfaces.

### Controllers

Controllers handle HTTP requests and coordinate application operations. The checkout and cart controllers have been refactored to delegate business logic to dedicated services.

### Services

CartService — encapsulates shopping cart operations and uses database transactions where appropriate.
CheckoutService — organizes checkout-related business logic.

### Models

Models represent application entities and their relationships, including users, products, categories, carts, and orders.

### Resources

Resource classes provide structured representations of application data. Product, user, and order resources have dedicated automated tests.

### Administrative Interface

The administrative interface supports user, product, category, and order management, along with invoice operations and dashboard reporting.

### Reliability and Idempotency

1. Idempotency keys are used in selected operations to reduce the risk of duplicate processing when a request is repeated.
1. This is particularly important for registration, adding items to a cart, and purchase-related actions.
1. The implementation has also been refined to address idempotency-key issues affecting registration and the Buy Now flow.
1. Database transactions are used in cart operations to help maintain data consistency when multiple related changes must succeed or fail together.

### Email Notifications

#### The application supports email notifications for key user and order events:

1. Welcome email after user registration.
1. Order creation notification.


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
