<% require javascript(sapphire/thirdparty/tabstrip/tabstrip.js) %>
<% require css(sapphire/thirdparty/tabstrip/tabstrip.css) %>

<div id="ModelAdminPanels">
	<% if ShowDefaultModelAdminPanel %>
		<h2>Models<span class="closed"></span></h2>
		<div class="content" style="display:none">
			<div id="SearchForm_holder" class="leftbottom">		
				<% if SearchClassSelector = tabs %>
					<ul class="tabstrip">
						<% control ModelForms %>
							<li class="$FirstLast"><a id="tab-ModelAdmin_$Title.HTMLATT" href="#{$Form.Name}_$ClassName">$Title</a></li>
						<% end_control %>
					</ul>
				<% end_if %>
				
				<% if SearchClassSelector = dropdown %>
					<p id="ModelClassSelector">
						<% _t('ModelAdmin.SEARCHFOR','Search for:') %>
						<select>
							<% control ModelForms %>
								<option value="{$Form.Name}_$ClassName">$Title</option>
							<% end_control %>
						</select>
					</p>
				<% end_if %>
				
				<% control ModelForms %>
					<div class="tab" id="{$Form.Name}_$ClassName">
						$Content
					</div>
				<% end_control %>
			</div>
		</div>	
	<% end_if %>

  <% if Panels %>
	<% control Panels %>
		<% if Panel.Enabled %>
			<div id="$Name">
				<h2>$Title<span class="<% if State = closed %>open<% end_if %>"></span></h2>
				<div class="content" style="display:<% if State = open %>block<% else %>none<% end_if %>">
					$Panel
				</div>
			</div>
		<% end_if %>
	<% end_control %>
  <% end_if %>
</div>
