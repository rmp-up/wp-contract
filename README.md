# WordPress contracts

> Helper for PHP (especially WordPress)

## Features

* Rewindable Generators
* Hydrate and exchange protected fields in objects
* System logger compatible logging
  * Using [`syslog()`](https://php.net/syslog)
  * Delegate to [PSR-3 logger](https://www.php-fig.org/psr/psr-3/)
  * Forward as user-errors to [`trigger_error()`](https://php.net/trigger_error)
  * Forward to [WP_CLI](https://make.wordpress.org/cli/)
* Message Bus (delegating to WP_Hook)
  * Decouple exception handling per action/filter
  * Stop propagation

Tested with:

* PHP 7.0 - 7.4
* WordPress >= 4.5
