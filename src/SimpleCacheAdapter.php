<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/craft-psr16/blob/master/LICENSE
 * @link       https://github.com/flipbox/craft-psr16
 */

namespace flipbox\craft\psr16;

use Craft;
use DateInterval;
use DateTime;
use Psr\SimpleCache\CacheInterface;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\caching\CacheInterface as YiiCacheInterface;
use yii\di\Instance;
use yii\helpers\StringHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SimpleCacheAdapter extends Component implements CacheInterface
{
    /**
     * @var YiiCacheInterface
     */
    public $cache;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->cache === null) {
            $this->cache = Craft::$app->getCache();
        }

        $this->cache = Instance::ensure(
            $this->cache instanceof \Closure ?
                call_user_func($this->cache) :
                $this->cache,
            YiiCacheInterface::class
        );
    }

    /**
     * @return YiiCacheInterface
     */
    protected function getCache(): YiiCacheInterface
    {
        return $this->cache;
    }

    /**
     * Cache::get() return false if the value is not in the cache or expired, but PSR-16 return $default(null)
     *
     * @param string $key
     * @param null $default
     * @return bool|mixed|null
     * @throws InvalidArgumentException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $key = $this->buildKey($key);

        $data = $this->getCache()->get($key);

        if ($data === false) {
            return $default;
        }

        if ($data === null) {
            return false;
        }

        return $data;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param null $ttl
     * @param null $dependency
     * @return bool
     * @throws InvalidArgumentException
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null, $dependency = null): bool
    {
        $key = $this->buildKey($key);

        $duration = $this->dateIntervalToSeconds($ttl);

        return $this->getCache()->set($key, $value, $duration, $dependency);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        $key = $this->buildKey($key);
        return $this->has($key) ? $this->getCache()->delete($key) : true;
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        return $this->getCache()->flush();
    }

    /**
     * @param iterable $keys
     * @param null $default
     * @return array|iterable
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        if (!$keys instanceof \Traversable && !is_array($keys)) {
            throw new InvalidArgumentException(
                'Invalid keys: ' . var_export($keys, true) . '. Keys should be an array or Traversable of strings.'
            );
        }
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = $this->get($key, $default);
        }
        return $data;
    }

    /**
     * @param iterable $values
     * @param null $ttl
     * @return bool
     */
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        if (!$values instanceof \Traversable && !is_array($values)) {
            throw new InvalidArgumentException(
                'Invalid keys: ' . var_export($values, true) . '. Keys should be an array or Traversable of strings.'
            );
        }
        $pairs = [];
        foreach ($values as $key => $value) {
            $key = $this->buildKey($key);
            $pairs[$key] = $value;
        }
        $res = true;
        foreach ($pairs as $key => $value) {
            $res = $res && $this->set($key, $value, $ttl);
        }
        return $res;
    }

    /**
     * @param iterable $keys
     * @return bool
     */
    public function deleteMultiple(iterable $keys): bool
    {
        if ($keys instanceof \Traversable) {
            $keys = iterator_to_array($keys, false);
        } else {
            if (!is_array($keys)) {
                throw new InvalidArgumentException(
                    'Invalid keys: ' . var_export($keys, true) . '. Keys should be an array or Traversable of strings.'
                );
            }
        }
        $res = true;
        array_map(function ($key) use (&$res) {
            $res = $res && $this->delete($key);
        }, $keys);
        return $res;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $key = $this->buildKey($key);
        return $this->getCache()->exists($key);
    }

    /**
     * @param $ttl
     * @return null|int
     */
    protected function dateIntervalToSeconds($ttl)
    {
        if ($ttl === null || !is_int($ttl)) {
            return $ttl;
        }

        if ($ttl instanceof DateInterval) {
            return ((new DateTime())->add($ttl))->getTimestamp() - (new DateTime())->getTimestamp();
        }

        return null;
    }


    /**
     * Builds a normalized cache key from a given key.
     *
     * If the given key is a string containing alphanumeric characters only and no more than 32 characters,
     * then the key will be returned back as it is. Otherwise, a normalized key is generated by serializing
     * the given key and applying MD5 hashing.
     *
     * @param mixed $key the key to be normalized
     * @return string the generated cache key
     */
    protected function buildKey($key)
    {
        if (is_string($key)) {
            return ctype_alnum($key) && StringHelper::byteLength($key) <= 32 ? $key : md5($key);
        }
        return md5(json_encode($key));
    }
}
