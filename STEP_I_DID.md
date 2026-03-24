## Backend

Ran Sail
`sail up -d`

Installed Filament
`sail composer require filament/filament:"^5.0"`

Installed Panels
`sail artisan filament:install --panels`


### Install API

`sail artisan install:api`

### Install Spatie Permissions

`sail composer require spatie/laravel-permission`

Read docs: `https://spatie.be/docs/laravel-permission/v7/installation-laravel`


Ran migration:

`sail artisan migrate`


Install Horizon

`sail composer require laravel/horizon`

(configuration of workers required)


Install Telescope

`sail composer require laravel/telescope`

(Pending)

Install Laravel Boost
`sail composer require laravel/boost --dev`

Install Broadcasting (for reverb)

`sail php artisan install:broadcasting`

---
