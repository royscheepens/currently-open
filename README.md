# Currently Open

[![Total Downloads](https://poser.pugx.org/royscheepens/currently-open/downloads)](https://packagist.org/packages/royscheepens/currently-open)
[![License](https://poser.pugx.org/royscheepens/currently-open/license)](https://packagist.org/packages/royscheepens/currently-open)
[![Latest Stable Version](https://poser.pugx.org/royscheepens/currently-open/v/stable)](https://packagist.org/packages/royscheepens/currently-open)
[![Monthly Downloads](https://poser.pugx.org/royscheepens/currently-open/d/monthly)](https://packagist.org/packages/royscheepens/currently-open)

> A simple Laravel Package to determine if a shop is open or closed, based on configurable config settings

This package is built to check whether your business is open, based on configurable settings. It currently supports setting default time slots for the days in a week, and the option to set exceptions based on a certain date.

## Installation
Install with `composer`:

Laravel 5.4 and above
```
composer require royscheepens/currently-open:^0.1.0
```

And add the service provider in `config/app.php`
```php
'providers' => [
    ........,
    RoyScheepens\CurrentlyOpen\CurrentlyOpenServiceProvider::class,
]
```

If you want to use the facade, add this to your facades in `config/app.php`
```php
'aliases' => [
    ........,
    'CurrentlyOpen' => RoyScheepens\CurrentlyOpen\CurrentlyOpenFacade::class,
]

```

To publish the configuration file, run
```
php artisan vendor:publish --provider="RoyScheepens\CurrentlyOpen\CurrentlyOpenServiceProvider"
```

## Configuration

The only required config value is 'weekdays', which lets you set the opening hours for the days of the week. It is not required to set all days, if a day is not set we assume you're not open.

Exceptions per date are also possible, but not required.

```php
return [

    // Add timeslots for each day in the week. Weekdays start at 0 (Sunday)
    'weekdays'      =>  [
        0 => false, // False means we're closed. Set to true to be opened all day
        1 => ['09:00', '17:30'],
        2 => ['09:00', '17:30'],
        3 => ['09:00', '17:30'],
        4 => ['09:00', '17:30'],
        5 => ['09:00', '17:30'],
        6 => ['10:00', '12:30'
    ],

    // Possible exceptions per date. Please note that 'weekday' rules will be overridden
    'exceptions'    =>  [
        '2017-12-25' => false // Closed on Christmas
        '2017-12-26' => ['12:00', '17:30'] // Opening a bit later... *burp*
    ]

];
```

## Usage

All examples are based on the config example above.

```php
$result = CurrentlyOpen::check()

var_dump($result->open); // True if we're open, false if not
```

This checks your config with the current date and time, e.g. 'now'. You can also supply a date to check dates in advance. The variable `$date` can be a Carbon instance, or a string parseable by Carbon.

```php
$date = '2017-12-25 12:00'; // Let's see if we're open for Christmas
$result = CurrentlyOpen::check($date);

var_dump($result->open); // False
```

The result of the `check()` method also contains a `until` attribute, which lets you know until when you're open. This attribute is returned as a Carbon instance and only included when `open` is true.

```php
$date = '2017-12-05 12:00'; // Let's see if we're open this Tuesday in December
$result = CurrentlyOpen::check($date);

var_dump($result->open); // True
var_dump($result->until->toDateTimeString()); // 2017-12-05 17:30:00
```

If you just want to check if you're open, use the method below. It returns a boolean and also accepts a `$date` variable, as seen above.

```php
$result = CurrentlyOpen::checkSimple()

var_dump($result) // True if we're open, false if not
```

## Future Roadmap

The package is still very basic and could use some more features, like:

- When open for the entire day, `until` should also check for time slots in the following day(s) 
- When closed, add the date and time when we're open again
- Support for multiple time slots per day (also for exceptions)
- More robust config checks