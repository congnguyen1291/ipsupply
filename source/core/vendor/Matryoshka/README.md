# Matryoshka

[![Build Status](https://travis-ci.org/iFixit/Matryoshka.png?branch=master)](https://travis-ci.org/iFixit/Matryoshka)
[![HHVM Status](http://hhvm.h4cc.de/badge/ifixit/matryoshka.svg)](http://hhvm.h4cc.de/package/ifixit/matryoshka)

Matryoshka is a caching library for PHP built around nesting components like [Russian nesting dolls].

[Russian nesting dolls]: http://en.wikipedia.org/wiki/Matryoshka_doll

## Motivation

The [Memcache] and [Memcached] PHP client libraries offer fairly low level access to [memcached servers].
Matryoshka adds convenience functions to simplify common operations that aren't covered by the client libraries.
Most of the functionality is provided by nesting `Backend`s.
For example, prefixing cache keys is accomplished by nesting an existing `Backend` within a `Prefix` backend.
This philosophy results in very modular components that are easy to swap in and out and simplify testing.

This concept is used to support key prefixing, disabling `get`s/`set`s/`delete`s, defining cache fallbacks in a hierarchy, storing values in clearable scope, and recording statistics.

[Memcache]: http://php.net/memcache
[Memcached]: http://php.net/memcached
[memcached servers]: http://memcached.org

## Backends

### Memcache

Wraps the [Memcache] client library.

```php
$memcache = new Memcache();
$memcache->pconnect('localhost', 11211);
$cache = Matryoshka\Memcache::create($memcache);

$value = $cache->get('key');
```

### APCu

Caches values in a shared memory segment (available to all processes under
a webserver) using the [APCu php extension].

```php
$cache = new Matryoshka\APCu();
$cache->set('key', 'value');
$value = $cache->get('key');
```

### Ephemeral

Caches values in a local memory array that lasts the duration of the PHP process.

```php
$cache = new Matryoshka\Ephemeral();
$cache->set('key', 'value');
$value = $cache->get('key');
```

### Enable

A wrapper around a cache that allows *disabling* `get`, `set`, or `delete` operations.
When an action is disabled the underlying backend is not modified nor accessed and `false` is returned.

```php
$cache = new Matryoshka\Enable($backend = (new Matryoshka\Ephemeral()));
$cache->getsEnable = false;
$cache->get('key'); // Always results in a miss.
```

### ExpirationChange

Modifies all expiration times using a callback for the new value.
This allows things like randomizing or scaling expiration times to decrease miss storms or improve hit ratios.

```php
$changeFunc = function($expiration) {
   // Double all expiration times.
   return $expiration * 2;
};
$cache = new Matryoshka\ExpirationChange($backend, $changeFunc);
$cache->set('key', 'value', 10); // Results in an expiration time of 20.
```

### KeyFix

Fixes troublesome keys by shortening longer ones to less than or equal the specified length, and getting rid of any specified, invalid characters.
It accomplishes this by using `md5` to hash offending keys into an alphanumeric string of uniform length.

```php
$cache = new Matryoshka\KeyFix(
   new Matryoshka\Ephemeral(),
   $maxLength = 50,
   $invalidChars = " \n"
);

// Gets converted to: `2552e62135d11e8d4233e2a51868132e`
$cache->get("long_key_that_needs_to_be_shortened_by_just_a_little_bit");

// Gets converted to: `6c4421388643338490f9c2c895af4fec`
$cache->get("key with bad chars like spaces");
```

### KeyShorten

Ensures that all keys are at most the specified length by shortening longer ones.
Long keys are shortend by using `md5` on the end of the string
to ensure long strings with a common prefix don't map to the same key.

Either this _or_ KeyFix should be used, not both. KeyFix handles long keys _and_ bad characters, making this an unnecessary addition.

```php
$cache = new Matryoshka\KeyShorten(
   new Matryoshka\Ephemeral(),
   $maxLength = 50
);

// Gets converted to: `long_key_that_need2552e62135d11e8d4233e2a51868132e`
$cache->get("long_key_that_needs_to_be_shortened_by_just_a_little_bit");
```

### Prefix

Prefixes all keys with a string.

```php
$cache = new Matryoshka\Prefix(new Matryoshka\Ephemeral(), 'prefix-');
// The key ends up being "prefix-key".
$cache->set('key', 'value');
$value = $cache->get('key');
```

### Stats

Records counts and timings for operations to be used for metrics.

```php
$cache = new Matryoshka\Stats(new Matryoshka\Ephemeral());
$cache->set('key', 'value');
$value = $cache->get('key');
var_dump($cache->getStats());
// array(
//    'get_count' => 1,
//    'get_time' => 0.007,
//    'set_count' => 1
//    'set_time' => 0.008,
//    ...
// )
```

### Hierarchy

Sets caches in a hierarchy to prefer faster caches that get filled in by slower caches.

Note: This Backend is currently experimental due to some of its potentially unexpected behavior.

```php
$cache = new Matryoshka\Hierarchy([
   new Matryoshka\Ephemeral(),
   Matryoshka\Memcache::create(new Memcache('localhost')),
   Matryoshka\Memcache::create(new Memcache($cacheServers)),
]);

// This misses the first two caches (array and local memcached) but hits the
// final cache. The retrieved value is then set in the local memcache as well
// as the memory array so subsequent requests can be fulfilled faster.
$value = $cache->getAndSet('key', function() {
   return 'value';
}, 3600);
// This is retrieved from the memory array without going all the way to
// Memcache.
$value = $cache->getAndSet('key', function() {
   return 'value';
}, 3600);
```

### Local

Caches all values from the specified backend in a local array so subsequent requests for the same key can be fulfilled faster.

```php
$cache = new Matryoshka\Local(new Memcache());
```

It's a faster version of:

```php
$cache = new Matryoshka\Hierarchy([
   new Matryoshka\Ephemeral(),
   Matryoshka\Memcache::create(new Memcache('localhost'))
]);
```

### Scope

Caches values in a scope that can be deleted to invalidate all cache entries under the scope.

```php
$cache = new Matryoshka\Scope(new Matryoshka\Ephemeral(), 'scope');
$cache->set('key', 'value');
$value = $cache->get('key'); // => 'value'
$cache->deleteScope();
// This results in a miss because the scope has been deleted.
$value = $cache->get('key'); // => false
```

### MultiScope

Uses multiple scopes to store keys. Stores the scope or scopes in one backend and the scoped values in another.
This primarily allows storing a scope in a shared but slower-to-acccess backend (for easy deletion), while storing the values in a local and faster backend for speedy access.

```php
$scope1 = new Matryoshka\Scope($remoteMemcache, 'scope1');
$scope2 = new Matryoshka\Scope($remoteMemcache, 'scope2');
$multiScope = new Matryoshka\MultiScope($localMemcache, [
   $scope1,
   $scope2
]);
// Stores the value on the local memcached backend but scope it to
// scope1 and scope2 which are stored on the remote memcache instance.
$multiScope->set('key', 'value');
$scope1->deleteScope();
// This results in a miss because one of the scopes has been deleted.
$value = $multiScope->get('key');
```

## Convenience Functions

### getAndSet

Wrapper for `get()` and `set()` that uses a read-through callback to generate missed values.

```php
$cache = new Matryoshka\Ephemeral();
// Calls the provided callback if the key is not found and sets it in the cache
// before returning the value to the caller.
$value = $cache->getAndSet('key', function() {
   return 'value';
});
```

### getAndSetMultiple

Wrapper around `getMultiple()` that uses a callback to generate values in batch to populate the cache.


```php
$cache = new Matryoshka\Ephemeral();
$keys = [
   'key1' => 'id1',
   'key2' => 'id2'
];
// Calls the provided callback for any missed keys so the missing values can be
// generated and set before returning them to the caller. The values are
// returned in the same order as the provided keys.
$values = $cache->getAndSetMultiple($keys, function($missing) {
   // Use the id's to fill in the missing values.
   foreach ($missing as $key => $id) {
      if ($id == 'id1') {
         $value = 'value1';
      } else if ($id == 'id2') {
         $value = 'value2';
      }

      $missing[$key] = $value;
   }

   // Return the new values to be cached and merged with the hits.
   return $missing;
});
```

## License

    The MIT License (MIT)

    Copyright (c) 2014 iFixit

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the Software), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED AS IS, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.

[APCu php extension]: http://php.net/manual/en/book.apcu.php
