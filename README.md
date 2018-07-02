# TC Gousto

## About me
Alexander Cheprasov
- email: acheprasov84@gmail.com
- phone: +44 7490 216907
- linkedin: https://uk.linkedin.com/in/alexandercheprasov/
- CV: https://cv.cheprasov.com/
- London, UK


## About the test

> The service must be built using a modern web application framework.

I used Laravel framework for the test. 

> Your reasons for your choice of web application framework.

I have not worked with Laravel before, but I know that Laravel is used by Gousto company, and for me it was a good point to try it.

> The code should be ‘production ready’ and maintainable

Done. But please note, I have made only required functional without any authorization, tokens and other thing for initial API.

> The service should use the accompanying CSV (in the zip you downloaded) as the primary data source, which can be loaded into memory (please don't use a database, though SQLite would be acceptable). Feel free to generate additional test data based on the same scheme if it helps.

I used SQLite & memcached

> Explain how your solution would cater for different API consumers that require different recipe data e.g. a mobile app and the front-end of a website

It is not required for implementation by the tech test. Anyway, the solution depends on a lot of factors like flexibility, frequencies of releases and deploys, rate of API changes, difference between mobile app and so on.

In some cases, we can use approach based on versioning of API, like `/v1/` or `/v2/`, and it works fine.

But I prefer more flexible approach based on feature flags. For example, a web or mobile client sends some flags via initial request and informs to a server that it supports some features, and based on this information the server constructs correct response with different recipe data for each client.

We used this approach in Badoo company, and it works really very nice, and helps clients to be more independent and flexible. You can read more about it here: https://badootech.badoo.com/crazy-agile-api-5130be6f5b06

> Anything else you think is relevant to your solution

I used standard pagination in Laravel. It uses `OFFSET` and `LIMIT` sql lexemes, but it is much better to use pagination based on `LAST_FOUND_ID` and `LIMIT`. It help to select data more faster from database like MySQL.

### Tech stack

- PHP >= 7.1
- Laravel
- SQLite
- Memcache
- Nginx

### How to run tests

1. Update vendor via composer: `composer update`
2. Run `./vendor/bin/phpunit`

### How to use the solution

1. Edit and add config file `./nginx.conf` to your nginx server
2. Add domain to `/etc/hosts`, for example `192.168.5.7 api.tc-gousto.lh`
3. Open link `http://api.tc-gousto.lh/recipes` in a browser
4. Change mode for some dirs if need.
