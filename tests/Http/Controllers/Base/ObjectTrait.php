<?php
namespace CHMSTests\Common\Http\Controllers\Base;

use CHMSTests\Common\Stubs\GenericModel;

trait ObjectTrait 
{

    protected function getFactory()
    {
        $model = $this->getRepository()->model();
        $modelClass = get_class($model);
        return factory($modelClass);
    }

    protected function createOne()
    {
        return $this->getFactory()->create();
    }

    protected function getGeneric($legitify = true)
    {
        $modelClass = get_class($this->getRepository()->model());
        $ref = new \ReflectionClass($modelClass);
        $generic = factory(GenericModel::class, $ref->getShortName())->make()->toArray();
        if ($legitify) {
            $modelObject = factory($modelClass)->make();
            $model = $generic;
            foreach ($generic as $key => $value) {
                if (isset($modelObject[$key])) {
                    $generic[$key] = $modelObject[$key];
                }
            }
        }
        return $generic;
    }

    protected function notExpectedAttributes()
    {
        return [];
    }

    protected function getExpected($attributes)
    {
        foreach ($this->notExpectedAttributes() as $attribute) {
            unset($attributes[$attribute]);
        }
        return $attributes;
    }
}