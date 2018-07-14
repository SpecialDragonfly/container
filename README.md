# Container

A very simple Container based around an array. Limited functionality.

## Usage

```php
$container = new Container();
$container->set('foo', function() {
    return new stdClass();
});
$container->get('foo');
```