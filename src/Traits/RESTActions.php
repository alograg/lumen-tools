<?php
namespace Alograg\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Trait RESTActions
 *
 * @package Alograg\Traits
 */
trait RESTActions
{

    /**
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function all()
    {
        /** @var Model $m */
        $m = get_called_class()::MODEL;

        return $this->respond(Response::HTTP_OK, $m::all());
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function get($id)
    {
        $m = get_called_class()::MODEL;
        $model = $m::find($id);
        if (is_null($model)) {
            return $this->respond(Response::HTTP_NOT_FOUND);
        }

        return $this->respond(Response::HTTP_OK, $model);
    }

    /**
     * @param  Request  $request
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function add(Request $request)
    {
        $m = get_called_class()::MODEL;
        $this->validate($request, $m::$rules);

        return $this->respond(Response::HTTP_CREATED, $m::create($request->all()));
    }

    /**
     * @param  Request  $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function put(Request $request, $id)
    {
        $m = get_called_class()::MODEL;
        $this->validate($request, $m::$rules);
        $model = $m::find($id);
        if (is_null($model)) {
            return $this->respond(Response::HTTP_NOT_FOUND);
        }
        $model->update($request->all());

        return $this->respond(Response::HTTP_OK, $model);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function remove($id)
    {
        $m = get_called_class()::MODEL;
        if (is_null($m::find($id))) {
            return $this->respond(Response::HTTP_NOT_FOUND);
        }
        $m::destroy($id);

        return $this->respond(Response::HTTP_NO_CONTENT);
    }

    /**
     * @param $status
     * @param  array  $data
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function respond($status, $data = [])
    {
        if ($status == Response::HTTP_NO_CONTENT) {
            return response(null, Response::HTTP_NO_CONTENT);
        }
        if ($status == Response::HTTP_NOT_FOUND) {
            return response(['message' => 'resource not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($data, $status);
    }

}
