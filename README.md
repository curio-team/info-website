# Software Developer Info

**Willekeurig gekozen website gemaakt door student:**
![Screenshot van willekeurige website](.github/screenshot-student.png)
*(Verouderd screenshot)*

**Beheer paneel waar docenten sites uploaden:**
![Screenshot van docent beher paneel](.github/screenshot-manage.png)
*(Verouderd screenshot)*

## Getting Started

### Local development

* Clone this repository
* Run the following commands in the root of that repo:
    * `composer install`
    * `npm install`
    * Create and configure the `.env` file, note:
        * Fill `SD_CLIENT_ID` and `SD_CLIENT_SECRET` with the correct (secret) app secrets
        * Students can simply temporarily put `->middleware('auth')` in the `routes/web.php` in the comment
    * `php artisan storage:link`
    * `php artisan migrate --seed`
    * `npm run watch`
    * `php artisan serve`

The website is now available for
* Prospects: `https://info.curio.codes/`
* Students who create websites: `https://info.curio.codes/test`
* Teachers: `https://info.curio.codes/beheer`

### Production
* `sudo chown -R www-data:www-data /path/to/this/repo/root`
* Fill `TESTER_ACCESS_USER` and `TESTER_ACCESS_PASSWORD` with any combination to protect the test-page from bots/outsiders. *Optionally use `TESTER_REMOVE_AFTER` in env to change the 5 minute test site lifetime default.*
* To run the queue that removes tester sites:
    * Locally for development use: `php artisan queue:work --stop-when-empty` to run the queue manually (wait 5 minutes after adding test site)
    * On production set this CRON task: `* * * * * cd /path-to-your-project && php artisan queue:work --stop-when-empty >> /dev/null 2>&1` (NOTE: untested, but should work...)
* Configure your apache vhosts file to allow access to uploaded files from inside the sandboxed iframe (some students use JS to include content from their site):
```
<VirtualHost _default_:443>
    ...
    # Ensure this Directory points to the symlink path apache follows (and not the actual storage directory)
    <Directory /var/www/html/info/public/storage/>
            <IfModule mod_headers.c>
                Header set Access-Control-Allow-Origin "*"
            </IfModule>
    </Directory>
    ...
</VirtualHost>
```

## Notes:

### cURL error 60: SSL certificate expired
To test locally without HTTPS set `SD_SSL_VERIFYPEER=no` in the `.env` file.

### Testing
The directory `tests/TestData/` contains some websites that can be used to test.
