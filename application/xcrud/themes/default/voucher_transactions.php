<table class="table table-default">
	<tr>
		<th>#</th>
		<th>Account</th>
		<th>Description</th>
		<th>Debit</th>
		<th>Credit</th>
	</tr>
	<?php if(count($this->result_list) > 0) { ?>
		<?php foreach($this->result_list as $lst) { ?>
			<tr>
				<td class="xcrud-current xcrud-num"><?php echo $lst['primary_key']; ?></td>
				<td><?php echo $lst['rel.y_transctions.y_account_id']; ?></td>
				<td><?php echo $lst['y_transctions.description']; ?></td>
				<td>
					<?php if($lst['y_transctions.type'] == 'debit') { echo number_format($lst['y_transctions.amount'],2,'.',','); } ?>		
				</td>
				<td><?php if($lst['y_transctions.type'] == 'credit') { echo number_format($lst['y_transctions.amount'],2,'.',','); } ?></td>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr>
			<td colspan="4">No record found!</td>
		</tr>
	<?php } ?>
</table>