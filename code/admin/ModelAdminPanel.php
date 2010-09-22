<?php

abstract class ModelAdminPanel extends ViewableData {	
	
	public $panel_title ;
	
	public $panel_state = 'open';
	
	public $managed_models 	= array();
	
	public $parentController;
	
	public $url_segment ;
	
	static $module_folder ;
	
	public $Name ;
	public $Content ;
	public $enabled = true;	
	
	function __construct($title = '' , $state = 'open', $models = array()){
		$this->panel_title   	= $title;
		$this->panel_state   	= $state;
		$this->managed_models   = $models;
		$this->parentController = Controller::curr();
		$this->url_segment 		= $this->parentController->getUrlSegment();
		self::$module_folder	= $this->parentController->stat('module_folder');
	}
	
	function init(){
		
	}
	
	function setManagedModels($models = array()){
		$this->managed_models = $models;
	}
	
	function getManagedModels(){
		return $this->managed_models ? $this->managed_models : $this->parentController->getManagedModels();
	}
	
	function setEnabled($bool){
		$this->enabled = $bool;
	}
	
	function Enabled(){
		return $this->enabled; 	
	}
	
	function setName($name = ''){
		$this->Name = $name;
	}
	
	function canView($bool = true){
		$this->enabled = $bool;	
	}
	
	function Title(){
		return $this->panel_title ? $this->panel_title : get_class($this);	
	}
	
	function Content(){
		return $this->Content;
	}
	
	function setContent($content = ''){
		$this->Content = $content;	
	}
	
	function PanelState(){
		return $this->panel_state;	
	}
	
	function forTemplate() {
		return $this->renderWith(array(get_class($this),get_parent_class($this),'ModelAdminPanel'));
	}
}
