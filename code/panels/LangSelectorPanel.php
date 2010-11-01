<?php

class LangSelectorPanel extends ModelAdminPanel {
	
	function init(){
		var_dump($this->parentController->currentModel());
		exit();
	}
	
	function LangSelector() {
			
			$member = Member::currentUser(); //check to see if the current user can switch langs or not
			if(Permission::checkMember($member, 'VIEW_LANGS')) {
				$dropdown = new LanguageDropdownField(
					'LangSelector', 
					'Language', 
					array(), 
					$this->modelClass, 
					'Locale-English'
				);
				$dropdown->setValue(Translatable::get_current_locale());
				return $dropdown;
			}
		
			//user doesn't have permission to switch langs so just show a string displaying current language
			return i18n::get_locale_name( Translatable::get_current_locale() );
		
	}
	
}