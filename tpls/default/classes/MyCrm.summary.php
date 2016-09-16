<?php

final class MyCrm_summary extends MyCrm {
	public function __construct(&$Index, &$Config) {
		parent::__construct($Index,$Config);
		$this->id_custom = $this->name;
	}
	
	protected function find() {
		
		

	}

	protected function save() {
		
	}
	
	
	public function window() {
		
	}
	
	protected function one($form = false) {
		
	}
	
}

