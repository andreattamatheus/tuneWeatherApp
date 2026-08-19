# The Weather App

The Weather App provides users with accurate, up-to-date weather information. Users can search for the current weather and a 5-day forecast for any city worldwide. The app integrates with the OpenWeatherMap API to fetch weather data.

<p align="center">
  <img src="./public/image-readme.png" alt="Presentation" />
</p>

## Tech Stack

<img src="https://skillicons.dev/icons?i=html,css,javascript,typescript,docker,git,laravel,vue,mysql,php,tailwindcss" width="415px" alt="Technologies" />

- PHP 8.2, Laravel 11
- Vue.js frontend (inside `resources/js/app`)
- MySQL
- Laravel Sanctum (auth), queues (scheduled jobs)
- OpenWeatherMap API integration

## Features

- Real-time weather updates
- 5-day weather forecast
- Search for any city worldwide
- Scheduled job to purge locations older than 3 days
- Responsive UI

## Prerequisites

- PHP 8.2
- Composer
- Node 16.10+, npm 6.14+, Yarn 1.22+
- MySQL

## Installation

```sh
git clone https://github.com/andreattamatheus/weatherApp
cd weatherApp
composer install
```

Copy `.env.example` to `.env` and fill in your database and API credentials:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=YOUR_DATABASE
DB_USERNAME=YOUR_USERNAME
DB_PASSWORD=YOUR_PASSWORD

OPENWEATHER_API_KEY=your_key_from_openweathermap
OPENWEATHER_API_URL=https://api.openweathermap.org/data/2.5
```

```sh
php artisan optimize
php artisan migrate:fresh --seed
php artisan serve
```

Seeding creates two users: `backoffice@yopmail.com` and `admin@yopmail.com`, both with password `123123123`.

## Running the Frontend

Frontend lives in `resources/js/app`. Add the API URL to `.env` (e.g. `http://localhost:8000/api/`).

```sh
yarn
yarn run serve      # dev
yarn run build      # production build
yarn run lint        # lint & fix
```

The frontend has two pages: `/login` and `/home`.

## Background Jobs

```sh
php artisan schedule:list   # list scheduled jobs
php artisan schedule:run    # run the location cleanup job
php artisan queue:listen    # process queued jobs
```

## Quality & Tests

```sh
./vendor/bin/pint          # code style
./vendor/bin/phpstan analyse
php artisan test
```

## Contact

Matheus Andreatta — [@andreattamatheus](https://github.com/andreattamatheus)
