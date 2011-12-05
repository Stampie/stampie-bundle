# HBStampieBundle

Integrates [Stampie](http://github.com/henrikbjorn/Stampie) with Symfony2.

## Usage

Add `HB\StampieBundle\HBStampieBundle()` to your `AppKernel.php` in the `registerBundles` method.

Add the configuration to `config.yml` as follows

``` yaml
hb_stampie:
    adapter: buzz # Only Buzz is supported
    mailer: postmark # [send_grid, mail_chimp, postmark] is supported
    server_token: POSTMARK_API_TEST # Replace with your ServerToken for you Service
```

For the `buzz` adapter to work it is required to have a `buzz` service fortunately [SensioBuzzBundle](http://github.com/sensio/SensioBuzzBundle)
provides this.
