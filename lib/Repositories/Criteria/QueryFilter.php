<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Repositories\Criteria;

use CHMS\Common\Http\Transformers\BaseTransformer;
use Illuminate\Database\Eloquent\Builder as Query;
use CHMS\Common\Repositories\BaseRepository;
use CHMS\Common\Repositories\BaseCriteria;
use Illuminate\Database\Eloquent\Model;

class QueryFilter extends BaseCriteria
{
    private $filter;
    public function __construct($filter = [], Model $model, BaseTransformer $transformer)
    {
        $this->filter = $transformer->limitFilter($model, $filter);
    }

    public function apply(Query $query, BaseRepository $repository)
    {
        $meta = false;
        if (isset($this->filter['meta'])) {
            $meta = $this->filter['meta'];
            unset($this->filter['meta']);
        }
        if (!empty($this->filter)) {
            $query->where($this->filter);
        }
        if (!empty($meta)) {
            $table = $query->getModel()->getTable();
            $query->join('meta', $table . '.id', '=', 'meta.object_id');
            $metaQuery = $query->getQuery()->newQuery();
            foreach ($meta as $k => $v) {
                $metaQuery->orWhere(['meta.key' => $k, 'meta.value' => $v]);
            }
            $query->addNestedWhereQuery($metaQuery);
            $query->groupBy($table.'.id');
            $results = $query->get();
        }
    }
}
