<?php

interface IEcomPayment {
	
	function getModuleName ();
	function getModuleVersion ();
	function getPaymentMode ();
	
	function sendRequest (array $params);
	function getResponse (array $params);

}
