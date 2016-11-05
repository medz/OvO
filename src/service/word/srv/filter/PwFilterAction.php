<?php

abstract class PwFilterAction
{
    abstract public function createData($data);

    abstract public function check($str);

    abstract public function match($str);

    abstract public function replace($str);
}
