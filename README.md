# tTable
## Public transport time schedules - API

1. About
This project is an API for tTable - a public transport time schedules.
It uses:
  * [Composer](https://getcomposer.org/) - dependency menager
  * [Slim](http://www.slimframework.com/) - PHP micro framework
  * [RedBeanPHP](http://redbeanphp.com) - easy to use **ORM**

2. Configuration - file `config.php`
**tTable** uses MySQL/MariaDB as database engine
  * `DB_HOST` - database host
  * `DB_USER` - database username
  * `DB_PASS` - database password
  * `DB_BASE` - database name

3. API routes
  * GET `/lines` - gets and displays JSON with lines and valid dates
  * GET `/lines/:line` - gets and displays JSON with selected line directions and stops for these directions
  * GET `/stops` - gets and displays JSON with stops
  * GET `/stops/:stop` - gets and displays JSON with chronological departures from chosen stop
  * GET `/stops/all/:stop` - EXPERIMENTAL
  * GET `/trip/:line/:direction/:day/:trip` - gets and displays JSON with trip for chosen line, directions, daytype and trip number
  * GET `/departures/:line/:direction/:stop` - gets and displays JSON with departures for chosen line, direction and stop ID  