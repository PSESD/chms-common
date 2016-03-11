<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Repositories\Role;

use CHMS\Common\Repositories\BaseRepository;
use Cache;

class Provider
    extends BaseRepository
    implements Contract
{
    const ROLE_REGISTRY_CACHE_KEY = '__roles';

    private $registryCache;
    private $registryCacheBySystemId;

    public function clearRegistryCache()
    {
        Cache::forget(static::ROLE_REGISTRY_CACHE_KEY);
        $this->registryCache = null;
    }

    public function getRegistry()
    {
        if ($this->registryCache === null && !($this->registryCache = Cache::get(static::ROLE_REGISTRY_CACHE_KEY)) || empty($registry)) {
            $registryRaw = $this->findAll();
            $this->registryCache = [];
            foreach ($registryRaw as $role) {
                $this->registryCache[$role->id] = $role;
            }
            Cache::forever(static::ROLE_REGISTRY_CACHE_KEY, $this->registryCache);
        }
        return $this->registryCache;
    }

    public function getRegistryBySystemId()
    {
        if (!isset($this->registryCacheBySystemId)) {
            $this->registryCacheBySystemId = [];
            $registryById = $this->getRegistry();
            foreach ($registryById as $role) {
                $this->registryCacheBySystemId[$role['system_id']] = $role;
            }
        }
        return $this->registryCacheBySystemId;
    }

    public function getRoleBySystemId($systemId)
    {
        $registry = $this->getRegistryBySystemId();
        if (isset($registry[$systemId])) {
            return $registry[$systemId];
        }
        return;
    }

    public function getRoleById($id)
    {
        $registry = $this->getRegistry();
        if (isset($registry[$id])) {
            return $registry[$id];
        }
        return;
    }
}
