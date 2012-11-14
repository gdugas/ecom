<?php 

class ecomDaoListener extends jEventListener {
	
	function onDaoInsertAfter ($e) {
	    // Calling afterSave record method
	    switch($e->getParam('dao')) {
	        case 'ecom~cart':
	        case 'ecom~cart_item':
	        case 'ecom~order':
	        case 'ecom~order_item':
	            $record = $e->getParam('record');
	            $record->afterSave();
	            break;
	        
	        default: break;
	    }
	}
	
	function onDaoInsertBefore ($e) {
		$dao = $e->getParam('dao');
		$record = $e->getParam('record');
		
	    // Calling beforeSave record method (cart and cartItem)
	    switch($dao) {
	        case 'ecom~cart':
	        case 'ecom~cart_item':
	        case 'ecom~order':
	        case 'ecom~order_item':
	            $record->beforeSave();
	            break;
	        
	        default: break;
	    }
	    
		if ($dao == 'ecom~account') {
			if (! $record->login) {
				$record->login = $record->email;
				$record->reference = 'CL'.rand(10000,99999);
			}
		}
	}
	
	function onDaoUpdateAfter ($e) {
	    // Calling afterSave record method
		switch($e->getParam('dao')) {
	        case 'ecom~cart':
	        case 'ecom~cart_item':
	        case 'ecom~order':
	        case 'ecom~order_item':
	            $record = $e->getParam('record');
	            $record->afterSave();
	            break;
	        
	        default: break;
	    }
	}
	
	function onDaoUpdateBefore ($e) {
        // Calling beforeSave record method (cart and cartItem)
	    switch($e->getParam('dao')) {
	        case 'ecom~cart':
	        case 'ecom~cart_item':
	        case 'ecom~order':
	        case 'ecom~order_item':
	            $record = $e->getParam('record');
	            $record->beforeSave();
	            break;
	        
	        default: break;
	    }
    }
    
	// Check if deleted object is in cart
	function onDaoDeleteBefore ($e) {
	    $dao = $e->getParam('dao');
		switch($dao) {
	        case 'ecom~cart':
	        case 'ecom~cart_item':
	        case 'ecom~order':
	        case 'ecom~order_item':
	            $keys = $e->getParam('keys');
	            $record = jDao::get($dao)->get($keys['id']);
    	        if ($record) {
        	        $record->beforeDelete();
    	        }
    	        break;
	    }
	}
	
	function onDaoDeleteByBefore ($e) {
	    $dao = $e->getParam('dao');
	    switch($dao) {
	        case 'ecom~cart':
	        case 'ecom~cart_item':
	        case 'ecom~order':
	        case 'ecom~order_item':
	            $records = jDao::get($dao)->findBy($e->getParam('criterias'));
    	        foreach ($records as $record) {
        	        $record->beforeDelete();
    	        }
    	        break;
	    }
	}
	
	function onDaoSpecificDeleteBefore ($e) {
	    $dao = $e->getParam('dao');
	    $method = $e->getParam('method');
		if ($dao == 'ecom~cart_item' && $method == 'deleteByCart') {
            $record = jDao::get($dao)->get($e->getParam('params'));
    	    if ($record) {
    	        $record->beforeDelete();
    	    }
	    }
	}
}
