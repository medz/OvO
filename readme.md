<img align="right" width="100px" src="public/fans.svg" alt="Fans Logo">

# Fans 2

Dragonfly fly on the water, Socialize with Fans!

## Installation

Cloned code:

```sh
git clone https://github.com/medz/fans
```

Install dependents

```sh
composer install
```

Copy environment variable file

```sh
cat .env.example > .env
```

Configuring environment variable files.

Generate an application key

```sh
php artisan key:generate
php artisan jwt:secret
```

Publish assets

```sh
php artisan vendor:publish
```

Create a soft connection symbol

```sh
php artisan storage:link
```

Run data table migration

```sh
php artisan migrate --seed
```

Create founder

```sh
php artisan db:create:founder
```

