/**
 * Lots of duplicate code in here
 * For now we can not use delete or history actions in a popup form, due to the redirect of doDelete in ModelAdmin.
 * so we remove these buttons in the popup for now.
 * this way of rendering an EditForm in a popup will also generates duplicate ID attributes, which is bad.
 * Maybe I should try to use the default GreyBoxPopup and an Iframe for rendering Forms in a popup
 */


(function($){$(function(){				
						
	$('body.PanelModelAdmin input.pma').live('click', function(e){
		if($(this).hasClass('popup')){
			getCustomForm(this, 'popup');
		} else if($(this).hasClass('rightpane')){
			getCustomForm(this, 'rightpane');
		}
		e.preventDefault();
	});
	
	/**
	 * init dialog
	 */
	$('body').append('<div id="PanelModelAdminDialog"></div>');
	$('#PanelModelAdminDialog').dialog({
		
		autoOpen: false,
		width: 620,
		height: 440,
		modal: false,
		resizable: true,
		autoResize: true,
		overlay: {
			opacity: 0.5,
			background: "black"
		},
		close: function(event, ui) { 
			reloadRHS();
			$(this).hide();
		}
	}).width(600).height(420).hide();		
	
	/**
	 * make shure forms in the jQuery Dialog will be ajax sumbitted as well
	 * and puts the returned content in the dialog
	 */
	$('#PanelModelAdminDialog input[type=submit]').live('click', function(e){
		var elem = $(this);
		$(elem).addClass('loading');
		var form = $('#PanelModelAdminDialog form');
		var formAction = form.attr('action') + '?' + $(this).attr('name');
		
		if(typeof tinyMCE != 'undefined') tinyMCE.triggerSave();
		
		$.post(formAction, form.formToArray(), function(result){
			// will this work in a popup?
			tinymce_removeAll();
			
			$('#PanelModelAdminDialog').html(result);
			$('#PanelModelAdminDialog #Form_EditForm_action_doDelete').hide();
			$('#PanelModelAdminDialog #Form_EditForm_action_goBack, #PanelModelAdminDialog #Form_ResultsForm_action_goBack').hide();
			
			if($('#right #ModelAdminPanel form').hasClass('validationerror')) {
				statusMessage(ss.i18n._t('ModelAdmin.VALIDATIONERROR', 'Validation Error'), 'bad');
			} else {
				statusMessage(ss.i18n._t('ModelAdmin.SAVED', 'Saved'), 'good');
			}
			$(elem).removeClass('loading');
			Behaviour.apply(); // refreshes ComplexTableField
			if(window.onresize) window.onresize();
			
		}, 'html');

		return false;																					  
	});
	
	
	function getCustomForm(elem, whereToShow){
		$(elem).addClass('loading');
		var form = $('#right form');
		var formAction = form.attr('action') + '?' + $(elem).fieldSerialize();
		
		//hack to reload the rightpane
		$('#ModelAdminPanel').fn('addHistory', form.attr('action'));
		
		if(typeof tinyMCE != 'undefined') tinyMCE.triggerSave();

		$.post(window.location.href + $(elem).attr('name').replace('action_',''), form.formToArray(), function(result){
			
			tinymce_removeAll();
			if(whereToShow == 'popup'){
				// hack to preserve buttons for now
				var actions = $("#form_actions_right");
				actions.attr('id', 'form_actions_right_temp');
				showPopup(result, $(elem).attr('value'));
			} else {
				$('#right #ModelAdminPanel').html(result);
			}
			$(elem).removeClass('loading');
			Behaviour.apply(); // refreshes ComplexTableField
		}, 'html');

		return false;
	}
	
	function showPopup(html, title){
		$('#PanelModelAdminDialog').dialog( "option", "title", title );
		$('#PanelModelAdminDialog').html(html).dialog("open");	

		$("#form_actions_right_temp").attr('id', 'form_actions_right');
		$('#PanelModelAdminDialog #Form_EditForm_action_doDelete').hide();
		$('#PanelModelAdminDialog #Form_EditForm_action_goBack, #PanelModelAdminDialog #Form_ResultsForm_action_goBack').hide();
		
	}
	
	function reloadRHS(){
		$('#ModelAdminPanel').fn('goBack');
	}

	
});})(jQuery);