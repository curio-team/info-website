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
* Fill `TESTER_ACCESS_USER` and `TESTER_ACCESS_PASSWORD` with any combination to protect the test-page from bots/outsiders.
* To run the queue that removes tester sites:
    * Locally for development use: `php artisan schedule:work`
    * On production set this CRON task: `* * * * * cd /path-to-your-project && php artisan queue:work --stop-when-empty >> /dev/null 2>&1` (NOTE: untested, but should work...)

## Notes:

### cURL error 60: SSL certificate expired

To test locally it can be useful to change line `28` in `/vendor/studiokaa/amoclient/src/AmoclientController.php` to `$http = new \GuzzleHttp\Client(['curl' => [CURLOPT_SSL_VERIFYPEER => false]]);`
