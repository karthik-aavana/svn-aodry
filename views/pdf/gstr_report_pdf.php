<!DOCTYPE html>
<html>
  	<head>
    	<title>GSTR1 Report</title>
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
   			<h3 style='text-align: center; border-bottom: 1px solid #777; padding-bottom: 20px'><?=(@$export_data['data_type'] ? $export_data['data_type'] : '')?> Reports</h3>
   			<table class="table table-bordered">
   				<thead>
   					<tr>
   						<?php
   						foreach($export_data['headers'] as $field){ ?>
   						<td><?= $field ?></td>
				        <?php }
   						?>
   					</tr>
   				</thead>
				<tbody>
					<?php
					if(!empty($export_data['data'])){
					 	foreach ($export_data['data'] as $value) { ?>
			                <tr>
				                <?php foreach ($value as $k => $temp) { ?>
				                	<td><?php echo $temp ?></td>
				                	<?php 
				                }?>
				            </tr>
	            	<?php
	            	}
			   		}
			   		else{ 
			   			$count = count($export_data['headers']);?>
	            		<tr><td colspan="<?php echo $count; ?>" style="text-align: center;">No Record Is Available In-Between This Period</td></tr>
	            	<?php
	            	}
			   		?>
				</tbody>
   			</table>
   		</div>
   	</body>
</html>