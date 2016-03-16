<?php
/**
 * Clock Hour Management System - Hub
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Hub\Http\Input\Filters;

use Illuminate\Database\Eloquent\Model;
use CHMS\Common\Contracts\Acl as AclContract;
use CHMS\Common\Contracts\Filter as FilterContract;
use CHMS\Common\Exceptions\InvalidInputException;

abstract class BaseFilter
    implements FilterContract
{
    private $fieldAclCache = [];

    /**
     * @inheritdoc
     */
    public function filter(Model $model, array $input, $scenario, $throwException = true)
    {
        $allowedAclFields = $this->getAllowedAclFields($model, $scenario);
        foreach ($input as $fieldName => $value) {
            if (!in_array($fieldName, $allowedAclFields)) {
                if ($throwException) {
                    throw new InvalidInputException("Invalid field '{$fieldName}'");
                }
                unset($input[$fieldName]);
            }
        }
        return $input;
    }

    /**
     * Get and cache the field rules
     * @param  Model  $model
     * @param  string $scenario
     * @return array
     */
    protected function getAllowedAclFields(Model $model, $scenario)
    {
        $privilege = ['set', 'set_on_' . $scenario];
        $acl = app(AclContract::class);
        $modelClass = get_class($model);
        $cacheKey = sha1(json_encode($privilege) . $acl->getContextId() . $modelClass);
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
