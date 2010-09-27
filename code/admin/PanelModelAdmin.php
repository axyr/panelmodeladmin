<?php
/**
 * PanelModelAdmin generates a panel based ModelAdmin
 * By default it replaces the standard ModelAdmin left sidebar with panels.
 * The models are selected with a menu instead of the 'Search' method of ModelAdmin. The Model searchforms are placed in a seperate panel.
 *
 * You can still enable the ModelAdmins default sidebar with 
 * <code>
 * PanelModelAdmin::enableDefaultAdminPanel(true);
 * </code>
 *
 * Extending PanelModelAdmin works the same as ModelAdmin.
 * You can add custom sidebar Panels by extending ModelAdminPanel and 
 * add them or remove them with self::addPanels, self::addPanel, self::removePanels or self::removePanelByName
 * When now panels are set, a default set of Search and Menu Panel will be added to the sidebar.
 *
 * @uses ModelAdmin, ModelAdminPanel
 * 
 * @package PanelModelAdmin
 * @author Martijn van Nieuwenhoven
 */
 
abstract class PanelModelAdmin extends ModelAdmin  {
	
	/**
	 * List of all sidebar panels for admin
	 */
	static $panels 			= array();
	
	/**
	 * Override default ModelAdmin_CollectionController to create custom ResultForms.
	 * This will make it posible to use other Results appearance then the default TableListField
	 * and set this per DataObject in function ResultsTable
	 */
	public static $collection_controller_class = "PanelModelAdmin_CollectionController";
	
	public static $record_controller_class = "PanelModelAdmin_RecordController"; 
	
	/**
	 * Just for dev purposes, so I can change the name later eventualy
	 */
	static $module_folder = 'panelmodeladmin'; 
	
	/**
	 * Show the default ModelAdmin LeftPanel in a seperate tab.
	 * Will be positioned as first panel.
	 * @param boolean
	 */
	static $enable_default_modeladmin_panel = false;
	
	/**
	 * If you want to disable the default panels to show up when no Panels are set.
	 * @param boolean
	 */
	static $disable_default_panels = false;
	
	
	static $default_model = '';
	/**
	 * Adds a single Panel to the sidebar
	 *
	 * <code>
	 * $this->addPanel('MenuPanel4', new ModelAdminSearchPanel('Search', 'closed', $this->getManagedModels(), 'MenuPanel');
	 * </code>
	 *
	 * @param string $name a unique name for the Panel
	 * @param string $panel the Panel Object
	 * @param string $before the PanelName to insert the newly added Panel before
	 */
	function addPanel($name, $panel, $before = NULL){
		if($before){
			$currentPanels	= self::$panels;
			$offset 		= array_search($before,array_keys($currentPanels));
			self::$panels	= array_slice($currentPanels, 0, $offset, true) 
						 	+ array($name => $panel) 
						  	+ array_slice($currentPanels, $offset, NULL, true);
		} else {
			self::$panels[$name] = $panel;
		}
	}
	
	/**
	 * Adds multiple Panels to the sidebar
	 *
	 * <code>
	 *	$this->addPanels(array(
	 *		'SearchPanel' 	=> new ModelAdminSearchPanel('Search', 'closed', array('Product','Category')),
	 *		'MenuPanel' 	=> new ModelAdminMenuPanel('Menu', 'open', array('Product','Category'))
	 *	));
	 * </code>
	 *
	 * @param array $panels an array of Panels
	 */
	function addPanels($panels = array()){
		foreach($panels as $name => $panel){
			$this->addPanel($name, $panel);
		}
	}
	
	/**
	 * Removes a Panel by its unique name
	 * @param string $name the Panel name to remove
	 */
	function removePanelByName($name = NULL){
		unset(self::$panels[$name]);
	}
	
	/**
	 * Removes an array of Panels
	 * @param array $names an array of Panel names to remove
	 */
	function removePanels($names = array()){
		foreach($names as $name){
			self::removePanelByName($name);
		}
	}
	
	/**
	 * Show the default ModelAdmin leftbar in the first Panel
	 * @param boolean 
	 */
	function enableDefaultAdminPanel($bool){
		self::$enable_default_modeladmin_panel = $bool;
	}
	
	/**
	 * Template variable to determine if the default ModelAdmin should be displayed
	 * @TODO move the ModelAdminPanel to its own panel
	 * @return boolean 
	 */
	function ShowDefaultModelAdminPanel(){
		return self::$enable_default_modeladmin_panel;
	}
	
	/**
	 * By default, when no Panels are set in the subclass, PanelModelAdmin will display 
	 * a ModelAdminSearchPanel and a ModelAdminMenuPanel.
	 * Set this to false to disable this behaviour, or add your own set of Panels.
	 * @param boolean 
	 */
	function disableDefaultPanels($bool){
		self::$disable_default_panels = $bool;
	}
	
	/**
	 * Collects all activated Panels for this PanelModelAdmin and renders them in the left sidebar.
	 * When no Panels are set, a default set of Search and MenuPanel will be shown.
	 */
	function Panels() {
		if(!self::$panels && !self::$disable_default_panels){
			$this->addPanels(array(
				'SearchPanel' 	=> new ModelAdminSearchPanel('Search', 'closed', $this->getManagedModels()),
				'MenuPanel' 	=> new ModelAdminMenuPanel('Menu', 'open', $this->getManagedModels())
			));
		}
		
		$panels = new DataObjectSet();
		foreach(self::$panels as $name => $panel) {
			$panel->setName($name);
			$panel->init();
			$panels->push(new ArrayData(array(
				'Name'		=> $name,
				'Panel'		=> $panel,
				'Title'		=> $panel->Title(),
				'State'		=> $panel->PanelState()
			)));
		}
		return $panels;
	}
	
	function getUrlSegment(){
		return $this->stat('url_segment');
	}
	
	function getDefaultModel(){
		return $this->stat('default_model');	
	}
	
	function init(){
		parent::init();
		Requirements::css($this->stat('module_folder').'/css/panels.css');
		// will be combined later on
		Requirements::javascript($this->stat('module_folder').'/javascript/PanelModelAdmin.js');
		Requirements::javascript($this->stat('module_folder').'/javascript/PanelModelAdminButtonActions.js');	
		
		Requirements::javascript('sapphire/thirdparty/jquery-ui/jquery-ui-1.8rc3.custom.js');
		
		Requirements::css('sapphire/thirdparty/jquery-ui-themes/smoothness/jquery-ui-1.8rc3.custom.css');
	}
	
	
	/**
	 * This will get the RecordController for a single DataObject 
	 * @param object $request the current request
	 * @param string $classname the DataObject ClassName
	 * @param int $id the DataObject ID 
	 *
	 * @return the RecordController instance for example a ModelAdmin_RecordController
	 */
	function getRecordController($request, $classname, $id){
		$recordController = $this->getRecordControllerClass($classname);
		return new $recordController($this->bindModelController($classname, $request), $request, $id);
	}
	
	function getEditForm(){
		if($this->getDefaultModel()){
			return $this->bindModelController($this->getDefaultModel())->ResultsForm(array());
		}
	}
}

class PanelModelAdmin_CollectionController extends ModelAdmin_CollectionController {
	
	/**
	 * singleton DataObject instance
	 */
	protected $singleton;
	
	/**
	 * The TableField class for the current object
	 */
	protected $tableFieldClass;
	
	/**
	 * The parent DataObject class.
	 * This is used in the CategoryMenuPanel.
	 * Setting this here will return the correct ResultSet after a DetailForm action is performed
	 */
	protected $parentClass;
	
	/**
	 * The total amount of items in the current ResultSet
	 */
	protected $resultsCount;
	
	/**
	 * The current TableListField or subclass instance
	 * We set this as variable, so we can set customisation across methods easier
	 */
	protected $tf;
	
	function __construct($parent, $model) {
		parent::__construct($parent, $model);
		$this->singleton		= singleton($this->modelClass);
		$this->tableFieldClass	= $this->setTableFieldClass();		
		$this->parentClass		= $this->singleton->stat('admin_parent_class');
		$this->tf				= $this->getResultsTable();
		$this->resultsCount		= $this->tf->TotalCount();
		
	}
	
	function setTableFieldClass(){
		if($admin_table_field = $this->singleton->stat('admin_table_field')){
			if($admin_table_field != 'TableListField' && !is_subclass_of($admin_table_field, 'TableListField')){
				user_error(
					"PanelModelAdmin_CollectionController::__construct(): 
					$admin_table_field is not a valid TableListField or subclass", 
					E_USER_ERROR
				);	
			}
			return $this->singleton->stat('admin_table_field');
		} else {
			return $this->parentController->resultsTableClassName();
		}
	}
	
	/**
	 * Overload Modeladmin::getResultsTable() to have more flexible permissions on ResultsTables
	 * {@link setTableFieldPermissions()}
	 */
	function getResultsTable() {
		
		$searchCriteria = isset($this->request) ? $this->request->getVars() : array();
		$summaryFields = $this->getResultColumns($searchCriteria);
		
		/*** TableField ***/
		if($this->tableFieldClass == 'TableField' || in_array($this->tableFieldClass, ClassInfo::subclassesFor('TableField'))){
			
			$fields = $this->singleton->scaffoldFormFields(array('restrictFields' => $summaryFields));
			foreach($fields as $f){
				$formFields[$f->name] = $f->class;
			}
			$labels = $this->singleton->fieldLabels();
			foreach($summaryFields as $s){
				$summaryFields[$s] = $labels[$s];
			}
			$tf = new $this->tableFieldClass(
					$this->modelClass,
					$this->modelClass,
					$summaryFields,
					$formFields
			);
			if($this->parentClass){
				$parentID = 0;
				if(isset($this->request)){
					$parentID = $this->request->requestVar($this->parentClass.'__ID');
				}
				$tf->setExtraData(array(
					$this->parentClass.'ID' => $parentID
				));
			}
			if($this->singleton->canCreate()){
				$tf->showAddRow = true;
			} else{
				$tf->showAddRow = false;
			}
			$tf->setTransformationConditions($this->performCanEditTransformation($tf->Items(), $formFields));
			
		/*** ComplexTableField ***/
		}elseif($this->tableFieldClass == 'ComplexTableField' || in_array($this->tableFieldClass, ClassInfo::subclassesFor('ComplexTableField'))){
			$tf = new $this->tableFieldClass(
					$this,
					$this->modelClass,
					$this->modelClass,
					$summaryFields,
					"getCMSFields_forPopup"
			);
		/*** TableListField ***/
		}else{
			$tf = new TableListField(
				$this->modelClass,
				$this->modelClass,
				$summaryFields
			);
			$url = '<a href=\"' . $this->Link() . '/$ID/edit\">$value</a>';
			$tf->setFieldFormatting(array_combine(array_keys($summaryFields), array_fill(0,count($summaryFields), $url)));
		}
		
		/*** DataObjectManager ***/
		// @TODO : make DOM work!
		if($this->tableFieldClass == 'DataObjectManager' || in_array($this->tableFieldClass, ClassInfo::subclassesFor('DataObjectManager'))){
			$filteredCriteria = $searchCriteria;
			unset($filteredCriteria['ctf']);
			unset($filteredCriteria['url']);
			unset($filteredCriteria['action_search']);
			$tf->setSourceFilter($filteredCriteria);
		}
		
		$tf->setCustomQuery($this->getSearchQuery($searchCriteria));
		$tf->setPageSize($this->parentController->stat('page_length'));
		$tf->setPermissions($this->setTableFieldPermissions($tf->getPermissions()));
		$tf->setShowPagination(true);
		
		if($tf->sourceItems()){
			$tf->setCustomSourceItems($this->performCanViewOnEachItem($tf->sourceItems()));	
		}
		
		// csv export settings (select all columns regardless of user checkbox settings in 'ResultsAssembly')
		$exportFields = $this->getResultColumns($searchCriteria, false);
		$tf->setFieldListCsv($exportFields);
		
		return $tf;
	}
	
	/**
	 * Set the permissions on ResultsTables
	 * You can use canExport() and canPrint() in your DataObject in addition to the de default can methods.
	 */
	function setTableFieldPermissions($permissions = array()){
		$permissions = array_merge(array('export', 'print', 'delete'), $permissions);
		
		if($this->singleton->hasMethod('canExport') && !$this->singleton->canExport()){
			unset($permissions[array_search('export',$permissions)]);
		}
		if($this->singleton->hasMethod('canPrint') && !$this->singleton->canPrint()){
			unset($permissions[array_search('print',$permissions)]);
		}
		if($this->singleton->hasMethod('canCreate') && !$this->singleton->canCreate()){
			unset($permissions[array_search('add',$permissions)]);
		}
		return $permissions;
	}
	
	//perform canView check on each item and remove the item
	function performCanViewOnEachItem($sourceItems){
		if($items = $sourceItems){
			if($this->singleton->hasMethod('canPrint')){
				foreach($items as $item){
					if($item->canView() !== NULL  && !$item->canView()){
						$items->remove($item);
					}			
				}
			}
			return $items;	
		}
	}
	
	function performCanEditTransformation($fields, $formFields){
		if($items = $fields){
			if($this->singleton->hasMethod('canPrint')){
				$conditions = array();
				foreach($items as $item){
					if(!$item->canEdit()){					
						foreach($formFields as $field => $title){
							$conditions[$field] = array(
								"rule" => '!$canEdit()',
								"transformation" => "performDisabledTransformation"							  
							);
						}
					}
				}
				return $conditions;
			}
		}
	}
	
	function search($request, $form) {
		// Get the results form to be rendered
		$resultsForm = $this->ResultsForm(array_merge($form->getData(), $request));
		
		if(is_a($resultsForm,'Form')){
			if($this->resultsCount) {
				return new SS_HTTPResponse(
					$resultsForm->forTemplate(), 
					200, 
					sprintf(
						_t('ModelAdmin.FOUNDRESULTS',"Your search found %s matching items"), 
						$this->resultsCount
					)
				);
			} else {
				return new SS_HTTPResponse(
					$resultsForm->forTemplate(), 
					200, 
					_t('ModelAdmin.NORESULTS',"Your search didn't return any matching items")
				);
			}
		} else {
			return $resultsForm;	
		}
	}
	
	/**
	 * If the DataObjects admin_table_field is a not a (subclass of) TableListField
	 * or the DataObject has a custom ModelAdminResultsForm set, create a CustomResultsForm
	 * else proceed as normal, by calling the ModelAdmin's ResultsForm
	 * Add a createNew Button in case the ModalAdminPanel is disabled
	 */
	function ResultsForm($searchCriteria) {
		if($form = $this->getCustomResultsForm($searchCriteria)){
			return $form;
		} else {
			$form = parent::ResultsForm($searchCriteria);				
			$form->Actions()->push(new FormAction("createNew",  _t('ModelAdmin.ADDBUTTON', "Add"), $form));
			return $form;
		}
	}
	
	/**
	 * @TODO write doc
	 * @TODO make this more solid
	 */
	function getCustomResultsForm($searchCriteria){
	
		if($this->singleton->hasMethod('ModelAdminResultsForm') && $this->singleton->ModelAdminResultsForm()){
			$resultsForm = $this->singleton->ModelAdminResultsForm();
			
			if(is_a($resultsForm, 'FormField')){
				$fields = new FieldSet(
					$resultsForm
				);
			} elseif(is_a($resultsForm, 'FieldSet')){
				$fields = $resultsForm;
			} else {
				return $resultsForm; // return raw ModelAdminResultsForm
			}
		} else { 

			$fields = new FieldSet(
				new TabSet(
					$name = "ResultsTabSet",
					new Tab(
						$title= $this->singleton->plural_name(),
						$this->tf
					)
				)				
			);
		}
	
		$form = new Form(
			$this,
			'ResultsForm',
			$fields,
			$actions = new FieldSet(
				new FormAction("goBack", _t('ModelAdmin.GOBACK', "Back")),
				new FormAction("goForward", _t('ModelAdmin.GOFORWARD', "Forward"))
			)
		);
		
		if(isset($this->request) && $this->parentClass){
			$fields->push(
				new HiddenField($this->parentClass.'__ID',$this->parentClass.'__ID', 
					$this->request->requestVar($this->parentClass.'__ID')
				)
			);
		}
		
		/** 
		 * Set searchCriteria so the returned form will obey the selections
		 * On TableField we need a dirty HiddenField, to return to correct selection after save
		 * On CTF we need a filter URL to get the correct selection after closing the popup
		 */
		$filter = '';
		$filteredCriteria = $searchCriteria;
		
		if(is_a($this->tf, 'TableField')){
			$fields->push(new HiddenField("searchCriteria", "searchCriteria", http_build_query($searchCriteria)));
			if($this->singleton->canEdit() || $this->singleton->canCreate()){
				$actions->push(new FormAction("doSave", _t('ModelAdmin.SAVE', "Save"), $form));
			}
		}elseif(is_a($this->tf, 'DataObjectManager')){
			$filter = '';
		}else{
			unset($filteredCriteria['ctf']);
			unset($filteredCriteria['url']);
			unset($filteredCriteria['action_search']);
			$filter = '?' . http_build_query($filteredCriteria);	
		}
		if(!is_a($this->tf, 'TableField')){
			if($this->singleton->canCreate()){
				$form->Actions()->push(new FormAction("createNew",  _t('ModelAdmin.ADDBUTTON', "Add"), $form));	
			}
		}
		$form->setFormAction($this->parentController->Link() . $this->modelClass . '/ResultsForm' . $filter);
		return $form;
	}
	
	/**
	 * This will save the TableField records
	 * Seems a little ugly to create a seperate save function for the TableField
	 * in the CollectionController but it works
	 */
	function doSave($data, $form, $request) {
		if(isset($data[$this->modelClass]) && is_array($data[$this->modelClass])){
			foreach($data[$this->modelClass] as $k => $v){
				if($k == 'new'){
					$class = $this->modelClass;
					$record = new $class;
					$form->saveInto($record); //don't use write(), cause it will store empty records
				} else {
					$record = DataObject::get_by_id($this->modelClass, $k);
					$form->saveInto($record);
					$record->write();
				}
			}
		}
		
		// Set searchCriteria so the returned form will obey the selections
		$searchCriteria = array();
		if(isset($data['searchCriteria'])){
			parse_str($data['searchCriteria'], $searchCriteria);
		}
		
		return $this->ResultsForm($searchCriteria)->forAjaxTemplate();
	}
}

class PanelModelAdmin_RecordController extends ModelAdmin_RecordController{
	
/*	public function EditForm() {
		if($this->request->getVar('admin_table_field')){
			return $this->CustomEditForm();
		}else{
			return parent::EditForm();
		}
	}
	
	function CustomEditForm($model = NULL, $type = NULL){
		$dataObject = $model ?  $model : $this->request->getVar('Model');
		$fieldType = $type ?  $type : $this->request->getVar('admin_table_field');
		$sng = singleton($dataObject);
		$fields = $sng->scaffoldFormFields(array('restrictFields' => array('none'), 'tabbed' => true));
	
		$tfFields = $sng->scaffoldFormFields(array('restrictFields' => $sng->stat('summary_fields')));
		$formFields = array();
		foreach($tfFields as $f){
			$formFields[$f->title] = $f->class;
		}
		
		
		
		if($fieldType == 'TableField'){
			$tf = new $fieldType(
				$this->parentController->getModelClass(),
				$sng->class,
				$sng->stat('summary_fields'),
				$formFields,
				$this->parentController->getModelClass().'ID',
				2
			);		
			$tf->setExtraData(array(
				$this->parentController->getModelClass().'ID' => 2 
			));
		} else if($fieldType == 'ComplexTableField' || $fieldType == 'DataObjectManager'){// 
			$tf = new $fieldType(
					$this,
					$sng->class,
					$sng->class,
					$sng->stat('summary_fields'),
					"getCMSFields_forPopup",
					$this->parentController->getModelClass().'ID'
					
			);	
		} 
		
		$fields->addFieldToTab("Root.Main", $tf);
		$fields->push(new HiddenField("ID"));
		$fields->push(new HiddenField("TableField","TableField",$fieldType));
		$fields->push(new HiddenField("Model","Model",$dataObject));
		
		$actions = new FieldSet(
			new FormAction("doSave", _t('ModelAdmin.SAVE', "Save")),
			new FormAction("goBack", _t('ModelAdmin.GOBACK', "Back"))
		);

		
		$form = new Form($this, "EditForm", $fields, $actions);
		$form->loadDataFrom($this->currentRecord);

		return $form;
	}
	
	function doSave($data, $form, $request) {
		$form->saveInto($this->currentRecord);
		if(isset($data['TableField']) && $data['TableField'] == 'TableField'){
			
		} else{
			try {
				$this->currentRecord->write();
			} catch(ValidationException $e) {
				$form->sessionMessage($e->getResult()->message(), 'bad');
			}
		}
		if(isset($data['TableField']) && $data['TableField'] == 'TableField' && isset($data['Model'])){
			if(Director::is_ajax()) {
				return $this->CustomEditForm($data['Model'],$data['TableField'])->forAjaxTemplate();
			} else {
				Director::redirectBack();
			}
		} else {
			if(Director::is_ajax()) {
				return $this->edit($request);
			} else {
				Director::redirectBack();
			}
		}
		
	}	*/
}