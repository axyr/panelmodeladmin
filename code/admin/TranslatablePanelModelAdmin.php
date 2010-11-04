<?php

abstract class TranslatablePanelModelAdmin extends PanelModelAdmin{
	
	/**
	 * @var String $Locale
	 */
	public $Locale = NULL;
	
	public static $collection_controller_class	= "TranslatablePanelModelAdmin_CollectionController";
	public static $record_controller_class 		= "TranslatablePanelModelAdmin_RecordController";
	
	
	function init(){
		parent::init();
		Requirements::block(CMS_DIR . '/javascript/LangSelector.js');
		
		Requirements::customScript("SiteTreeHandlers.controller_url = '" . $this->Link() . "';");
		Requirements::block(CMS_DIR . '/javascript/TranslationTab.js');
		Requirements::block(CMS_DIR . '/javascript/LangSelector.js');
		Requirements::javascript('panelmodeladmin/javascript/TranslatablePanelModelAdmin.js');
		
		if($this->getRequest()->requestVar("Locale")) {
			$this->Locale = $this->getRequest()->requestVar("Locale");
		} elseif($this->getRequest()->requestVar("locale")) {
			$this->Locale = $this->getRequest()->requestVar("locale");
		} elseif(Session::get('Locale')){
			$this->Locale = Session::get('Locale');
		} else {
			$this->Locale = Translatable::default_locale();
		}
		Session::set('Locale', $this->Locale);
		Translatable::set_current_locale($this->Locale);
	}
	
	function Panels(){
		$panels = parent::Panels();
		$langPanel = new LangSelectorPanel('Languages', 'open');
		$langPanel->setName('Languages');
		$langPanel->init();
		$panels->insertFirst(new ArrayData(array(
			'Name'		=> 'Languages',
			'Panel'		=> $langPanel,
			'Title'		=> 'Languages',
			'State'		=> 'open'
		)));
		
		return $panels;
	}
	
	function getEditForm(){
		if($this->getCurrentRecord()){
			return $this->getCurrentRecord()->renderWith('ModelAdmin_right');
		}elseif($this->getCurrentModel()){
			return $this->bindModelController($this->getCurrentModel())->ResultsForm(array());
		}
	}	
	
	function getCurrentRecord(){
		if($currentRecord = Session::get('currentRecord')){
			$parts = explode('.',$currentRecord);
			if(isset($parts[0]) && isset($parts[1])){
				$recordController = $this->getRecordControllerClass($parts[0]);
				return new $recordController($this->bindModelController($parts[0], array()),array(), $parts[1]);
			}
		}		
	}
	
	function getCurrentModel(){
		if($currentRecord = Session::get('getCurrentModel')){
			return $currentRecord;
		}else{
			Session::set('getCurrentModel', $this->getDefaultModel());
			return $this->getDefaultModel();
		}
	}
}

class TranslatablePanelModelAdmin_CollectionController extends PanelModelAdmin_CollectionController {
	
	function __construct($parent, $model) {
		parent::__construct($parent, $model);
		Session::set('getCurrentModel', $model);
		Session::clear('currentRecord');
	}
	
	function getSearchQuery($searchCriteria) {	
		$context = singleton($this->modelClass)->getDefaultSearchContext();
		$context->addFilter(new ExactMatchFilter('Locale', Translatable::get_current_locale()));
		return $context->getQuery($searchCriteria);
	}
	
	function ResultsForm($searchCriteria) {
		$form = parent::ResultsForm($searchCriteria);
		//$form->Fields()->insertFirst($this->LangSelector());
		$form->Fields()->push(new HiddenField('Locale', 'Locale', Translatable::get_current_locale()));
		return $form;
	}
	
	function AddForm() {
		$form = parent::AddForm();
		$form->Fields()->push(new HiddenField('Locale', 'Locale', Translatable::get_current_locale()));
		return $form;
	}
	
	function LangSelector() {
		if(Object::has_extension('TourCategory', 'Translatable')) {
			$member = Member::currentUser(); //check to see if the current user can switch langs or not
			if(Permission::checkMember($member, 'VIEW_LANGS')) {
				$dropdown = new LanguageDropdownField(
					'LangSelector', 
					'Language', 
					array(), 
					'TourCategory', 
					'Locale-English'
				);
				$dropdown->setValue(Translatable::get_current_locale());
				return $dropdown;
	        }
        
	        //user doesn't have permission to switch langs so just show a string displaying current language
	        return i18n::get_locale_name( Translatable::get_current_locale() );
		}
    }
}

class TranslatablePanelModelAdmin_RecordController extends PanelModelAdmin_RecordController{
	
	static $allowed_actions = array('createtranslation');
	function __construct($parentController, $request, $recordID = null) {
		
		$modelName = $parentController->getModelClass();
		$sng = singleton($modelName);
		if(!$sng->hasExtension('Translatable')) {
			Translatable::disable_locale_filter();
		}
		parent::__construct($parentController, $request, $recordID);
		$this->setCurrentRecord();
	}
	
	function setCurrentRecord(){
		Session::set('currentRecord', $this->currentRecord->ClassName.'.'.$this->currentRecord->ID);	
	}
	
	// redundant
	function getCurrentRecord(){
		if($currentRecord = Session::get('currentRecord')){
			$parts = explode('.',$currentRecord);
			$this->currentRecord = DataObject::get_by_id($parts[0], $parts[1]);
		}
	}
	
	function EditForm() {
		$form = parent::EditForm();

		if($this->currentRecord->hasExtension('Translatable')) {
			$form->Fields()->push(new HiddenField('Locale', 'Locale', Translatable::get_current_locale()));
			// TODO Exclude languages which are already translated into 
			$dropdown = new LanguageDropdownField(
				'NewTransLang', 
				_t('TranslatableModelAdmin.LANGDROPDOWNLABEL', 'Language'), 
				array(), 
				$this->currentRecord->class, 
				'Locale-English'
			);
			$action = new InlineFormAction(
				'createtranslation', 
				_t('TranslatableModelAdmin.CREATETRANSBUTTON', 
				"Create translation")
			);
			$header = new HeaderField(
				'ExistingTransHeader', 
				_t('TranslatableModelAdmin.EXISTINGTRANSTABLE', 'Existing Translations'),
				4
			);
			// TODO Exclude the current language
			$table = new TableListField(
				'Translations',
				$this->currentRecord->class
			);
			$table->setPermissions(array('show'));
			if(!$sourceItems = $this->currentRecord->getTranslations()){
				$sourceItems = new DataObjectSet();
			}
			$table->setCustomSourceItems($sourceItems);
			$action->includeDefaultJS = false;
			if($form->Fields()->hasTabSet()) {
				$form->Fields()->findOrMakeTab(
					'Root.Translations', 
					_t("TranslatableModelAdmin.TRANSLATIONSTAB", "Translations")
				);
				$form->Fields()->addFieldToTab('Root.Translations', $header);
				$form->Fields()->addFieldToTab('Root.Translations', $table);
				$form->Fields()->addFieldToTab('Root.Translations', $dropdown);
				$form->Fields()->addFieldToTab('Root.Translations', $action);
			} else {
				$form->Fields()->push(new HeaderField(
					'TranslationsHeader',
					_t("TranslatableModelAdmin.TRANSLATIONSTAB", "Translations")
				));
				$form->Fields()->push($header);
				$form->Fields()->push($table);
				$form->Fields()->push($dropdown);
				$form->Fields()->push($action);
			}
			// TODO This is hacky, but necessary to get proper identifiers
			$form->Fields()->setForm($form);
			
		}
		
		return $form;
	}
	
	/**
	 * Create a new translation from an existing item, switch to this language and reload the tree.
	 */
	function createtranslation($data, $form, $edit) {
			if($this->currentRecord->hasExtension('Translatable')) {
			$langCode = Convert::raw2sql($_REQUEST['newlang']);
	
			Translatable::set_current_locale($langCode);
			$translatedRecord = $this->currentRecord->createTranslation($langCode);
	
			$this->currentRecord = $translatedRecord;
			
			// TODO Return current language as GET parameter
			return $this->edit(null);
		}
	}
}