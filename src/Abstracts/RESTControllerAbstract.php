<?php

namespace Alograg\Abstracts;

use Alograg\Interfaces\RestControllerInterface;
use Alograg\Traits\RESTActions;
use Laravel\Lumen\Routing\Controller as LumenController;

/**
 * Class RESTControllerAbstract
 * @package Alograg\Abstracts
 */
abstract class RESTControllerAbstract extends LumenController implements RestControllerInterface
{
    use RESTActions;

    /**
     *
     */
    const MODEL = null;
}
