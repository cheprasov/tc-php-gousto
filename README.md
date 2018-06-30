# TC Gousto

## About me
Alexander Cheprasov
- email: acheprasov84@gmail.com
- phone: +44 7490 216907
- linkedin: https://uk.linkedin.com/in/alexandercheprasov/
- CV: https://cv.cheprasov.com/
- London, UK


## About the test

The service must be built using a modern web application framework
The code should be ‘production ready’ and maintainable
The service should use the accompanying CSV (in the zip you downloaded) as the primary data source, which can be loaded into memory (please don't use a database, though SQLite would be acceptable). Feel free to generate additional test data based on the same scheme if it helps.

### Tech

- PHP >= 7.1
- Laravel
- SQLite
- Memcache
- Nginx

### How to run tests

1. Update vendor via composer: `composer update`
2. Run `./vendor/bin/phpunit`

### How to run the application

1. Edit and add config file `./nginx.conf` to your nginx server
2. Add some host to `/etc/hosts`, for example `192.168.5.7 api.tc-gousto.lh`
3. Open in browser `http://api.tc-gousto.lh/recipes`
4. Change mode for some dirs if need.
