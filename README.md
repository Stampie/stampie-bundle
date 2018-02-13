# StampieBundle

[![Build Status](https://api.travis-ci.org/Stampie/HBStampieBundle.png)](https://travis-ci.org/Stampie/HBStampieBundle)

Integrates [Stampie](https://github.com/Stampie/Stampie) with Symfony.

## Usage

Add `Stampie\StampieBundle\StampieBundle()` to your `AppKernel.php` in the `registerBundles` method.

Add the configuration to `config.yml` as follows

``` yaml
stampie:
    adapter: buzz # buzz, guzzle and noop are supported
    mailer: postmark # [send_grid, postmark, mailgun, mandrill] is supported
    server_token: POSTMARK_API_TEST # Replace with your ServerToken for you Service
```

For the `buzz` adapter to work it is required to have a `buzz` service fortunately [SensioBuzzBundle](http://github.com/sensio/SensioBuzzBundle)
provides this.

If you want to use the `guzzle` adapter, the [MisdGuzzleBundle](https://github.com/misd-service-development/guzzle-bundle) provides
the required dependencies.

## StampieExtra

This bundles allows you to use [StampieExtra](https://github.com/stof/StampieExtra) easily:
add the extra library in your project and activate the configuration to wrap the mailer
in the extra mailer dispatching events:

``` yaml
stampie:
    extra: ~
```

If you want to enable the ImpersonateListener to send all emails to the same address, provide
a non-empty delivery address:

``` yaml
stampie:
    extra:
        delivery_address: dev@example.com
```
