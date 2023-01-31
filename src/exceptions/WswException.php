<?php
/**
 * Author:Shaun·Yang
 * Date:2023/1/30
 * Time:下午6:00
 * Description:异常类
 */

namespace wsw\exceptions;

use Throwable;

class WswException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}