<?php

class SearchObj extends FactoryObj
{    
    public function __construct($uniqueid=null) {
        parent::__construct("id","searches",$uniqueid);
    }
    
}
