<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use Symfony\Contracts\Cache\CacheInterface as SymfonyCacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class SymfonyCacheAdapter implements CacheInterface
{
    public function __construct(
        private readonly SymfonyCacheInterface $cache
    ) {
    }

    public function get(string $key): mixed
    {
        return $this->cache->get($key, function (ItemInterface $item) {
            return null;
        });
    }

    public function set(string $key, mixed $value, int $ttl = 3600): void
    {
        $this->cache->get($key, function (ItemInterface $item) use ($value, $ttl) {
            $item->expiresAfter($ttl);
            return $value;
        });
    }

    public function delete(string $key): void
    {
        $this->cache->delete($key);
    }

    public function clear(): void
    {
        // Note: Symfony cache doesn't have a clear method by default
        // This would need to be implemented based on the specific cache adapter
    }
}
