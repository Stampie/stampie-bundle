# StampieBundle

[![CI](https://github.com/Stampie/stampie-bundle/actions/workflows/ci.yaml/badge.svg)](https://github.com/Stampie/stampie-bundle/actions/workflows/ci.yaml)

Integrates [Stampie](https://github.com/Stampie/Stampie) with Symfony.

```bash
$ composer require stampie/stampie-bundle
```

## Usage

Add `Stampie\StampieBundle\StampieBundle()` to your `AppKernel.php` in the `registerBundles` method.

Add the configuration to `config.yml` as follows

``` yaml
stampie:
    mailer: postmark # [send_grid, postmark, mailgun, mandrill, mailjet, spark_post] are supported
    server_token: POSTMARK_API_TEST # Replace with your ServerToken for your Service
```

The HttpClient used by the bundle is configurable. By default, it uses the service `httplug.client`, which
is the name of the default HTTP client when using [HttplugBundle](https://github.com/php-http/HttplugBundle).
Using this bundle is optional. You can provide your own service integrating HTTPlug:


``` yaml
stampie:
    http_client: my_http_client
```

## StampieExtra

This bundles allows you to use [StampieExtra](https://github.com/Stampie/extra) easily:
add the extra library in your project. The integration is activated automatically to wrap the mailer
in the extra mailer dispatching events. An integration with the profiler is also provided.

If you want to enable the ImpersonateListener to send all emails to the same address, provide
a non-empty delivery address:

``` yaml
stampie:
    extra:
        delivery_address: dev@example.com
```
