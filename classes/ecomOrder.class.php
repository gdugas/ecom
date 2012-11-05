<?php

class ecomOrder {
	public static function genRef ($prefix='C') {
		return uniqid($prefix);
	}
}
