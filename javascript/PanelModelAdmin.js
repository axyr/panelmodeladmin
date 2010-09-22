(function($){$(function(){ 	
	
	/**
	 * This will create a url with data from the corresponding SearchForm of the Default DataObject.
	 * Don't use live to ensure this is run first on initial load.
	 */
	$('#right #Form_ResultsForm tbody td a:not(.deletelink,.downloadlink)').click(function(){
		// Set history on initial load for default managed Model
		if(!this.history || this.history.length < 1) {
			var action = $('#Form_ResultsForm').attr('action').replace('ResultsForm?','SearchForm');
			var form = $('form[action="'+action+'"]');
			if(typeof $(form).attr('action') != 'undefined'){
				var url = $(form).attr('action') + '?' + $(form).serialize();
				$('#ModelAdminPanel').fn('addHistory', url);
			}
		}
	});
	
	/**
	 * Basic Panel accordion
	 */
	$('#ModelAdminPanels h2').live('click', function() {
		$(this).next().slideToggle('fast');
		$(this).find('span').toggleClass('open');
		return false;
	});
	
	Behaviour.register({
		/**
		 * Move Actions to AjaxActions and hide goBack and goForward buttons on initial load
		 */
		'#right form div.Actions': {
			initialize: function(){
				var actions = $("#right form div.Actions");
				$("#right form div.Actions").remove();
				$('#ModelAdminPanel').append(actions);
				$("#right div.Actions").addClass('ajaxActions').removeClass('Actions');
				if(!this.future || !this.future.length) {
					$('#Form_EditForm_action_goForward, #Form_ResultsForm_action_goForward').hide();
				}
				if(!this.history || this.history.length <= 1) {
					$('#Form_EditForm_action_goBack, #Form_ResultsForm_action_goBack').hide();
				}
			}
		},
		/**
		 * Overload ModelAdmin set height, since its not alwayt working on PanelModelAdmin
		 */
		'#right form': {
			initialize: function(){
				var newModelAdminPanelHeight = $("#right").height()-$(".ajaxActions").height();
				$('#right form').height(newModelAdminPanelHeight);
				//console.log($('#right form').height());
				$('#right form').css('overflow','auto');
			}
		},
		'#form_actions_right': {
			initialize: function(){
				$("#form_actions_right").removeClass('Actions');
			}
		}
	});
	
	$('#Form_ResultsForm_action_createNew').live('click', function(e){
		getAddForm(e, this);	
		return false;
	});
	
	function getAddForm(e, elem){
		$(elem).addClass('loading');
		var form = $('#right form');
		var post = form.attr('action').replace('ResultsForm','CreateForm');
		$.post(post, form.formToArray(), function(result){
			tinymce_removeAll();									      
			$('#ModelAdminPanel').html(result);
			$(e).removeClass('loading');
			Behaviour.apply();
			if(window.onresize) window.onresize();
		}, 'html');
	}
	
});})(jQuery);