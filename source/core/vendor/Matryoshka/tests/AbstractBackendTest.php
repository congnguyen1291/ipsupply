<?php

// Be very strict about errors when testing.
error_reporting(E_ALL);

require_once __DIR__ . '/../library/iFixit/Matryoshka.php';

use iFixit\Matryoshka;

Matryoshka::autoload();

abstract class AbstractBackendTest extends PHPUnit_Framework_TestCase {
   protected abstract function getBackend();

   /**
    * It's hard to test them individually so we can just test all 3 at once.
    */
   public function testgetSetDelete() {
      $backend = $this->getBackend();
      list($key1, $value1) = $this->getRandomKeyValue();
      list($key2, $value2) = $this->getRandomKeyValue();
      list($key3, $value3) = $this->getRandomKeyValue();

      $this->assertNull($backend->get($key1));
      $this->assertTrue($backend->set($key1, $value1));
      $this->assertSame($value1, $backend->get($key1));
      $this->assertTrue($backend->set($key1, $value1));
      $this->assertTrue($backend->delete($key1));
      $this->assertNull($backend->get($key1));
      $this->assertFalse($backend->delete($key1));

      $this->assertTrue($backend->set($key2, $value2));
      $this->assertTrue($backend->set($key3, $value3));
      $this->assertSame($value2, $backend->get($key2));
      $this->assertSame($value3, $backend->get($key3));
   }

   public function testgetMultiple() {
      $backend = $this->getBackend();
      list($key1, $value1, $id1) = $this->getRandomKeyValueId();
      list($key2, $value2, $id2) = $this->getRandomKeyValueId();
      list($key3, $value3, $id3) = $this->getRandomKeyValueId();
      list($key4, $value4, $id4) = $this->getRandomKeyValueId();

      list($found, $missed) = $backend->getMultiple([]);
      $this->assertEmpty($found);
      $this->assertEmpty($missed);

      $keys = [
         $key1 => $id1,
         $key2 => $id2,
         $key3 => $id3,
         $key4 => $id4
      ];
      list($found, $missed) = $backend->getMultiple($keys);
      $this->assertEmpty(array_filter($found));
      $this->assertSame(array_keys($keys), array_keys($found));
      $this->assertSame($keys, $missed);

      $backend->set($key1, $value1);
      $backend->set($key2, $value2);
      $expectedFound = [$key1 => $value1, $key2 => $value2, $key3 => null,
       $key4 => null];
      $expectedMissed = [$key3 => $id3, $key4 => $id4];
      list($found, $missed) = $backend->getMultiple($keys);
      $this->assertSame($found, $expectedFound);
      $this->assertSame($missed, $expectedMissed);

      $backend->set($key3, $value3);
      $backend->set($key4, $value4);
      $expectedFound = [$key1 => $value1, $key2 => $value2, $key3 => $value3,
       $key4 => $value4];
      list($found, $missed) = $backend->getMultiple($keys);
      $this->assertSame($expectedFound, $found);
      $this->assertEmpty($missed);
   }

   public function testdeleteMultiple() {
      $backend = $this->getBackend();

      list($key1, $value1) = $this->getRandomKeyValue();
      list($key2, $value2) = $this->getRandomKeyValue();
      list($key3, $value3) = $this->getRandomKeyValue();
      list($key4, $value4) = $this->getRandomKeyValue();

      $this->assertTrue($backend->set($key1, $value1));
      $this->assertTrue($backend->set($key2, $value2));
      $this->assertTrue($backend->set($key3, $value3));
      $this->assertTrue($backend->set($key4, $value4));

      $this->assertTrue($backend->deleteMultiple([$key1, $key2]));
      $this->assertNull($backend->get($key1));
      $this->assertNull($backend->get($key2));
      $this->assertFalse($backend->deleteMultiple([$key1, $key3]));
      $this->assertFalse($backend->deleteMultiple([$key1, $key2, $key3, $key4]));
      $this->assertNull($backend->get($key1));
      $this->assertNull($backend->get($key2));
      $this->assertNull($backend->get($key3));
      $this->assertNull($backend->get($key4));
   }

   public function testadd() {
      $backend = $this->getBackend();
      list($key1, $value1) = $this->getRandomKeyValue();
      list($key2, $value2) = $this->getRandomKeyValue();

      $this->assertTrue($backend->add($key1, $value1));
      $this->assertFalse($backend->add($key1, $value1));
      $this->assertSame($value1, $backend->get($key1));
      $this->assertTrue($backend->add($key2, $value2));
      $this->assertSame($value2, $backend->get($key2));

      $this->assertTrue($backend->delete($key1));
      $this->assertNull($backend->get($key1));
      $this->assertTrue($backend->add($key1, $value1));
      $this->assertSame($value1, $backend->get($key1));
      $this->assertSame($value2, $backend->get($key2));
   }

   public function testincrement() {
      $backend = $this->getBackend();
      list($key1, $value1) = $this->getRandomKeyValue();
      list($key2, $value2) = $this->getRandomKeyValue();

      $this->assertNull($backend->get($key1));
      $currentValue = 0;
      foreach ([1, 5, 100] as $amount) {
         $currentValue += $amount;
         $this->assertSame($currentValue, $backend->increment($key1, $amount),
          "Amount: $amount");
      }

      $realValue = $backend->get($key1);
      if (get_called_class() === 'MemcacheTest'
       || get_called_class() === 'MemcachedTest') {
         // HHVM's memcache get returns a string rather than an integer.
         $realValue = (int)$realValue;
      }
      $this->assertSame($currentValue, $realValue);

      $this->assertTrue($backend->delete($key1));
      $this->assertNull($backend->get($key1));
      $this->assertSame(7, $backend->increment($key1, 7));

      // TODO: Memcache has some strange behavior with these values that
      // doesn't appear to match the docs. It might have to do with
      // compression.
      if (get_called_class() !== 'MemcacheTest'
       && get_called_class() !== 'MemcachedTest') {
         $invalidValues = [
            'string',
            ['array'],
            (object)['object' => 'value']
         ];
         foreach ($invalidValues as $invalidValue) {
            $this->assertTrue($backend->set($key2, $invalidValue));
            $this->assertSame(1, $backend->increment($key2));
            $this->assertSame(1, $backend->get($key2));
         }
      }
   }

   public function testdecrement() {
      $backend = $this->getBackend();
      list($key1, $value1) = $this->getRandomKeyValue();
      list($key2, $value2) = $this->getRandomKeyValue();

      $this->assertNull($backend->get($key1));

      // TODO: Memcache values cannot be decremented below 0 so we must
      // start it out higher.
      if (get_called_class() === 'MemcacheTest'
       || get_called_class() === 'MemcachedTest') {
         $currentValue = 400;
         $backend->set($key1, $currentValue);
      } else {
         $currentValue = 0;
      }
      foreach ([1, 5, 100] as $amount) {
         $currentValue -= $amount;
         $this->assertSame($currentValue, $backend->decrement($key1, $amount),
          "Amount: $amount");
      }

      $realValue = $backend->get($key1);
      if (get_called_class() === 'MemcacheTest'
       || get_called_class() === 'MemcachedTest') {
         // HHVM's memcache get returns a string rather than an integer.
         $realValue = (int)$realValue;
      }
      $this->assertSame($currentValue, $realValue);

      $this->assertTrue($backend->delete($key1));

      // TODO: Memcache has some strange behavior with these values that
      // doesn't appear to match the docs.
      if (get_called_class() !== 'MemcacheTest'
       && get_called_class() !== 'MemcachedTest') {
         $this->assertSame(-7, $backend->decrement($key1, 7));

         $invalidValues = [
            'string',
            ['array'],
            (object)['object' => 'value']
         ];
         foreach ($invalidValues as $invalidValue) {
            $this->assertTrue($backend->set($key2, $invalidValue));
            $this->assertSame(-1, $backend->decrement($key2));
            $this->assertSame(-1, $backend->get($key2));
         }
      }
   }

   /**
    * A negative increment/decrement should act as a positive
    * decrement/increment.
    */
   public function testNegativeIncrementDecrement() {
      $backend = $this->getBackend();
      list($key) = $this->getRandomKeyValue();
      $initialValue = 20;

      $this->assertTrue($backend->set($key, $initialValue));

      $this->assertSame($initialValue - 1, $backend->increment($key, -1));
      $this->assertSame($initialValue, $backend->decrement($key, -1));
      $this->assertEquals($initialValue, $backend->get($key));
   }

   public function testgetAndSet() {
      $backend = $this->getBackend();
      list($key, $value) = $this->getRandomKeyValue();

      $this->assertNull($backend->get($key));

      $miss = false;
      $callback = function() use ($value, &$miss) {
         $miss = true;
         return $value;
      };

      $getAndSetValue = $backend->getAndSet($key, $callback);

      $this->assertTrue($miss);
      $this->assertSame($value, $getAndSetValue);
      $this->assertSame($value, $backend->get($key));

      $miss = false;
      $getAndSetValue = $backend->getAndSet($key, $callback);

      $this->assertFalse($miss);
      $this->assertSame($value, $getAndSetValue);
      $this->assertSame($value, $backend->get($key));

      $miss = false;
      list(, $newValue) = $this->getRandomKeyValue();
      $callback = function() use ($newValue, &$miss) {
         $miss = true;
         return $newValue;
      };

      // Try resetting the backend with getAndSet.
      $getAndSetValue = $backend->getAndSet($key, $callback, 0, $reset = true);

      $this->assertTrue($miss);
      $this->assertSame($newValue, $getAndSetValue);
      $this->assertSame($newValue, $backend->get($key));


      list($key, $value) = $this->getRandomKeyValue();
      $this->assertNull($backend->get($key));
      $backend->set($key, $value);

      // Return null from the callback and make sure the value isn't updated.
      $getAndSetValue = $backend->getAndSet($key,
       function() { return null; }, 0, $reset = true);

      $this->assertNull($getAndSetValue);
      $this->assertSame($value, $backend->get($key));
   }

   public function testgetAndSetMultiple() {
      $backend = $this->getBackend();
      list($key1, $value1, $id1) = $this->getRandomKeyValueId();
      list($key2, $value2, $id2) = $this->getRandomKeyValueId();
      list($key3, $value3, $id3) = $this->getRandomKeyValueId();
      list($key4, $value4, $id4) = $this->getRandomKeyValueId();
      $keys = [
         $key1 => $id1,
         $key2 => $id2,
         $key3 => $id3,
         $key4 => $id4
      ];
      $keyToValue = [
         $key1 => $value1,
         $key2 => $value2,
         $key3 => $value3,
         $key4 => $value4
      ];
      $numMisses = -1;
      $callback = function($missing) use ($keyToValue, &$numMisses) {
         $this->assertNotEmpty($missing);
         $numMisses = count($missing);

         $result = [];

         foreach ($missing as $key => $id) {
            $result[$key] = $keyToValue[$key];
         }

         return $result;
      };

      $this->assertSame($keyToValue,
       $backend->getAndSetMultiple($keys, $callback));
      $this->assertSame(count($keyToValue), $numMisses);

      $numMisses = -1;
      $this->assertSame($keyToValue,
       $backend->getAndSetMultiple($keys, $callback));
      $this->assertSame(-1, $numMisses);

      $backend->delete($key2);
      $backend->delete($key3);
      $this->assertSame($keyToValue,
       $backend->getAndSetMultiple($keys, $callback));
      $this->assertSame(2, $numMisses);

      $numMisses = -1;
      $newKeys = [];
      $this->assertSame($keyToValue,
       $backend->getAndSetMultiple($keys, $callback));
      $this->assertSame(-1, $numMisses);

      $emptyCallbacks = [
         'empty' => function($missing) use (&$numMisses) {
            $numMisses = count($missing);
            return [];
         },
         'invalid values' => function($missing) use (&$numMisses, &$newKeys) {
            $numMisses = count($missing);
            list($key1, $value1) = $this->getRandomKeyValue();
            list($key2, $value2) = $this->getRandomKeyValue();

            $newKeys = [$key1, $key2];

            return [
               $key1 => $value1,
               $key2 => $value2
            ];
         }
      ];

      foreach ($emptyCallbacks as $type => $emptyCallback) {
         list($key1, $value1, $id1) = $this->getRandomKeyValueId();
         list($key2, $value2, $id2) = $this->getRandomKeyValueId();
         list($key3, $value3, $id3) = $this->getRandomKeyValueId();
         $keys = [
            $key1 => $id1,
            $key2 => $id2,
            $key3 => $id3
         ];

         $numMisses = -1;
         $newKeys = [];
         $result = $backend->getAndSetMultiple($keys, $emptyCallback);
         $this->assertEmpty($result);
         $this->assertSame(count($keys), $numMisses);

         // Make sure the false keys aren't set.
         foreach ($keys as $key => $id) {
            $this->assertNull($backend->get($key));
         }

         // Make sure the new keys aren't set.
         foreach ($newKeys as $key) {
            $this->assertNull($backend->get($key));
         }
      }
   }

   public function testlongKeys() {
      $backend = $this->getBackend();
      list($key, $value) = $this->getRandomKeyValue();
      // Make a super long key.
      $key = str_repeat($key, 100);

      $this->assertNull($backend->get($key));
      $this->assertTrue($backend->set($key, $value));
      $this->assertSame($value, $backend->get($key));

      $newKey = "{$key}-new";
      $newValue = "{$value}-new";
      $this->assertNull($backend->get($newKey));
      $this->assertTrue($backend->set($newKey, $newValue));
      $this->assertSame($newValue, $backend->get($newKey));
   }

   public function testvalidKeys() {
      $backend = $this->getBackend();
      // Just a bunch of special characters that shouldn't cause any problems.
      $validChars = [
         '`', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_',
         '+', '=', '|', '\\', '[', '{', ']', '}', '<', ',', '>', '.', '?', '/',
         '☃', "\n", "\r", ' ',
      ];

      list($baseKey, $value) = $this->getRandomKeyValue();
      $equivalentKeyChars = [];
      foreach ($validChars as $char) {
         $key = $baseKey . $char;
         $value = $char;

         // If this key had been set before, it means one of the previous
         // chars are considered equivalent, and that's bad.
         $prevVal = $backend->get($key);
         if ($prevVal !== null) {
            if (!array_key_exists($prevVal, $equivalentKeyChars)) {
               $equivalentKeyChars[$prevVal][] = $prevVal;
            }
            $equivalentKeyChars[$prevVal][] = $char;
            $value = $prevVal;
         }

         $this->assertTrue($backend->set($key, $value));
         $this->assertSame($value, $backend->get($key));
      }

      $allEquivalentChars = [];
      array_walk_recursive($equivalentKeyChars,
       function($char) use (&$allEquivalentChars) {
         if (!$this->isCharExemptFromKeyEquivalence($char)) {
            $allEquivalentChars[] = $char;
         }
      });
      $this->assertSame([], $allEquivalentChars,
       "Some keys are considered equivalent when they shouldn't be");
   }

   public function testisAvailable() {
      // Only available backends should be used for tests.
      $this->assertTrue($this->getBackend()->isAvailable());
   }

   protected function getRandomKeyValue() {
      return [
         'key-' . rand(),
         'value-' . rand()
      ];
   }

   protected function getRandomKeyValues($count = 5) {
      $keys = [];

      for ($i = 0; $i < $count; $i++) {
         $keys['key-' . rand()] = 'value-' . rand();
      }

      return $keys;
   }

   protected function getRandomKeyValueId() {
      return [
         'key-' . rand(),
         'value-' . rand(),
         'id-' . rand()
      ];
   }

   protected function getMemcache() {
      $memcache = new Memcache();
      $memcache->pconnect('localhost', 11211);

      return $memcache;
   }

   /**
    * Allow some implementations to exempt some chars from key equivalence
    * checking.
    */
   protected function isCharExemptFromKeyEquivalence($char) {
      return false;
   }
}

// Exposes the array of cached values.
class TestEphemeral extends Matryoshka\Ephemeral {
   public function getCache() {
      return $this->cache;
   }

   public function clear() {
      $this->cache = [];
   }
}

class TestLocal extends Matryoshka\Local {
   public function getCache() {
      return $this->cache;
   }
}
