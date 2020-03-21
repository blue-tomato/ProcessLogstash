# ProcessLogstash

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![ProcessWire 3](https://img.shields.io/badge/ProcessWire-3.x-orange.svg)](https://github.com/processwire/processwire)

This Module for [ProcessWire CMS/CMF](http://processwire.com/) will send your ProcessWire Logs to [Logstash](https://www.elastic.co/logstash) via HTTP-Input.

## Installation

1. Execute the following command in the root directory of your ProcessWire installation:

```bash
composer require blue-tomato/process-logstash
```

2. ProcessWire will detect the module and list it in the backend's `Modules` > `Site` > `ProcessLogstash` section. Navigate there and install it.

3. Add you Logstash HTTP-Input Endpoint URL into field on the Module Configuration Page

### Override Endpoint or deactivate Logging on Dev/Stage Servers
If you want to override the endpoint URL set by the module configuration you can add to your config.php or config-dev.php following:

- `$config->processLogstash = [ "endpoint" => "http://localhost:9600" ]`

If you want to deactivate the logging you can set this also to `false`:

- `$config->processLogstash = [ "endpoint" => false ]`

### Add custom HTTP Header to the Request
Some Logstash cloud providers like logit.io need an API Key in the HTTP Headers. You can add it with this way in the config.php

- `$config->processLogstash = [ "customHttpHeaders" => [ "ApiKey: YOUR-API_KEY" ] ]`

### Request to Logstash throw a proxy server
If you have your Server behind a proxy, you can add to your `config.php` file following properties:

- `$config->httpProxy = "your-http-proxy-server.xyz:8888";`
- `$config->httpsProxy = "your-https-proxy-server.xyz:5394";`

## Support

Please [open an issue](https://github.com/blue-tomato/ProcessLogstash/issues/new) for support.

## Contributing

Create a branch on your fork, add commits to your fork, and open a pull request from your fork to this repository.

To get better insights and onboard you on module implementation details just open a support issue. We'll get back to you asap.

## Credits

This module is made by people from Blue Tomato. If you want to read more about our work, follow us on https://dev.to/btdev

## License

Find all information about this module's license in the LICENCE.txt file.
