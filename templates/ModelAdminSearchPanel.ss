<% if ModelForms.MoreThanOnePage %>
<p class="panelmodelclassselector">
	<% _t('ModelAdmin.SEARCHFOR','Search for:') %>
	<select>
		<% control ModelForms %>
			<option value="{$FormID}_$ClassName">$Title</option>
		<% end_control %>
	</select>
</p>
<% end_if %>
<div class="modelpanelsearch<% if HideColumnSelect %> hidden<% end_if %>">
	<% control ModelForms %>
		<div id="{$FormID}_$ClassName">
		$SearchForm
		</div>
	<% end_control %>
</div>