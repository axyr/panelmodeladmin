<?php

class LangSelectorPanel extends ModelAdminPanel {
	
	function init(){
	}
	
	function LangSelector() {
		return TranslatablePanelModelAdmin_CollectionController::LangSelector();
	}
	
}