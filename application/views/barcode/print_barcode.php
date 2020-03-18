<?php  
    defined('BASEPATH') OR exit('No direct script access allowed');
    $this->load->view('layout/header');
?>
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
                            <div class="col-sm-12" id="barcode-con">
                            <?php
                                if (!empty($barcodes)){
                                    $c = 1;
                                    if ($style == 12 || $style == 18 || $style == 24 || $style == 40){
                                        echo '<div class="barcodea4">';
                                    }elseif ($style != 50){
                                        echo '<div class="barcode">';
                                    }

                                    foreach ($barcodes as $item){
                                        for ($r = 1; $r <= $item['quantity']; $r++){
                                        echo '<div style="border: 0.5px dashed #555 !important;" class="item style' . $style . '" ' .($style == 50 && $this->input->post('cf_width') && $this->input->post('cf_height') ? 'style="width:' . $this->input->post('cf_width') . 'in;height:' . $this->input->post('cf_height') . 'in;border:0;"' : ''). '>';

                                            if ($style == 50){

                                                if ($this->input->post('cf_orientation')){

                                                $ty = (($this->input->post('cf_height') / $this->input->post('cf_width')) * 100) . '%';
                                                $landscape = ' -webkit-transform-origin: 0 0;
                                                -moz-transform-origin:    0 0;
                                                -ms-transform-origin:     0 0;
                                                transform-origin:         0 0;
                                                -webkit-transform: translateY(' . $ty . ') rotate(-90deg);

                                                -moz-transform:    translateY(' . $ty . ') rotate(-90deg);

                                                -ms-transform:     translateY(' . $ty . ') rotate(-90deg);

                                                transform:         translateY(' . $ty . ') rotate(-90deg);

                                                ';

                                                    echo '<div class="div50" style="width:' . $this->input->post('cf_height') . 'in;height:' . $this->input->post('cf_width') . 'in;border: 1px dotted #CCC;' . $landscape . '">';
                                                }else{
                                                    echo '<div class="div50" style="width:' . $this->input->post('cf_width') . 'in;height:' . $this->input->post('cf_height') . 'in;border: 1px dotted #CCC;padding-top:0.025in;">';
                                                }
                                            }
                                            if ($item['code'] || $item['name']){
                                                $item_name = explode("/",$item['name']);
                                                echo '<span class="barcode_site">' .  $item_name[0] . '</span>';
                                            }

                                            // $item['code'] . '-' .
                                            // if($item['name']) {

                                            //     echo '<span class="barcode_name">'.$item['name'].'</span>';

                                            // }                                            

                                            echo '<span class="barcode_image"> <img src ="' . base_url() . $item['barcode'] . '" > </span>';

                                            if ($item['selling_price']){
                                                echo "Price - ";
                                                echo '<span class="barcode_price">' . $item['selling_price'] . '</span> ';

                                            }

                                            if ($item['unit']){

                                                echo " Unit - ";
                                                echo '<span class="barcode_unit">' . $item['unit'] . '</span><br>';
                                            }

                                            if ($item['category']){
                                                echo "Category - ";
                                                echo '<span class="barcode_category">' . $item['category'] . '</span><br>';
                                            }

                                            if ($item['sku']){
                                                echo "SKU - ";
                                                echo '<span class="barcode_category">' . $item['sku'] . '</span>';
                                            }

                                           /* if ($item['serial_no']){
                                                echo "Serial No - ";
                                                echo '<span class="barcode_category">' . $item['serial_no'] . '</span>';
                                            } */
                                            

                                            if ($style == 50){
                                                echo '</div>';
                                            }
                                            echo '</div>';
                                            if ($style == 40){
                                                if ($c % 40 == 0){
                                                    echo '</div><div class="clearfix"></div><div class="barcodea4">';
                                                }
                                            }elseif ($style == 30){
                                                if ($c % 30 == 0){
                                                    echo '</div><div class="clearfix"></div><div class="barcode">';
                                                }
                                            }elseif ($style == 24){
                                                if ($c % 24 == 0){
                                                    echo '</div><div class="clearfix"></div><div class="barcodea4">';

                                                }
                                            }elseif ($style == 20){
                                                if ($c % 20 == 0){
                                                    echo '</div><div class="clearfix"></div><div class="barcode">';
                                                }
                                            }elseif ($style == 18){
                                                if ($c % 18 == 0){
                                                    echo '</div><div class="clearfix"></div><div class="barcodea4">';
                                                }
                                            }elseif ($style == 14){
                                                if ($c % 14 == 0){
                                                    echo '</div><div class="clearfix"></div><div class="barcode">';
                                                }
                                            }elseif ($style == 12){
                                                if ($c % 12 == 0){
                                                    echo '</div><div class="clearfix"></div><div class="barcodea4">';
                                                }
                                            }elseif ($style == 10){
                                                if ($c % 10 == 0){
                                                    echo '</div><div class="clearfix"></div><div class="barcode">';
                                                }
                                            }
                                            $c++;
                                        }
                                    }                                    
                                    
                                    if ($style != 50){
                                        echo '</div>';
                                    }

                                }else{

                                    echo '<h3>' . lang('no_product_selected') . '</h3>';
                                }

                                ?>
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