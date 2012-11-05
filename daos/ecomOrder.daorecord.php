<?php 

jIncluder::inc(new jSelectorDao('ecom~order_item', ''));
jClasses::inc('ecom~ecomOrder');

abstract class ecomOrderDaoRecord extends cDaoUserRecord_ecom_Jx_order_Jx {

    const STATUS_WAITING = 'waiting';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_SENT = 'sent';
    const STATUS_CANCELED = 'canceled';

    private $_dao = 'ecom~order';
    private $_daobilling = 'ecom~billing';
    private $_record = NULL;
    
    function afterSave() {
    }
    
    function beforeDelete() {
        jDao::get('ecom~order_item')->deleteByOrder($this->id);
    }
    
    function beforeSave() {
        $this->price = 0;
        $this->price_full = 0;
        foreach ($this->items() as $item) {
            $this->price += $item->price * $item->quantity;
            $this->price_full += $item->getPriceFull() * $item->quantity;
        }
    }
    
    public function setStatus ($status=NULL) {
        // Status param validation
        if (    $status != self::STATUS_CANCELED &&
                $status != self::STATUS_ONGOING &&
                $status != self::STATUS_SENT &&
                $status != self::STATUS_WAITING) {
            return False;
        }

        // Status update validation
        if ($this->status == self::STATUS_SENT || $this->status == self::STATUS_CANCELED) {
            return False;
            	
        } elseif ($status == self::STATUS_WAITING && $this->status == self::STATUS_ONGOING) {
            return False;
            	
        } else {
            switch ($status) {
                case self::STATUS_ONGOING:	$this->date_ongoing = date('Y-m-d H:i:s'); break;
                case self::STATUS_SENT:		$this->date_sent = date('Y-m-d H:i:s'); break;
                case self::STATUS_CANCELED:	$this->date_canceled = date('Y-m-d H:i:s'); break;
                default: break;
            }
            $this->status = $status;
            $this->save();
            return True;
        }
    }
    
    function items() {
        return jDao::get('ecom~order_item')->findByOrder($this->id);
    }
}
