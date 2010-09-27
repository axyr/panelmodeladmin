<?php

class ModelAdminSearchPanel extends ModelAdminPanel{
	
	public $panel_title 		= 'Search';	
	
	static $hide_column_select	= false;
	
	function init(){
		Requirements::javascript($this->stat('module_folder').'/javascript/ModelAdminSearchPanel.js');	
	}
	
	function ModelForms(){
		$forms  = new DataObjectSet();
		foreach($this->getManagedModels() as $key => $object){ 
			$forms->push(new ArrayData(array (
				'Title'     	=> singleton($object)->i18n_singular_name(),
				'ClassName' 	=> $object,
				'FormID' 		=> $this->Name,
				'SearchForm'	=> $this->SearchForm($object)
			)));
		}
		return $forms;
	}
	
	function SearchForm($object){
		$collectionControllerClass = $this->parentController->getCollectionControllerClass($object);
		$collectionController = new $collectionControllerClass($this->parentController, $object);
		return $collectionController->SearchForm();
	}
	
}