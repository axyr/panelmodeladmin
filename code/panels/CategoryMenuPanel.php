<?php
/**
 * Shows a nested menu list with child items.
 * The first level DataObjects will be directy linked to the detailform.
 * This is usefull when one DataObject is a category like DataObjects, which holds one or more child DataObjects
 * A root item will hold all the first DataObjects in a regular TableListField.
 */
class CategoryMenuPanel extends ModelAdminMenuPanel{
	
	function __construct($title = '' , $state = 'open', $models = array(), $nesting = array()){
		
		parent::__construct($title, $state, $models);
	}
	
	function init(){
		Requirements::javascript($this->stat('module_folder').'/javascript/ModelAdminMenuPanel.js');	
	}
	
	function Menu(){
		$rootitems = new DataObjectSet();
		
		$rootObjectItems = NULL;
		$models = $this->getManagedModels();
		//check if there are childitems, otherwise just return a list
		if(array_key_exists(0,$models)){
			$models[$models[0]] = array();
		}
		foreach($models as $object => $childs){
			if(class_exists($object)){ 
				
				if($rootObjects = DataObject::get($object)){
					
					$rootObjectItems = new DataObjectSet();
					foreach($rootObjects as $rootObjectItem){
						
						$childItems = new DataObjectSet();
						foreach($childs as $key => $child){
							if(class_exists($child)){
								$childItem = singleton($child);
								$childSearch = $this->getResultAssembly($childItem);
								$link = Controller::join_links("admin",$this->url_segment,$child,"SearchForm") . $childSearch
										.$rootObjectItem->ClassName.'ID='.$rootObjectItem->ID;
								$childItems->push(new ArrayData(array(
									'Class'		 => $child,
									'Link'		 => $link,
									'Title'		 => $childItem->plural_name()
								)));
							}					
						}
						
						$rootObjectItems->push(new ArrayData(array(
							'Class'		=> (($childs) ? $object . ' folder' : $object),
							'Link'		=> Controller::join_links("admin",$this->url_segment,$object,$rootObjectItem->ID,"edit"),
							'Title'		=> $rootObjectItem->Title,
							'Children'	=> $childItems
						)));
					}
				}
				
				$rootItem = singleton($object);
				$rootSearch = $this->getResultAssembly($rootItem);
				$rootitems->push(new ArrayData(array(
					'Class'		=> $object,
					'Link'		=> Controller::join_links("admin",$this->url_segment,$object,"SearchForm") . $rootSearch,
					'Title'		=> $rootItem->plural_name(),
					'Children'	=> $rootObjectItems
				)));
			}
		}
		return $rootitems;	
	}
	
	function getResultAssembly($sgn){
		$columns = $sgn->stat('summary_fields');
		$search = '?';
		if($columns){
			foreach($columns as $k => $v){
				$search	.= 'ResultAssembly['.$k.']='.$k.'&';
			}
		} else {
			$field = $sgn->hasDatabaseField('Title') ? 'Title' : 'Name';
			$search	.= 'ResultAssembly['.$field.']='.$field.'&';
		}
		return $search;
	}
}