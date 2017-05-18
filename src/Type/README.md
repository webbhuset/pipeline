# Types

### Usage

Use the `TypeConstructor` class and instantiate your type class with `T::<Type>`.

Types
* __Any__ (No type validation)
* __Bool__ (`true`, `false`)
* __Enum__
* __Float__
    * __Decimal__
* __Hashmap__
* __Int__
* __Scalar__ (String, Float, Int, Bool)
* __Set__ (Unique set, all elements of the same type)
* __String__
    * __Datetime__
* __Struct__
* __Union__

### Example

```php
<?php

use Webbhuset\Bifrost\Type\TypeConstructor AS T;

$languagesType  = T::Enum(['en', 'sv', 'es']);
$currencyType   = T::Enum(['USD', 'SEK', 'EUR']);

$productType = T::Struct(
    'name' => T::Hashmap(
        $languagesType,
        T::String(
            T::MIN(1),
            T::MAX(255)
        )
    ),
    'sku' => T::String(
        T::MIN(1),
        T::NOTMATCH([
            '/(^\s+\S)|(\S\s+$)/' => 'Trailing whitespace in field',
        ])
    ),
    'description' => T::Hashmap(
        $languagesType,
        T::String(T::NULLABLE)
    ),
    'price' => T::Hashmap(
        $currencyType,
        T::Decimal(
            T::MIN(0),
            T::NULLABLE
        )
    ),
    'color' => T::Enum(['White', 'Blue', 'Green']), // Option Attribute
    'size'  => T::Set(T::Enum(['S', 'M', 'L'])),    // Multiselect Attribute
);

$product = [
    'name' => [
        'en' => 'White Dress',
        'sv' => 'Vit Klänning',
        'es' => 'Vestido blanco',
    ],
    'sku' => '1001-10-20',
    'price' => [
        'USD' => 12.95,
        'SEK' => 129.00,
        'EUR' => 10.95,
    ],
    'color' => 'White',
    'size' => ['S', 'M'],
];
```
