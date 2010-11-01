<?php

class TranslatablePanelModelAdmin extends LeftAndMainDecorator{
	
	function init(){
		parent::init();
		Requirements::block(CMS_DIR . '/javascript/LangSelector.js');
		if($this->owner->getRequest()->requestVar("Locale")) {
				$this->owner->Locale = $this->owner->getRequest()->requestVar("Locale");
		} elseif($this->owner->getRequest()->requestVar("locale")) {
				$this->owner->Locale = $this->owner->getRequest()->requestVar("locale");
		} else {
				$this->owner->Locale = Translatable::default_locale();
		}
		
		Translatable::set_current_locale($this->owner->Locale);
	}
	
}

class TranslatablePanelModelAdmin_CollectionController extends Extension {
	
	function search($request, $form) {
		exit();
	}
	function getSearchQuery($searchCriteria) {
		var_dump($this->modelClass);var_dump($this->owner->Locale);
		exit();
		$context = singleton($this->modelClass)->getDefaultSearchContext();
		$context->addFilter(new ExactMatchFilter('Locale', $this->parentController->Locale));
		return $context->getQuery($searchCriteria);
	}
	
	function updatePanels($panels){
		$langPanel = new LangSelectorPanel('Languages', 'open');
		$langPanel->setName('Languages');
		$langPanel->init();
		$panels->insertFirst(new ArrayData(array(
			'Name'		=> 'Languages',
			'Panel'		=> $langPanel,
			'Title'		=> 'Languages',
			'State'		=> 'open'
		)));
	}
	
}