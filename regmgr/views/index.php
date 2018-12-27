<table class="mwIndexTable" id="rmIndex">
	<thead><tr>
		
		<?foreach($heads as $k => $v):?>
		<th><?=$v['label']?></th>
		<?endforeach;?>
	
	</tr></thead>
<?php	foreach ($rows as $row): ?>
		<tr data-id="<?=$row['id']; ?>" data-type="<?=$row['type']; ?>">
			
			<?foreach($row['RMColumns'] as $c):?>
				<?=$c?>
			<?endforeach;?>
			
			<!--
			
			<th><a class="edit" href="#"><?=$row['sn']?></a></th>
			<th><a class="edit" href="#"><?=$row['first_name']?></a></th>

			<td><?=mwDate($row['modified'])?></td>

			<td><a class="Button Delete"></a></td>
			
			-->
			
		</tr>
<?php	endforeach; ?>
</table>

<script type="text/javascript">

	jQuery(document).ready(function() {

		rmApplicationAdmin('#rmIndex');

	}); //ready

</script>
