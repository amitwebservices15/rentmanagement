# Rent Management System

A Laravel-based Rent Management System for property owners to manage:

- Properties
- Rooms
- Tenants
- Rent Collection
- Electricity Bills
- Monthly Rent Slips
- WhatsApp Notifications

## Requirements

- PHP 8.2+
- Laravel 12
- MySQL

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```