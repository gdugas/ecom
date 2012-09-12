<?php 

interface IEcomDelivery {
	
	public function getModuleName ();
	public function getModuleVersion ();
	
	public function applyRule ($quantity, $weight=NULL);
	
	public function getDeliveryCost ();
	public function getDeliveryMode ();
	public function getDeliveryTax ();
	
}
