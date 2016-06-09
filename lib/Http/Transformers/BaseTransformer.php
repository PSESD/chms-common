<?php
/**
 * Clock Hour Management System 
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http\Transformers;

use League\Fractal;
use Illuminate\Database\Eloquent\Model;
use CHMS\Common\Contracts\Acl as AclContract;

abstract class BaseTransformer
    extends Fractal\TransformerAbstract
{
    protected $eagerLoadIncludes = [];
    private $fieldAclCache = [];

    public function getSafeEagerLoad($includes = [])
    {
        return array_intersect($this->eagerLoadIncludes, array_values($includes));
    }

    public function transform(Model $model)
    {
        $attributes = $model->toArray();
        $allowedAclFields = $this->getAllowedAclFields($model);
        foreach ($attributes as $fieldName => $value) {
            if (!in_array($fieldName, $allowedAclFields)) {
                unset($attributes[$fieldName]);
            }
        }
        $attributes['meta'] = [];
        if (in_array('meta.*', $allowedAclFields)) {
            $attributes['meta'] = $model->meta;
        } else {
            $meta = $model->meta;
            foreach ($model->metaFields() as $key) {
                if (isset($meta[$key])) {
                    $attributes['meta'][$key] = $meta[$key];
                }
            }
        }
        return $attributes;
    }

    public function limitFilter(Model $model, array $filter)
    {
        $allowedAclFields = $this->getAllowedAclFields($model);
        foreach ($filter as $fieldName => $value) {
            if (!in_array($fieldName, $allowedAclFields)) {
                unset($filter[$fieldName]);
            }
        }
        if (isset($filter['meta']) && !in_array('meta.*', $allowedAclFields)) {
            foreach ($filter['meta'] as $key => $value) {
                if (!in_array('meta.' . $key, $allowedAclFields)) {
                    unset($filter['meta'][$key]);
                }
            }
        }
        return $filter;
    }

    public function getMeta(Model $model)
    {
        return [];
    }

    protected function getAllowedAclFields(Model $model, $privilege = 'read')
    {
        $acl = app(AclContract::class);
        $modelClass = get_class($model);
        $cacheKey = sha1($privilege . $acl->getContextId() . $modelClass);
        if (!isset($this->fieldAclCache[$cacheKey])) {
            $this->fieldAclCache[$cacheKey] = [];
            $this->fieldAclCache[$cacheKey][] = 'meta';
            foreach ($model->getTableColumns() as $fieldName) {
                if ($acl->isAllowedField($modelClass, $fieldName, $privilege)) {
                    $this->fieldAclCache[$cacheKey][] = $fieldName;
                }
            }
            foreach ($model->metaFields() as $fieldName) {
                $metaFieldName = 'meta.' . $fieldName;
                if ($acl->isAllowedField($modelClass, $metaFieldName, $privilege)) {
                    $this->fieldAclCache[$cacheKey][] = $metaFieldName;
                }
            }
        }
        return $this->fieldAclCache[$cacheKey];
    }
}
