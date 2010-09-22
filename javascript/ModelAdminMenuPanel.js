(function($){$(function(){
	$('li.root').live('click', function() {
		return false;
	});
	
	$('.modeladminmenu a').live('click', function() {					
		getResultsForm(this);
		return false;
	});
	
	$('.collapsible li').live('click', function() {
		$(this).find('ul').slideToggle('fast');
		$(this).toggleClass('open');
		$(this).find('a').removeClass('open');
		return false;
	});
	
	function getResultsForm(e){
		$(e).addClass('loading');
		$('.modeladminmenu a').not(e).removeClass('open');
		$('#ModelAdminPanel').fn('addHistory', $(e).attr('href'));
		$.post($(e).attr('href'), function(result){
			tinymce_removeAll();									   
			$("#form_actions_right").remove();			   
			$('#ModelAdminPanel').html(result);
			$(e).removeClass('loading').addClass('open');
			
			$('#Form_EditForm_action_goForward, #Form_ResultsForm_action_goForward').hide();
			$('#Form_EditForm_action_goBack, #Form_ResultsForm_action_goBack').hide();
			/*var actions = $("#right form div.Actions");
			$("#right form div.Actions").remove();
			$('#ModelAdminPanel').append(actions);
			$("#right div.Actions").addClass('ajaxActions').removeClass('Actions');*/
			
			
			Behaviour.apply();
			if(window.onresize) window.onresize();
		}, 'html');
	}	
});})(jQuery);