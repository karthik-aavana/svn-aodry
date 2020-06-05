<?php  
    defined('BASEPATH') OR exit('No direct script access allowed');
    $this->load->view('layout/header');
?>
<style type="text/css">
	.barcode .item {
	    display: block;
	    overflow: hidden;
	    text-align: center;
	    border: 1px solid #000 !important;
	    font-size: 11px;
	    line-height: 20px;
	    text-transform: capitalize;
	    float: left;
	    color: #000000;
	}
	.barcode_maindiv#barcode-con{
	    margin: auto;
	    width: 104mm;
	    float: left;
	}
</style>
<div class="content-wrapper" media='all'>
    <!-- Content Header (Page header) -->
    <section class="content-header"></section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('barcode'); ?>">Barcode</a></li>
            <li class="active">Add </li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Generated Barcodes</h3>
                        <a class="btn btn-sm btn-default pull-right" href="<?php echo base_url('barcode'); ?>">Back </a>
                    </div>
                    <!-- font-family: 'Work Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; -->
                    <div class="box-body">
                        <div class="row">
                            <div class="barcode_maindiv" id="barcode-con">
                            	<div class="barcode" style="font-family: sans-serif;display: block;width: 104mm;float: left;">
                            		<?php
                            		foreach ($barcodes as $key => $value) {
                            			for ($i=1; $i <= $value['quantity']; $i++) { ?>
                            				<div style="display: block;overflow: visible;text-align: center;font-size: 10px;text-transform: capitalize;float: left;color: #000000;margin: 9.5px;padding: 5px;width: 100%;height: 46mm;font-weight: normal;line-height: 1.2;    letter-spacing: 0.5px;position: relative;float: left;color: black !important;">
		                            			<div class="horizontal_div" style="width: 65%;float: left;    padding-top: 0px;">
		                            				<div class="col-sm-12 f_left xsmall" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;text-align: left;font-size: xx-small;">
			                            				<div class="col-sm-6 p_0" style="position: relative;min-height: 1px;float: left;width: 25%;padding: 0px;">Article</div>
			                            				<div class="col-sm-3 p_0" style="position: relative;min-height: 1px;float: left;width: 50%;padding: 0px;">Color</div>
			                            				<div class="col-sm-3 p_0" style="position: relative;min-height: 1px;float: left;width: 25%;padding: 0px;">Size</div>
			                            			</div>
			                            			<div class="col-sm-12 f_left" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;text-align: left;">
			                            				<div class="col-sm-6 p_0 fweight" style="position: relative;min-height: 1px;float: left;width: 25%;font-weight: bold; padding: 0px;"><?=$value['code'];?></div>
			                            				<div class="col-sm-3 p_0 fweight" style="position: relative;min-height: 1px;float: left;width: 50%;font-weight: bold;padding: 0px;"><?=($value['color'] != '' ? ucfirst($value['color']) : '-');?></div>
			                            				<div class="col-sm-3 p_0 fweight" style="position: relative;min-height: 1px;float: left;width: 25%;font-weight: bold;padding: 0px;"><?=($value['size'] != '' ? strtoupper($value['size']) : '-');?></div>
			                            			</div>
			                            			<div class="col-sm-12 f_left" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;text-align: left;">
			                            				Style : <span class="fweight" style="font-weight: bold;"><?=ucfirst($value['category_name']);?></span>
			                            			</div>
			                            			<div class="image_horizontal col-sm-12" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;">
			                            				<img class="barcode_image" width="100%" height="35px" src="<?=base_url() . $value['barcode'];?>">
			                            			</div>
			                            			<div class="col-sm-12" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;">
			                            				<div class="col-sm-6 p_0 f_left" style="position: relative;min-height: 1px;float: left;width: 50%;text-align: left;padding: 0px;"> Unit : 
			                            				    <?php 
			                            						if(strtoupper($value['unit']) == 'PCS'){
			                            							echo "PIECE";
			                            						}elseif (strtoupper($value['unit']) == 'PRS') {
			                            							echo "PAIR";
			                            						}else{
			                            							echo strtoupper($value['unit']);
			                            						}
			                            					?></div>
			                            				<div class="col-sm-6 p_0 f_right" style="position: relative;min-height: 1px;float: left;width: 50%;text-align: right;padding: 0px;"><?=$value['barcode_number'];?></div>
			                            			</div>
			                            			<div class="col-sm-12" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;">
			                            				<div class="col-sm-6 p_0 f_left fweight" style="position: relative;min-height: 1px;float: left;width: 50%;text-align: left;font-weight: bold;padding: 0px;">MRP: Rs.<span class="xlarge" style="font-size: small;font-weight: bold;"><?=$value['mrp'];?></span></div>
			                            				<div class="col-sm-6 p_0 f_right fweight" style="position: relative;min-height: 1px;float: left;width: 50%;text-align: right;font-weight: bold;padding: 0px;">Mfg Date: <span class="xlarge" style="font-size: small;font-weight: bold;"><?=($value['mfg_date'] != '' && $value['mfg_date'] != '0000-00-00' ? date('m-y',strtotime($value['mfg_date'])) : '');?></span></div>
			                            			</div>
			                            			<div class="col-sm-12 f_left fweight" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;text-align: left;font-weight: bold;">
			                            				Mkt By: <?php 
			                            				if (isset($branch[0]->branch_name)) {
								                            echo strtolower($branch[0]->branch_name);
								                        }else{
								                        	echo "Leathercraft lifestyle pvt ltd.";
								                        }
			                            				?><br>
			                            				<span style="font-size:8px;"><?php
									                    if (isset($branch[0]->branch_address)) {
									                        echo str_replace(array(
									                            "\r\n",
									                            "\\r\\n",
									                            "\n",
									                            "\\n"), "<br>", $branch[0]->branch_address);
									                        ?>
									                        <?php
									                    } else { ?>855/6, 10th Main, 5th A Cros, Srinivas nagar, Banglore-50 <?php } ?></span>
			                            			</div>
			                            			<div class="col-sm-12 f_left xsmall" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;text-align: left;font-size: xx-small;">
			                            				Email ID: <span><?php
			                            				if (isset($branch[0]->branch_email_address)) {
									                        if ($branch[0]->branch_email_address != "") {
									                            echo $branch[0]->branch_email_address;
									                        }else{
									                        	echo "info@leathercraft.net.in";
									                        }
									                    }else{
								                        	echo "info@leathercraft.net.in";
								                        }
			                            				?></span><br>
			                            				Customer Care: <span> 
			                            				<?php if ($branch[0]->branch_land_number != "" || $branch[0]->branch_land_number != null) {
								                            echo $branch[0]->branch_land_number;
								                        }else{
								                        	echo "9088997788";
								                        } ?></span>
			                            			</div>
			                            			<div class="col-sm-12 f_left xlarge fweight bold" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;text-align: left;font-size: smaller;font-weight: bold;">
			                            				 BRAND : <span><?=strtoupper($value['brand_name']);?></span>
			                            				<br>
			                            				<b>MADE IN INDIA</b>
			                            			</div>
		                            			</div>
		                            			<div class="verticle_div" style="    width: 29%;float: left;position: relative;height: 100%;   letter-spacing: 0.3px;">
		                            				<div class="rotate" style="transform: rotate(90deg);width: 44mm;height: 100px;top: 28px;position: relative;right: 10px;display: inline-block;">
			                            				<div class="col-sm-12 f_left" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%; text-align: left;">
				                            				<div class="col-sm-6 p_0" style="position: relative;min-height: 1px;float: left;width: 25%;padding: 0px;">Article</div>
				                            				<div class="col-sm-3 p_0" style="position: relative;min-height: 1px;float: left;width: 50%;padding: 0px;text-align:center;">Color</div>
				                            				<div class="col-sm-3 p_0" style="position: relative;min-height: 1px;float: left;width: 25%;padding: 0px;text-align:right;">Size</div>
				                            			</div>
				                            			<div class="col-sm-12 f_left" style="    position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;text-align: left;">
				                            				<div class="col-sm-6 p_0 fweight" style="position: relative;min-height: 1px;float: left;width: 25%;padding: 0px;font-weight: bold;"><?=$value['code'];?></div>
				                            				<div class="col-sm-3 p_0 fweight" style="position: relative;min-height: 1px;float: left;width: 50%;padding: 0px;font-weight: bold;text-align:center;"><?=($value['color'] != '' ? ucfirst($value['color']) : '-');?></div>
				                            				<div class="col-sm-3 p_0 fweight" style="position: relative;min-height: 1px;float: left;width: 25%;padding: 0px;font-weight: bold;text-align:right;"><?=($value['size'] != '' ? strtoupper($value['size']) : '');?></div>
				                            			</div>
				                            			<div class="col-sm-12" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;">
				                            				<div class="col-sm-6 p_0 f_left fweight" style="position: relative;min-height: 1px;float: left;width: 50%;text-align: left;padding: 0px;">MRP: Rs.<span class="xlarge" style="    font-size: smaller;font-weight: bold;"><?=$value['mrp'];?></span></div>
				                            				<div class="col-sm-6 p_0 f_right fweight" style="position: relative;min-height: 1px;float: left;width: 50%;text-align: right;padding: 0px;">Mfg Dt: <span class="xlarge" style="font-size: smaller;font-weight: bold;"><?=($value['mfg_date'] != '' && $value['mfg_date'] != '0000-00-00' ? date('m-y',strtotime($value['mfg_date'])) : '');?></span></div>
				                            			</div>
				                            			<div class="image_horizontal col-sm-12" style="position: relative; min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;">
				                            				<img class="barcode_image" width="100%"  src="<?=base_url() . $value['barcode'];?>">
				                            			</div>
				                            			<div class="col-sm-12" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;">
				                            				<div class="col-sm-6 p_0 f_left" style="position: relative;min-height: 1px;float: left;width: 20%;text-align: left;padding: 0px;"></div>
				                            				<div class="col-sm-6 p_0 f_right" style="position: relative;min-height: 1px;float: left;width: 78%;text-align: right;padding: 0px;"><?=$value['barcode_number'];?></div>
				                            			</div>
				                            			<div class="col-sm-12 f_left xlarge fweight" style="position: relative;min-height: 1px;padding-right: 5px;padding-left: 5px;float: left;width: 100%;text-align: left;font-size: smaller;font-weight: bold;">
				                            				BRAND : <span><?=strtoupper($value['brand_name']);?></span>
				                            				<br>
			                            					<b>MADE IN INDIA</b>
				                            			</div>
		                            				</div>
		                            			</div>
		                            		</div>
                            			<?php }
                            		}
                            		?>
                            		
                            	</div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="button" onclick="printDiv('barcode-con')"; class="btn btn-primary tip no-print" title="" data-original-title="Print"><i class="icon fa fa-print"></i> Print</button>
                            <span class="btn btn-default" id="sale_cancel" onclick="cancel('barcode')">Cancel</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
<?php $this->load->view('layout/footer'); ?>
<script type="text/javascript">
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>