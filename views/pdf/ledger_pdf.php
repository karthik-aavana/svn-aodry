<!DOCTYPE html>
<html>
  	<head>
    	<title>Ledger Report</title>
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
   			<h3 style='text-align: center; border-bottom: 1px solid #777; padding-bottom: 20px'><?=(@$export_data['ledger_name'] ? $export_data['ledger_name'] : '')?> Ledger Reports</h3>
   			
   			<p style='text-align: center;'><b><?=$export_data['firm_detail']['company_name']?></b> : <?=$export_data['firm_detail']['primary_address']?></p>
   			<p style="text-align: center;"><b>From : </b><?=date('d-m-Y',strtotime($export_data['firm_detail']['from_date']));?> <b> to </b><?=date('d-m-Y',strtotime($export_data['firm_detail']['to_date']));?></p>
   		<table class="table table-bordered">
   			<thead>
				<tr>
					<th>Voucher Date</th>
					<th>Voucher No</th>
					<th>Legder</th>
					<th>Amount DR</th>
					<th>Amount CR</th>
					<?php
					if($export_data['isBank'] == 1){
					?>
					<th>Closing Balance</th>
					<?php
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php  
				if(!empty($export_data)){ ?>
					<tr>
						<td></td>
						<td></td>
						<td>Opening Balance</td>
						<td><?=($export_data['opening_balance'] > 0 ? number_format(abs($export_data['opening_balance']),2) : '')?></td>
						<td><?=($export_data['opening_balance'] <= 0 ? number_format(abs($export_data['opening_balance']),2) : '')?></td>
						<?php
						if($export_data['isBank'] == 1){
						?>
						<td></td>
						<?php
						}
						?>
					</tr>
				<?php 
					$closing_balance = $export_data['opening_balance'] ;
	           		$closing_balance_type = '';
				 foreach ($export_data['date_asc'] as $k => $val){              
            		foreach ($export_data['final_report'][$val] as $key => $value) {?>
					<tr>
						<td><?=date('d-m-Y',strtotime($value['voucher_date']))?></td>
						<td><?=$value['voucher_no']?></td>
						<td><?=$value['ledger']?></td>
						<?php
							 if(@$value['DR']){								
								echo '<td>'.number_format($value['DR'],2).'</td><td></td>';
								$closing_balance = $closing_balance + $value['DR'];
		                        if($closing_balance < 0){
		                            $closing_balance_type = number_format(abs($closing_balance),2)." CR";
		                        }else{
		                            $closing_balance_type = number_format(abs($closing_balance),2)." DR";
		                        }
		                        if($export_data['isBank'] == 1){
									echo '<td>'.$closing_balance_type.'</td>';
								}
							}elseif(@$value['CR']){								
								echo '<td></td><td>'.number_format($value['CR'],2).'</td>';
								$closing_balance = $closing_balance + $value['CR'] * -1;
		                        if($closing_balance < 0){
		                            $closing_balance_type = number_format(abs($closing_balance),2)." CR";
		                        }else{
		                            $closing_balance_type = number_format(abs($closing_balance),2)." DR";
		                        }
		                        if($export_data['isBank'] == 1){
									echo '<td>'.$closing_balance_type.'</td>';
								}
							}else{
								if($export_data['isBank'] == 1){
									echo '<td></td><td></td><td></td>';
								}else{
									echo '<td></td><td></td>';
								}
							}
						?>
					</tr>
				<?php }} ?>
					<tr>
						<td></td>
						<td></td>
						<td>Closing Balance</td>
						<td><?= ($export_data['closing_balance'] < 0 ? number_format(abs($export_data['closing_balance']),2) : ''); ?></td>
						<td><?=($export_data['closing_balance'] >= 0 ? number_format(abs($export_data['closing_balance']),2) : ''); ?></td>
						<?php
						if($export_data['isBank'] == 1){
						?>
						<td></td>
						<?php
						}
						?>
					</tr>
				<?php } ?>
			</tbody>
   		</table>
   		</div>
   	</body>
</html>