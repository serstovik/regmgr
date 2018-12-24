<table class="mwIndexTable" id="rmIndex">
	<thead>
		<tr>
			<?php
			foreach ($headers as $header){
				echo "<td>$header</td>";
			}//foreach
			?>

		</tr>
	</thead>
	<tbody>
	<?php	foreach ($applications as $application): ?>
			<tr data-id="<?=$application['id']; ?>">
			<?php	foreach ($application as $appField) { echo "<td>$appField</td>"; } ?>
				<td><a class="Button Delete"></a></td>
			</tr>
	<?php	endforeach; ?>
	</tbody>
</table>

<script type="text/javascript">
	jQuery(document).ready(function(){
		rmApplicationAdmin('#rmIndex');
	}); //ready
</script>
