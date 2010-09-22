<?php

class ModelAdminMenuPanel extends ModelAdminPanel {
	
	public $panel_title 	= 'Menu';
	
	function init(){
		Requirements::javascript($this->stat('module_folder').'/javascript/ModelAdminMenuPanel.js');	
	}
	
	function Menu(){
		$items = new DataObjectSet();
		foreach($this->getManagedModels() as $key => $object){
			if(class_exists($object)){
				$sgn = singleton($object);
				$columns = $sgn->stat('summary_fields');
				$search = '?';
				if($columns){
					foreach($columns as $k => $v){
						$search	.= 'ResultAssembly['.$k.']='.$k.'&';
					}
				} else {
					$field = $sgn->hasDatabaseField('Title') ? 'Title' : 'Name';
					$search	.= 'ResultAssembly['.$field.']='.$field;
				}
				$items->push(new ArrayData(array(
					'Class'		=> $object,
					'Link'		=> Controller::join_links("admin",$this->url_segment,$object,"SearchForm") . $search,
					'Title'		=> $sgn->plural_name()
				)));
			}
		}
		return $items;	
	}
}