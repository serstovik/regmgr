<div class="mwDskTools">
	<h1>Register Manager</h1>
	<select id="order_select">
		<option value="default_desc">Order By</option>
		<option value="alpha_desc">Alphanumeric Desc</option>
		<option value="alpha_asc">Alphanumeric Asc</option>
		<option value="status_desc">Status Desc</option>
		<option value="status_asc">Status Asc</option>
		<option value="date_asc">Date Asc</option>
		<option value="date_desc">Date Desc</option>
	</select>
	<select id="filter_select">
		<option value="default">Filter By</option>
	</select>

	<a class="Add" onclick="applicationEd.dialog()" >Test Application</a>

</div>
<div class="mwDesktop">
	<?=$table;?>
</div>