Antpool
=========

Laravel PHP Facade/Antpool for the Antpool API

## Requirements

- PHP 5.6 or higher
- Laravel 5.1 or higher

## Installation

Run in console below command to download package to your project:
```
composer require aburakovskiy/laravel-antpool-api
```

## Configuration

In `/config/app.php` add AntpoolServiceProvider:
```
Aburakovskiy\LaravelAntpoolApi\AntpoolServiceProvider::class,
```

Do not forget to add also Antpool facade there:
```
'Antpool' => Aburakovskiy\LaravelAntpoolApi\Facades\Antpool::class,
```

Publish config settings:
```
$ php artisan vendor:publish --provider="Aburakovskiy\LaravelAntpoolApi\AntpoolServiceProvider"
```

Set your Antpool API credentials in the file:

```
/config/antpool.php
```

Or in the .env file
```
ANTPOOL_USERNAME = USERNAME
ANTPOOL_KEY = KEY
ANTPOOL_SECRET = SECRET
```


## Usage

```php
use Aburakovskiy\LaravelAntpoolApi\Facades\Antpool;

// Return an account info
$account = Antpool::get('account');
```

## Antpool API
- [Antpool API Developer Guide](https://www.antpool.com/user/apiGuild.htm)

## Credits
Built on code from Elompenta's [antpool-php-api](https://github.com/Elompenta/antpool-php-api).
