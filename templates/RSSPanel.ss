<% if RSSItems %>

	<ul class="item">
		<% control RSSItems %>
		   <li class="$EvenOdd"><a href="$Link">$Title</a></li>
		<% end_control %>
	</ul>
<% else %>
	<div class="bad">
	   <p>The rss feed appears to be empty</p>
	</div>
<% end_if %> 