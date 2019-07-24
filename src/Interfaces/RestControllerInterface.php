<?php
namespace Alograg\Interfaces;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Interface RestControllerInterface
 *
 * @package Alograg\Interfaces
 */
interface RestControllerInterface
{
    /**
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function all();

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function get($id);

    /**
     * @param  Request  $request
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function add(Request $request);

    /**
     * @param  Request  $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function put(Request $request, $id);

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function remove($id);

}
