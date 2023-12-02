# Glick API

[View the site](https://glick.jennybelanger.com/).

## Development

### Requirements

- [Composer](https://getcomposer.org/)
- [Git](https://git-scm.com/)
- Database
- Web server with PHP

### Setup

``` bash
# Clone the API repo
git clone https://github.com/jlbelanger/glick-api.git
cd glick-api

# Configure the environment settings
cp .env.example .env

# Install dependencies
composer install

# Generate key
php artisan key:generate

# Run database migrations
php artisan migrate
php artisan db:seed

# Set permissions
chown -R www-data:www-data storage

# Create account with username "test" and password "password" (or reset existing account password to "password")
php artisan reset-auth
```

Then, setup the [Glick app](https://github.com/jlbelanger/glick-app).

### Lint

``` bash
./vendor/bin/phpcs
```

### Test

``` bash
./vendor/bin/phpunit
```

## Deployment

Essentially, to set up the repo on the server:

``` bash
git clone https://github.com/jlbelanger/glick-api.git
cd glick-api
cp .env.example .env
# Then configure the values in .env.
composer install
php artisan key:generate
php artisan migrate
chown -R www-data:www-data storage
```

For subsequent deploys, push changes to the main branch, then run the following on the server:

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
