# Symfony3 Exception Report Bundle

This bundle will autoload an exception handler into the framework, so that all uncaught errors are sent by email to the developer. No external services required. Just produces bloody simple exception reports including important client and request metadata.

This bundle was inspired by [PhpAirbrakeBundle][1].

## Requirements

 - PHP 5.6 or above
 - Symfony 3.0 or above (Symfony2 might work as well)
 - [Symfony Swiftmailer Bundle][2] (included by composer)
 - [MaxMind GeoIP Symfony2 Bundle][3] (included by composer)

## Installation

### Install with Composer

```bash
$ composer require onlime/exception-report-bundle
```

Update GeoIP database:

```bash
$ bin/console maxmind:geoip:update-data http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz
```

## Configuration

Enable bundle in your AppKernel.php:

```php
# app/AppKernel.php
<?php
    // ...
    public function registerBundles()
    {
        $bundles = array(
        	// ...
            new Onlime\ExceptionReportBundle\OnlimeExceptionReportBundle(),
        );
	}
```

Configure bundle in config.yml:

```yaml
# app/config/config.yml

onlime_exception_report:
    handlers:
        swift:
            # by default 4xx http exceptions are not reported:
            # exclude_http: true
            #
            # to also log 4xx level errors (but not 404's):
            # exclude_http: false
            # excluded_404s:
            #     - ^/
            #
            # to hide stacktrace output for specific exceptions,
            # add them with fully qualified class name:
            # no_stacktrace_on_exceptions:
            #     - Doctrine\DBAL\Exception\ConnectionException
            #     - PDOException
            #
            exclude_http: false
            from_email: '%email.admin%'
            to_email:   '%email.admin%'
            subject:    "[Example.com] Exception Report"
```

You might disable specific handlers on dev environment:

```yaml
# app/config/config_dev.yml

onlime_exception_report:
    handlers:
        swift:
            enabled: false
```

### Default configuration reference


```yaml
# Default configuration for extension with alias: "onlime_exception_report"
onlime_exception_report:
    handlers:

        # Prototype
        name:
            enabled:              true
            exclude_http:         true
            excluded_404s:        []
            no_stacktrace_on_exceptions:  []
            from_email:           ~ # Required
            to_email:             [] # Required
            subject:              'Exception Report'
```

This default configuration reference was generated with the following command:

```bash
$ bin/console config:dump-reference onlime_exception_report
```

## Sample Report

Simply throw an exception in one of your controllers, e.g.:

```php
<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/demo")
 */
class DemoController extends Controller
{
    /**
     * Simply throws an Exception to test exception reporting.
     *
     * @Route("/exception", name="demo_exception")
     */
    public function exceptionAction()
    {
        throw new \Exception('Something really bad happened!');
    }
}
```

You will then find the exception report in the web debug toolbar or (on a correctly configured Swift mailer) in your mailbox:

```
GET /demo/exception: Something really bad happened!
Exception: Exception

Date:       Sun, 28 Feb 2016 22:15:47 +0100
ClientIP:   212.51.128.110 (CH / Switzerland)
ClientHost: 212-51-128-110.fiber7.init7.net
ReqHost:    demo.example.com
ReqUri:     http://demo.example.com/demo/exception
Env:        prod
Route:      demo_exception
User:       onlime
Agent:      Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:49.0) Gecko/20100101 Firefox/49.0
Referer:

------------------------------------------------------------
Stack trace
------------------------------------------------------------
#0 [internal function]: AppBundle\Controller\DemoController->exceptionAction()
#1 vendor/symfony/symfony/src/Symfony/Component/HttpKernel/HttpKernel.php(139): call_user_func_array(Array, Array)
#2 vendor/symfony/symfony/src/Symfony/Component/HttpKernel/HttpKernel.php(62): Symfony\Component\HttpKernel\HttpKernel->handleRaw(Object(Symfony\Component\HttpFoundation\Request), 1)
#3 vendor/symfony/symfony/src/Symfony/Component/HttpKernel/Kernel.php(169): Symfony\Component\HttpKernel\HttpKernel->handle(Object(Symfony\Component\HttpFoundation\Request), 1, true)
#4 web/app.php(58): Symfony\Component\HttpKernel\Kernel->handle(Object(Symfony\Component\HttpFoundation\Request))
#5 {main}

------------------------------------------------------------
Request Parameters
------------------------------------------------------------

POST Array
(
)


GET Array
(
)
```

## Special Use Cases

### Trigger Exception report without breaking application

Let's say you have caught an exception (e.g. from an API request) and do not wish to break the application flow, but still want to report the complete exception. That's what the `ExceptionEvent` is for - simply trigger it in your controller like this:

```php
<?php
use Onlime\ExceptionReportBundle\Event\ExceptionEvent;
use Onlime\ExceptionReportBundle\Event\ExceptionEvents;

// ...

        try {
            // do some dangerous stuff here...
        } catch (\Exception $e) {
            $this->get('event_dispatcher')->dispatch(
                ExceptionEvents::REPORT,
                new ExceptionEvent($e)
            );
        }
```

## Authors

 - Philip Iezzi (Twitter [@fifbear][4])

Copyright (c) 2016 Onlime Webhosting [www.onlime.ch][5] (Twitter [@ondalime][6])

## License

This bundle is released under the [MIT license](Resources/meta/LICENSE).

## Credits

This bundle is both inspired by and is using some of the code from [ibrows/PhpAirbrakeBundle][1] which is a fork of [dbtlr/PhpAirbrakeBundle][7].

I would also like to thank [Mike Meier][8] for his advice.


[1]: https://github.com/ibrows/PhpAirbrakeBundle
[2]: https://github.com/symfony/swiftmailer-bundle
[3]: https://github.com/IDCI-Consulting/Maxmind-GeoIp
[4]: https://twitter.com/fifbear
[5]: https://www.onlime.ch
[6]: https://twitter.com/ondalime
[7]: https://github.com/dbtlr/PhpAirbrakeBundle
[8]: https://github.com/mikemeier
