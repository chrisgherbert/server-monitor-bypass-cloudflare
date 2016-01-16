## Check response code of web server, using IP address

This check the response code of a website by using the IP address directly.  This is to bypass Cloudflare (or other similar services), which can continue to present a page even if the real webserver is down.

It's basically like putting the IP address and hostname directly in your hosts file.

If the server doesn't respond, or responds with a 4XX or 5XX code, an email is sent to the provided email addresses.

### Getting Started

First, install dependencies using:

```sh
composer install
```
After creating a configuration file (see below), just run:

```sh
php check.php
```

This probably only useful in a cron job that runs periodically.

### Configuration

The app is configured using `config.json`, present in the root.  The easiest way to get started is to duplicate the provided `config.json.example` file.  Then replace the values with your own.

```sh
cp config.json.example config.json
```

```json
{
	"recipients": [
		"example@email.com",
		"example2@email.com",
	],
	"smtp": {
		"host": "smtp.example.com",
		"username": "user",
		"password": "password",
		"send_email": "example@email.com",
		"auth": true,
		"secure": "tls",
		"port": 587
	},
	"servers": [
		{
			"host": "domain.com",
			"ip": "192.192.192.191"
		},
		{
			"host": "domain2.com",
			"ip": "192.192.192.192"
		}
	]
}
```