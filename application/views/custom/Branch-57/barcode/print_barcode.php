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
	.barcode .style10 {
	    margin: 12.5px;
	    padding: 15px;
	    width: auto;
	}
	.barcode .style10 {
	    margin: 9.5px;
	    padding: 12px;
	    width: 146mm;
	    height: 68mm;
	    border: 0.5px dashed #555 !important;
	    font-weight: normal;
	    line-height: 1.4;
	    position: relative;
	    float: left;
	}
	.barcode_maindiv#barcode-con{
		width: 43%;
	    margin: auto;
	    padding: 0;
	}
	div.horizontal_div {
	    width: 92mm;
	    float: left;
	}
	div.verticle_div{
		    width: 46mm;
    float: left;
    top: 0px;
    position: relative;
    left: 5px;
	}
	div.rotate{
		transform: rotate(90deg);
	    width: 66mm;
	    height: 41mm;
	    top: 36px;
	    position: relative;
	    right: 27px;
	    display: inline-block;
	}
	.xsmall{font-size: xx-small;}
	.xlarge{font-size: large;}
	.f_left{text-align: left;}
	.f_right{text-align: right;}
	.fweight{font-weight: bold;}
	.p_0{padding: 0px;}
</style>
<div class="content-wrapper">
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
                    <div class="box-body">
                        <div class="row">
                            <div class="barcode_maindiv" id="barcode-con">
                            	<div class="barcode">
                            		<?php
                            		foreach ($barcodes as $key => $value) {
                            			for ($i=1; $i <= $value['quantity']; $i++) { ?>
                            				<div class="item style10" style="display: block;overflow: hidden;text-align: center;border: 1px solid #000 !important;font-size: 11px;line-height: 20px;text-transform: capitalize;float: left;color: #000000;margin: 9.5px;padding: 12px;width: 146mm;height: 68mm;border: 0.5px dashed #555 !important;font-weight: normal;line-height: 1.5;position: relative;float: left;color: black !important;">
		                            			<div class="horizontal_div" style="width: 92mm;float: left;    padding-top: 5px;">
		                            				<div class="col-sm-12 f_left xsmall" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;text-align: left;font-size: xx-small;">
			                            				<div class="col-sm-6 p_0" style="position: relative;min-height: 1px;float: left;width: 50%;padding: 0px;">Article</div>
			                            				<div class="col-sm-3 p_0" style="position: relative;min-height: 1px;float: left;width: 25%;padding: 0px;">Color</div>
			                            				<div class="col-sm-3 p_0" style="position: relative;min-height: 1px;float: left;width: 25%;padding: 0px;">Size</div>
			                            			</div>
			                            			<div class="col-sm-12 f_left" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;text-align: left;">
			                            				<div class="col-sm-6 p_0 fweight" style="position: relative;min-height: 1px;float: left;width: 50%;font-weight: bold; padding: 0px;"><?=$value['code'];?></div>
			                            				<div class="col-sm-3 p_0 fweight" style="position: relative;min-height: 1px;float: left;width: 25%;font-weight: bold;padding: 0px;"><?=strtoupper($value['color']);?></div>
			                            				<div class="col-sm-3 p_0 fweight" style="position: relative;min-height: 1px;float: left;width: 25%;font-weight: bold;padding: 0px;"><?=$value['size'];?></div>
			                            			</div>
			                            			<div class="col-sm-12 f_left" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;text-align: left;">
			                            				Style : <span class="fweight" style="font-weight: bold;"><?=ucfirst($value['category_name']);?></span>
			                            			</div>
			                            			<div class="image_horizontal col-sm-12" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;">
			                            				<img class="barcode_image" width="100%" src="<?=base_url() . $value['barcode'];?>">
			                            			</div>
			                            			<div class="col-sm-12" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;">
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
			                            			<div class="col-sm-12" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;">
			                            				<div class="col-sm-6 p_0 f_left fweight" style="position: relative;min-height: 1px;float: left;width: 50%;text-align: left;font-weight: bold;padding: 0px;"> MRP: Rs.<span class="xlarge" style="font-size: large;"><?=$value['mrp'];?></span></div>
			                            				<div class="col-sm-6 p_0 f_right fweight" style="position: relative;min-height: 1px;float: left;width: 50%;text-align: right;font-weight: bold;padding: 0px;">Mfg Date: <span class="xlarge" style="font-size: large;"><?=($value['mfg_date'] != '' && $value['mfg_date'] != '0000-00-00' ? date('m-y',strtotime($value['mfg_date'])) : '');?></span></div>
			                            			</div>
			                            			<div class="col-sm-12 f_left fweight" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;text-align: left;font-weight: bold;">
			                            				Mkt By: <?php 
			                            				if (isset($branch[0]->branch_name)) {
								                            echo strtolower($branch[0]->branch_name);
								                        }else{
								                        	echo "Leathercraft lifestyle pvt ltd.";
								                        }
			                            				?><br>
			                            				<span><?php
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
			                            			<div class="col-sm-12 f_left xsmall" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;text-align: left;font-size: xx-small;">
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
			                            			<div class="col-sm-12 f_left xlarge fweight" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;text-align: left;font-size: medium;font-weight: bold;">
			                            				BRAND : <span><?=strtoupper($value['brand_name']);?></span>
			                            			</div>
		                            			</div>
		                            			<div class="verticle_div" style="width: 46mm;float: left;top: 0px;position: relative;left: 5px;">
		                            				<div class="rotate" style="transform: rotate(90deg);width: 66mm;height: 41mm;top: 36px;position: relative;right: 27px;display: inline-block;">
		                            					
			                            				<div class="col-sm-12 f_left" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;text-align: left;">
				                            				<div class="col-sm-6 p_0" style="position: relative;min-height: 1px;float: left;width: 50%;padding: 0px;">Article</div>
				                            				<div class="col-sm-3 p_0" style="position: relative;min-height: 1px;float: left;width: 25%;padding: 0px;">Color</div>
				                            				<div class="col-sm-3 p_0" style="position: relative;min-height: 1px;float: left;width: 25%;padding: 0px;">Size</div>
				                            			</div>
				                            			<div class="col-sm-12 f_left" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;text-align: left;">
				                            				<div class="col-sm-6 p_0 fweight" style="position: relative;min-height: 1px;float: left;width: 50%;font-weight: bold;padding: 0px;"><?=$value['code'];?></div>
				                            				<div class="col-sm-3 p_0 fweight" style="position: relative;min-height: 1px;float: left;width: 25%;font-weight: bold;padding: 0px;"><?=ucfirst($value['color']);?></div>
				                            				<div class="col-sm-3 p_0 fweight" style="position: relative;min-height: 1px;float: left;width: 25%;font-weight: bold;padding: 0px;"><?=strtoupper($value['size']);?></div>
				                            			</div>
				                            			<div class="col-sm-12" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;">
				                            				<div class="col-sm-6 p_0 f_left fweight" style="position: relative;min-height: 1px;float: left;width: 50%;text-align: left;font-weight: bold;padding: 0px;"> MRP: Rs.<span class="xlarge" style="font-size: medium;"><?=$value['mrp'];?></span></div>
				                            				<div class="col-sm-6 p_0 f_right fweight" style="position: relative;min-height: 1px;float: left;width: 50%;text-align: right;font-weight: bold;padding: 0px;">Mfg Date: <span class="xlarge" style="font-size: medium;"><?=($value['mfg_date'] != '' && $value['mfg_date'] != '0000-00-00' ? date('m-y',strtotime($value['mfg_date'])) : '');?></span></div>
				                            			</div>
				                            			<div class="image_horizontal col-sm-12" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;">
				                            				<img class="barcode_image" src="<?=base_url() . $value['barcode'];?>">
				                            			</div>
				                            			<div class="col-sm-12" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;">
				                            				<div class="col-sm-6 p_0 f_left" style="position: relative;min-height: 1px;float: left;width: 50%;text-align: left;padding: 0px;"></div>
				                            				<div class="col-sm-6 p_0 f_right" style="position: relative;min-height: 1px;float: left;width: 50%;text-align: right;padding: 0px;"><?=$value['barcode_number'];?></div>
				                            			</div>
				                            			<div class="col-sm-12 f_left xlarge fweight" style="position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;float: left;width: 100%;text-align: left;font-size: medium;font-weight: bold;">
				                            				BRAND : <span><?=strtoupper($value['brand_name']);?></span>
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