(function($) {
	$('.panelmodelclassselector select')
        // Set up an onchange function to show the applicable form and hide all others
        .change(function() {
            var $selector = $(this);
            $('option', this).each(function() {
                var $form = $('#'+$(this).val());
                if($selector.val() == $(this).val()) $form.show();
                else $form.hide();
            });
        })
        // Initialise the form by calling this onchange event straight away
        .change();
	
	$('.modelpanelsearch form input.action').live('click', function(e){
		var form = $(this).parent().parent('form');
		var post = form.attr('action');
		
		var elem = $(this);
		$.post(post, form.formToArray(), function(result){
			tinymce_removeAll();									   
										   
			$('#ModelAdminPanel').html(result);
			$(elem).removeClass('loading');
			$('#Form_EditForm_action_goForward, #Form_ResultsForm_action_goForward').hide();
			$('#Form_EditForm_action_goBack, #Form_ResultsForm_action_goBack').hide();
			var actions = $("#right form div.Actions");
			$("#right form div.Actions").remove();
			$('#ModelAdminPanel').append(actions);
			$("#right div.Actions").addClass('ajaxActions').removeClass('Actions');
			Behaviour.apply();
			if(window.onresize) window.onresize();
		}, 'html');
		e.preventDefault();
	});	
		
		
})(jQuery);