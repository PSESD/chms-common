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
        return $attributes;
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
            foreach ($model->getTableColumns() as $fieldName) {
                if ($acl->isAllowedField($modelClass, $fieldName, $privilege)) {
                    $this->fieldAclCache[$cacheKey][] = $fieldName;
                }
            }
        }
        return $this->fieldAclCache[$cacheKey];
    }
}
