# HBStampieBundle

[![Build Status](https://secure.travis-ci.org/henrikbjorn/HBStampieBundle.png)](http://travis-ci.org/henrikbjorn/HBStampieBundle)

Integrates [Stampie](http://github.com/henrikbjorn/Stampie) with Symfony2.

## Usage

Add `HB\StampieBundle\HBStampieBundle()` to your `AppKernel.php` in the `registerBundles` method.

Add the configuration to `config.yml` as follows

``` yaml
hb_stampie:
    adapter: buzz # buzz, guzzle and noop are supported
    mailer: postmark # [send_grid, mail_chimp, postmark] is supported
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
hb_stampie:
    extra: ~
```

If you want to enable the ImpersonateListener to send all emails to the same address, provide
a non-empty delivery address:

``` yaml
hb_stampie:
    extra:
        delivery_address: dev@example.com
```
