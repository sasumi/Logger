<?php

namespace FSLogger;

/**
 * var_export in minimal format
 * @param $var
 * @param bool $return
 * @return mixed|string
 */
function var_export_min($var, $return = false){
	if(is_array($var)){
		$toImplode = array();
		foreach($var as $key => $value){
			$toImplode[] = var_export($key, true).'=>'.var_export_min($value, true);
		}
		$code = 'array('.implode(',', $toImplode).')';
		if($return){
			return $code;
		}else echo $code;
	}else{
		return var_export($var, $return);
	}
}