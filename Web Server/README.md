# Web Server

## Overview

This section contains the web server portion of a Laravel application designed to make a health assessment tool.

## System Requirements

Before running this application, ensure that your system meets the following requirements:

- PHP >= 8.2
- Other Laravel system requirements, such as Composer, web server, etc. which you can find at https://laravel.com/docs/11.x/releases

## Installation

1. Clone the repository to your local machine:

   ```bash
   git clone https://github.com/your-username/Personalized-Health-Assessment-Through-Interactive-AI.git
   ```

2. Navigate to the Web Server Directory:
   ```bash
   cd webserver
   ```
3. Install the required PHP dependencies using Composer:
   ```bash
   composer install
   ```
4. Install the GeoIP2 package:
   ```bash
   composer require geoip2/geoip2:~2.0
   ```
5. Obtain the MaxMind GeoIP database from their website https://www.maxmind.com/en/geoip-databases.
6. Install the Laravel Guzzle HTTP package for making API requests:
   ```bash
   composer require guzzlehttp/guzzle
   ```

# Usage
Run the Laravel application:
  ```bash
  php artisan serve
  ```
Access the application in your web browser at http://localhost:8000.
<br>
<b>Make sure that the API server is up and running, and ensure that you are pointing to the correct API server endpoint.</b>

# Additional Notes
This section contains only the web server portion of the project. To utilize the full system, including the API server, please refer to the README.md file in the api server folder for instructions on setting it up and integrating it with this Laravel application.

# License
This project is licensed under the Apache License 2.0 - see the LICENSE file for details.
