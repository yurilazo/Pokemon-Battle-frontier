<?php

class CustomHelper{
	
	public static function indexarray($array, $field) {

		if(!is_array($array)) return FALSE;

		$array_indexed = array();

		foreach ($array as $row) {
			$array_indexed[$row[$field]][] = $row;
		}
		return $array_indexed;
	}
}