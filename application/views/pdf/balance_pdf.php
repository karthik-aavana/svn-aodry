<!DOCTYPE html>
<html>
  	<head>
    	<title>Balance Sheet</title>
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
			.padding_30{padding-left: 30px !important;}
			.padding_60{padding-left: 60px !important;}
			.padding_90{padding-left: 90px !important;}
			.b_bottom{text-decoration: underline;}
			.text-left{text-align: left;}
			.text-right{text-align: right;}
			.font-weight-bold{font-weight: bold;}
			.main_head{font-size: 17px;
    text-align: center;}
		</style>
    </head>
   	<body>
   		<div class="table-responsive">
   			<h3 style="text-align: center; border-bottom: 1px solid #777; padding-bottom: 20px">Balance Sheet</h3>

   			<p style="text-align: center;"><b><?=$firm_detail['company_name']?></b> : <?=$firm_detail['primary_address']?></p>
   			<p style="text-align: center;"><b>From :</b> <?=date('d-m-Y',strtotime($firm_detail['from_date']))?> <b>to</b> <?=date('d-m-Y',strtotime($firm_detail['to_date']));?></p>
   			
   		<table class="table table-bordered">
   			<thead>
				<tr>
					<th>Description</th>
					<th>Amount</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
				<?php  
				if(!empty($data)){

					foreach ($data as $key => $value) {
			        	$class ='';
			        	$class1 = ' text-right ';
			        	if(@$value['is_heading']) $class = 'font-weight-bold b_bottom main_head';
			        	if(@$value['is_main']) $class = 'font-weight-bold';
			        	if(@$value['is_left']) $class1 = 'text-left';
			        	$class .= ' padding_'.($value['space']*30);
			        	echo "<tr><td class='{$class}'>{$value[0]}</td><td class='{$class1}'>{$value[1]}</td><td>{$value[2]}</td></tr>";
			        }
				}
				?>
			</tbody>
   		</table>
   	</body>
</html>