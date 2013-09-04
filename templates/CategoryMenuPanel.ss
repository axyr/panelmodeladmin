<% if Menu %>
	<ul class="modeladminmenu collapsible">
	<% control Menu %>
		<li class="$Class root<% if Children %><% end_if %>"><a href="$Link" class="root">$Title</a>
			<% if Children %>
				<ul class="modeladminmenu">
					<% control Children %>
						<li class="$Class"><a href="$Link">$Title.XML</a>
							<% if Children %>
								<ul class="modeladminmenu hidden">
									<% control Children %>
										<li class="$Class folder"><a href="$Link">$Title</a></li>
									<% end_control %>
								</ul>
							<% end_if %>
						</li>
					<% end_control %>
				</ul>
			<% end_if %>
		</li>
	<% end_control %>
	</ul>
<% end_if %>
