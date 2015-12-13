## Spark Data

[![Build Status](https://img.shields.io/travis/sparkphp/data.svg)](https://travis-ci.org/sparkphp/data)
[![Code Coverage](https://img.shields.io/coveralls/sparkphp/data.svg)](https://coveralls.io/r/sparkphp/data)
[![License](https://img.shields.io/packagist/l/sparkphp/data.svg)](https://github.com/sparkphp/data/blob/master/LICENSE)
[![Latest Stable Version](https://img.shields.io/packagist/v/sparkphp/data.svg)](https://packagist.org/packages/sparkphp/data)

Interfaces and traits for creating a data layer in [Spark](http://sparkphp.github.io/).
Attempts to be [PSR-1](http://www.php-fig.org/psr/psr-1/), [PSR-2](http://www.php-fig.org/psr/psr-2/),
and [PSR-4](http://www.php-fig.org/psr/psr-4/) compliant.

## Basic Example

```php
namespace Acme;

use Spark\Data\EntityInterface;
use Spark\Data\Traits\EntityTrait;

class User implements EntityInterface
{
    use EntityTrait;
    
    private $id;
    private $email;
    private $password;
    private $registered_on;
    
    private function types()
    {
        return [
            'id' => 'int',
        ];
    }
}
```

The `EntityTrait` is a composition of all of the available traits:

- `ImmutableValueObjectTrait`
- `DateAwareTrait`
- `JsonAwareTrait`
- `SerializeAwareTrait`

### Usage

The properties of classes `ImmutableObjectValueTrait` will be publicly accessible but read only:

```php
$user = new User([
    'id' => 1,
    ...
]);

echo $user->id; // 1
```

These objects can only be modified by copying the object using `withData`:

```php
$user = $user->withData([
    'email' => 'user@example.com',
]);
```

To check if an entity has a value, use the `has` method:

```php
$user->has('email'); // true
$user->has('role'); // false
```

*Note that this a check to see if the entity has a property defined. It will return `true`
even if the value is currently `null` or otherwise empty.*

To get an array of values from the object, use the `toArray` method:

```php
$data = $user->toArray();
```

### Additional Features

The `EntityTrait` will allow an entity to be passed to `json_encode`. It can also be
passed through `serialize` and `unserialize`.

Properties that represent dates can be fetched as [`Carbon`](http://carbon.nesbot.com/)
objects by using the `date` method:

```php
$registered = $user->date('registered_on'); // Carbon
```
