<!DOCTYPE html>
<html>
  	<head>
    	<title>Trial Balance Reports</title>
		<style>
			.table {
				width: 100%;
				max-width: 100%;
				margin-bottom: 1rem;
				font-size:13px;
				border-collapse: collapse;
				background-color: transparent;
			}

			.table-responsive {
				display: block;
				width: 100%;
				overflow-x: auto;
				-webkit-overflow-scrolling: touch;
				-ms-overflow-style: -ms-autohiding-scrollbar;
			}
			.table-bordered th, .table-bordered td {
				border: 1px solid #777;
				padding: 6px; 
			}			

		</style>
    </head>
   	<body>
   		<div class="table-responsive">
   			<h3 style="text-align: center; border-bottom: 1px solid #777; padding-bottom: 20px">Trial Balance Reports</h3>
   			<p style="text-align: center;"><b><?=$firm_detail['company_name']?></b> : <?=$firm_detail['primary_address']?></p>
   			<p style="text-align: center;"><b>From :</b> <?=date('d-m-Y',strtotime($firm_detail['from_date']));?> <b>to</b> <?=date('d-m-Y',strtotime($firm_detail['to_date']));?></p>
   		<table class="table table-bordered">
   			<thead>
				<tr>
					<th>Main Group</th>
					<th>Sub Group1</th>
					<th>Sub Group2</th>
					<th>Ledger</th>
					<th>Debit</th>
					<th>Credit</th>
				</tr>
			</thead>
			<tbody>
				<?php  
				if(!empty($export_data)){
					foreach($export_data as $key => $value){ ?>
					<tr>
						<td><?=$value['main_grp_name']?></td>
						<td><?=$value['sub_grp_1']?></td>
						<td><?=$value['sub_grp_2']?></td>
						<td><?=$value['ledger_name']?></td>
						<td><?=$value['debit']?></td>
						<td><?=$value['credit']?></td>
					</tr>
					<?php }
				}
				?>
			</tbody>
   		</table>
   	</body>
</html>