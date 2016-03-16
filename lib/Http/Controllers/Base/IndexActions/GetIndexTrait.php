<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http\Controllers\Base\IndexActions;

use Illuminate\Http\Request;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

trait GetIndexTrait
{
    /**
     * Get a list of objects
     *
     * @param  Request  $request Request object
     * @return ResponseInterface Response for request
     */
    public function get(Request $request, Fractal $fractal, FractalCollection $collection)
    {
        $transformer = $this->getTransformer();
        $includes = $request->input('include', '');
        $fractal->parseIncludes($includes);
        $resource = $collection->setTransformer($transformer)->setResourceKey($this->getResourceKey());
        $items = $this->getRepository();
        $paginator = $items->paginate([], $transformer->getSafeEagerLoad($fractal->getRequestedIncludes()));
        $itemCollection = $paginator->getCollection();
        $resource->setData($itemCollection);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        return $this->respondWithCollection($fractal->createData($resource));
    }
}
?>