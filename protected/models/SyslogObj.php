<?php

class SyslogObj extends FactoryObj
{
	public function __construct($logid=null) {
		parent::__construct("logid","syslog",$logid);
	}
	
	public function pre_save() {
		if(!isset($this->date_logged) or $this->date_logged == "") {
			$this->date_logged = date("Y-m-d H:i:s");
		}
	}
}
