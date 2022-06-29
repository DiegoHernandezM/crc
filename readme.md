# Requisitos

nginx
redis
composer
mariadb

## Installation

Clone the repo locally:

```sh
git clone https://github.com/DiegoHernandezM/crc.git
cd crc
```

Install PHP dependencies:

```sh
composer install
```

Setup configuration:

```sh
cp .env.example .env
```

Generate application key:

```sh
php artisan key:generate
```

Create an MariaDB database. You can also use another database (MySQL, etc), simply update your configuration accordingly.

Run database migrations:

```sh
php artisan migrate
```

Run database seeder:

```sh
php artisan db:seed
```

Run:

```sh
configure your nginx to repo
```

## Credits

- TEAM OMS Cuidado con el perro
- Solo para fies de cosultar codigo
