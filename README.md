# Glick API

## Demo

View the app at https://glick.jennybelanger.com/

## Development setup

### Install requirements

- [Composer](https://getcomposer.org/)

You'll also need a database and web server with PHP to serve the API and app. Maybe one day, I'll add a Dockerfile to this repo, but probably not, because no one is ever going to set this up except me.

### Clone the API repo

``` bash
git clone https://github.com/jlbelanger/glick-api.git
cd glick-api
```

All other commands should be run in the `glick-api` folder.

### Configure environment settings

``` bash
cp .env.example .env
```

### Install dependencies

``` bash
composer install
```

### Run database migrations

``` bash
php artisan migrate
php artisan db:seed
```

### Setup the app

See [Glick app](https://github.com/jlbelanger/glick-app).

## Deployment

Essentially, to set up the repo on the server:

``` bash
git clone https://github.com/jlbelanger/glick-api.git
cd glick-api
cp .env.example .env
# Then configure the values in .env.
composer install
```

For subsequent deploys, push changes to master, then run the following on the server:

``` bash
cd glick-api
git fetch origin
git pull
composer install
php artisan config:clear
```

### Deploy script

Note: The deploy script included in this repo depends on other scripts that only exist in my private repos. If you want to deploy this repo, you'll have to create your own script.

``` bash
./deploy.sh
```

## Helpful development stuff

### Credentials

- **Username**: demo
- **Password**: demo

### Lint

``` bash
./vendor/bin/phpcs
```

### Test

``` bash
./vendor/bin/phpunit
```
