# Software Developer Info

**Willekeurig gekozen website gemaakt door student:**
![Screenshot van willekeurige website](.github/screenshot-student.png)

**Beheer paneel waar docenten sites uploaden:**
![Screenshot van docent beher paneel](.github/screenshot-manage.png)

## Getting Started

* **TODO**
* `composer install`
* `npm install`
* `php artisan storage:link`
* `php artisan migrate --seed`
* `npm run watch`
* `php artisan serve`

## Notes:

### cURL error 60: SSL certificate expired

To test locally it can be useful to change line `28` in `/vendor/studiokaa/amoclient/src/AmoclientController.php` to `$http = new \GuzzleHttp\Client(['curl' => [CURLOPT_SSL_VERIFYPEER => false]]);`
