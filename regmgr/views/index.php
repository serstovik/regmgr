<table class="mwIndexTable" id="rmIndex">
	<thead><tr>

		<th>SN</th>
		<th>Title</th>
		<td>Date</td>
		<td width="100">Delete</td>

	</tr></thead>
<?php	foreach ($applications as $row): ?>
		<tr data-id="<?=$row['id']; ?>" data-type="<?=$row['type']; ?>">

			<th><a class="edit" href="#"><?=$row['sn']?></a></th>
			<th><a class="edit" href="#"><?=$row['first_name']?></a></th>

			<td><?=mwDate($row['modified'])?></td>

			<td><a class="Button Delete"></a></td>
		</tr>
<?php	endforeach; ?>
</table>

<script type="text/javascript">

	jQuery(document).ready(function() {

		rmApplicationAdmin('#rmIndex');

	}); //ready

</script>
