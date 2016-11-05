<?php
abstract class PwFilterAction{
	
	abstract function createData($data);
	
	abstract function check($str);
	
	abstract function match($str);
	
	abstract function replace($str);
}