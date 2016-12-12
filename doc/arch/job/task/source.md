# Source

* The source class is responsable for reading one enitity and mapping it.
* You can chain many sources together.

## Create a source

```php
<?php

# SourceFactory example
$reader = new Reader\Csv;
$simpleMapper = new Source\Product\Simple($reader, $simpleFields);
$configurableMapper = new Source\Product\Configurable($simpleMapper, $configurableFields);

return $configurableMapper;
```

Now the `$configurableMapper` works as source, the [task](../task.md) can run `$source->getNextEntity()` and get one entity.


```php
<?php

# When developing, a mock can easily be used

$mock = new Source\Mock\Product;
$mapper = new Soruce\Product\Simple($mock, $productFields);

return $mapper;
```