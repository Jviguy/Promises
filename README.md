# Promises
A simple and fast pmmp js es6 promise virion

## Api usage
these examples are in base injectable php these should be easily transferable.
### Example usage of `Promise::then(callable)`

```php
use Jviguy\Promises\Promise;
class A {
    /**
    * @return Promise<string> - a promise containing a string
    */
    public static function foo(): Promise {
        return new Promise(fn() => "hello");
    }
}

A::foo()->then(function ($string) {
    echo $string; // should print "hello"
});
```

### Example usage of `Promise::catch(callable(Exception))`

```php
use Jviguy\Promises\Promise;
class A {
    /**
    * @return Promise<string> - a promise containing a string
    */
    public static function foo(): Promise {
        return new Promise(fn() => throw new Exception("testing exception"));
    }
}

A::foo()->catch(function (Exception $error) {
    echo "an error has a occurred message: ";
    echo $error->getMessage();
});
```