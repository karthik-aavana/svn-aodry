<?php
$val = sales_notification();
foreach ($val as $sales) {
    
}
$Purchse = purchase_notification();
foreach ($Purchse as $pur) {
# code...
}
$default_p = default_purchase_notification();
foreach ($default_p as $default_purchase) {
    
}
$expense = expense_bill_notification();
foreach ($expense as $exp) {
# code...
}
$default_e = default_expense_bill_notification();
foreach ($default_e as $default_expense) {    
}
$GLOBALS['common_settings_amount_precision'] = (@$access_common_settings[0]->amount_precision ? $access_common_settings[0]->amount_precision : array());
if (!function_exists('precise_amount')) {

    function precise_amount($val) {
        $val = (float) $val;
        // $amt =  round($val,$GLOBALS['common_settings_amount_precision']);
        $dat = number_format($val, $GLOBALS['common_settings_amount_precision'], '.', '');
        return $dat;
    }

}
?>
<!DOCTYPE html>
<html>
    <head>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>AODRY Accounting Software</title>
        <link rel="shortcut icon" type="image/png" href="<?php echo base_url('assets/images/favicon.png'); ?>" />        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>lib/font-awesome/4.5.0/css/font-awesome.min.css">
        <link href="<?php echo base_url('assets/'); ?>dist/css/jquery.mCustomScrollbar.css" media="all" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>lib/ionicons/css/ionicons.min.css">
        <link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/fullcalendar/fullcalendar.min.css">
        <script src="<?php echo base_url('assets/'); ?>graph/loader.js">
        </script>
        <!-- Close Graph -->
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/fullcalendar/fullcalendar.print.css" media="print">
        <!-- daterange picker -->
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/daterangepicker/daterangepicker.css">
        <!-- bootstrap datepicker -->
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/datepicker/datepicker3.css">
        <!-- iCheck for checkboxes and radio inputs -->
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/iCheck/all.css">
        <!-- Bootstrap Color Picker -->
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/colorpicker/bootstrap-colorpicker.min.css">
        <!-- Bootstrap time Picker -->
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/timepicker/bootstrap-timepicker.min.css">
        <link type="text/css" media="all" href="<?php echo base_url('assets/'); ?>css/style.css" rel="stylesheet" />
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/select2/select2.min.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/PNotifyBrightTheme.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/animate.css">
        <!-- DataTables -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/fixedHeader.dataTables.min.css">       
        <link href="<?php echo base_url(); ?>assets/plugins/datatables/responsive.bootstrap.min.css" media="all" type="text/css" rel="stylesheet"/>
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>dist/css/skins/_all-skins.min.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>dist/css/AdminLTE.min.css">        
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>documentation/style.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/plugins/autocomplite/') ?>jquery.auto-complete.css">
        <link href="<?php echo base_url('assets/'); ?>dist/css/pnotify.custom.min.css" media="all" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/tagsinput/bootstrap-tagsinput.css">
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/custom.css">
        <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-3.1.1.js">
        </script>
        <script src="<?php echo base_url(); ?>assets/lib/jquery-ui/jquery-ui.min.js">
        </script>
        <style type="text/css">
        	.box{
			margin: 0;
		}
         .box-border{
         border: 2px dotted #334;
         }
         .box-border h4{
         font-weight: 600;
         }
         hr{
         	margin-bottom: 10px;
         	margin-top: 10px;
         }
         .main-header .logo{
         	width: 240px;
         }
         .sidebar-menu>li>a {
    		padding: 6.3px;
		}
		#billing_filter{
			display: none;
		}
		#billing tfoot tr th input{
			color: red;
			border: red;
		}
		.content{
			padding-bottom: 0;
			min-height: auto;
		}

		.inline-form label{
			font-size: 11px;
		}
		.inline-form input, .inline-form .select2{
			height: 25px;
		}

		.input-group .input-group-addon{
			padding: 0 4px;
		}

		.input-group.date .input-group-addon i{
			font-size: 12px;
		}

		footer{
			display: none;
		}
		.sidebar-mini{
			height: 100vh !important;
		}

      </style>

<body class="hold-transition skin-blue sidebar-mini">
        <div class="loader">
        </div>
        <div class="wrapper">
            <header class="main-header">
                <a href="<?php echo base_url() ?>auth/dashboard" class="logo">
                   <img style='width: 155px;float: left; padding: 4px;' src = "<?= base_url('assets/images/Aodry- white-09.svg') ?>">
                </a>            
                <nav class="navbar navbar-static-top">
                        <div class="navbar-custom-menu">
                            <ul class="nav navbar-nav">
                                <li>
                                    <a href="javascript:void();">
                                       <i class="fa fa-sign-out" aria-hidden="true"></i>Logout
                                    </a>                                  
                                </li>                    
                            </ul>
                        </div>    
                    </nav>
            </header>
            <aside class="main-sidebar">
            <section class="sidebar">
                <ul class="sidebar-menu tree" data-widget="tree">
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Calculator</span></a>
                    </li> 
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Print Bill</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Recall Bill</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Close Bill</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Return</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Last Duplicate Bill</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Hold Bill</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Delete Item</span></a>
                    </li> 
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Duplicate Bill</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Cancel</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Bill Without Address</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Edit Quantity</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Modify Bill</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Membership Card</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Membership Card</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Return All Items</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Find Item in Bill</span></a>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span>Direct to Item</span></a>
                    </li>
                </ul>
            </section>
        </aside>
    </div>
<div class="content-wrapper">  
   <section class="content">
      <div class="box">
         <div class="box-body">
            <form class="inline-form">
               <div class="row">
                  <div class="col-sm-9">
                     <div class="row">
                        <div class="col-sm-4">
                           <div class="form-group row">
                              <label for="Billing Number" class="col-sm-4">Bill No<span class="validation-color">*</span></label>
                               <div class="col-sm-8">
                                 <input class="form-control">
                              </div>
                              <!-- <div class="col-sm-8">
                                 <select class="form-control select2">
                                    <option>AA-4578</option>
                                    <option>AA-4578</option>
                                    <option>AA-4578</option>
                                    <option>AA-4578</option>
                                 </select>
                              </div> -->
                              <span class="validation-color" id="error"></span>
                           </div>
                        </div>
                        <div class="col-sm-4">
                           <div class="form-group row">
                              <label for="Billing Date" class="col-sm-4">Date<span class="validation-color">*</span></label>
                              <div class="col-sm-8">
                              <div class="input-group date">
                                 <input type="text" class="form-control datepicker" id="voucher_date" name="voucher_date" value="26-11-2019" autocomplete="off">
                                 <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                 </div>
                              </div>
                          </div>
                           </div>
                        </div>
                        <div class="col-sm-4">
                           <div class="form-group row">
                              <label for="Billing Time" class="col-sm-4">Time<span class="validation-color">*</span></label>
                              <div class=" col-sm-8">
                              <div class="input-group time">
                                 <input type="text" class="form-control datepicker" id="voucher_date" name="voucher_date" value="26-11-2019" autocomplete="off">
                                 <div class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                 </div>
                              </div>
                          </div>
                           </div>
                        </div>
                        <div class="col-sm-4">
                           <div class="form-group row">
                              <label for="Customer Name" class="col-sm-4">Customer<span class="validation-color">*</span></label>
                             <!--  <div class="col-sm-8">
                                 <select class="form-control select2">
                                    <option>Ajay</option>
                                    <option>Dhone</option>
                                    <option>Sachin</option>
                                 </select>
                              </div> -->
                               <div class="col-sm-8">
                                 <input class="form-control">
                              </div>
                              <span class="validation-color" id="error"></span>
                           </div>
                        </div>
                        <div class="col-sm-4">
                           <div class="form-group row">
                              <label for="Telephone" class="col-sm-4">Telephone<span class="validation-color">*</span></label>
                              <div class="col-sm-8">
                                 <input class="form-control">
                              </div>
                              <span class="validation-color" id="error"></span>
                           </div>
                        </div>
                        <div class="col-sm-4">
                           <div class="form-group row">
                              <label for="Salesman Code" class="col-sm-4">Salesman<span class="validation-color">*</span></label>
                             <!--  <div class="col-sm-8">
                                 <select class="form-control select2">
                                    <option>Ajay</option>
                                    <option>Dhone</option>
                                    <option>Sachin</option>
                                 </select>
                              </div> -->
                               <div class="col-sm-8">
                                 <input class="form-control">
                              </div>
                              <span class="validation-color" id="error"></span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="row">
                        <div class="col-sm-12">
                           <div class="form-group">
                              <label for="Address">Address</label>
                              <textarea class="form-control" rows="2"></textarea>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
            <table id="billing" class="table table-bordered table-striped table-hover table-responsive">
               <thead>
                  <tr>
                     <th>Items</th>
                     <th>Code</th>
                     <th width="30%">Description</th>
                     <th>Quantity</th>
                     <th>Discount</th>
                     <th>Rate</th>
                     <th>Value</th>
                     <th>S/R</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>7845514</td>
                     <td>AA-4545512</td>
                     <td>Dummy text is text that is used</td>
                     <td>69</td>
                     <td>30%</td>
                     <td>78956423.00</td>
                     <td>54562321.00</td>
                     <td>621</td>
                  </tr>
                  <tr>
                     <td>5413485</td>
                     <td>AA-89494156</td>
                     <td>Dummy text is text that is used</td>
                     <td>9</td>
                     <td>20%</td>
                     <td>989656.00</td>
                     <td>34741.00</td>
                     <td>846</td>
                  </tr>
                  <tr>
                     <td>7564</td>
                     <td>AA-91859246</td>
                     <td>Dummy text is text that is used</td>
                     <td>54</td>
                     <td>30%</td>
                     <td>66458795.00</td>
                     <td>3216587.00</td>
                     <td>21</td>
                  </tr>
                   <tr>
                     <td>7564</td>
                     <td>AA-91859246</td>
                     <td>Dummy text is text that is used</td>
                     <td>54</td>
                     <td>30%</td>
                     <td>66458795.00</td>
                     <td>3216587.00</td>
                     <td>21</td>
                  </tr>
                   <tr>
                     <td>7564</td>
                     <td>AA-91859246</td>
                     <td>Dummy text is text that is used</td>
                     <td>54</td>
                     <td>30%</td>
                     <td>66458795.00</td>
                     <td>3216587.00</td>
                     <td>21</td>
                  </tr>
                   <tr>
                     <td>7564</td>
                     <td>AA-91859246</td>
                     <td>Dummy text is text that is used</td>
                     <td>54</td>
                     <td>30%</td>
                     <td>66458795.00</td>
                     <td>3216587.00</td>
                     <td>21</td>
                  </tr>
                  <tr>
                     <td>698532470</td>
                     <td>AA-956273001</td>
                     <td>Dummy text is text that is used</td>
                     <td>92</td>
                     <td>60%</td>
                     <td>6654231587.00</td>
                     <td>964231057.00</td>
                     <td>37</td>
                  </tr>
               </tbody>
               <tfoot>
                  <tr>
                     <th>Item Number</th>
                     <th>Code</th>
                     <th width="30%">Description</th>
                     <th>Quantity</th>
                     <th>Discount</th>
                     <th>Rate</th>
                     <th>Value</th>
                     <th>S/R</th>
                  </tr>
               </tfoot>
            </table>
            <hr>
            <form class="inline-form">
               <div class="row">
                  <div class="col-sm-3">
                     <div class="form-group row">
                        <label for="Sold Quantity" class="col-sm-4">Sold Qty<span class="validation-color">*</span></label>
                        <div class="col-sm-8">	                                    	
                           <input class="form-control">    	
                        </div>
                        <span class="validation-color" id="error"></span>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="form-group row">
                        <label for="Sold Item" class="col-sm-4">Sold Item<span class="validation-color">*</span></label>
                        <div class="col-sm-8">
                           <input class="form-control">
                        </div>
                        <span class="validation-color" id="error"></span>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="form-group row">
                        <label for="Returned Quantity" class="col-sm-4">Returned Qty<span class="validation-color">*</span></label>
                        <div class="col-sm-8">
                           <input class="form-control">
                        </div>
                        <span class="validation-color" id="error"></span>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="form-group row">
                        <label for="Return Item" class="col-sm-4">Return Item<span class="validation-color">*</span></label>
                        <div class="col-sm-8">
                           <input class="form-control">
                        </div>
                        <span class="validation-color" id="error"></span>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="form-group row">
                        <label for="Net Amount" class="col-sm-4">Net Amt<span class="validation-color">*</span></label>
                        <div class="col-sm-8">
                           <input class="form-control">
                        </div>
                        <span class="validation-color" id="error"></span>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="form-group row">
                        <label for="Total" class="col-sm-4">Total<span class="validation-color">*</span></label>
                        <div class="col-sm-8">
                           <input class="form-control">
                        </div>
                        <span class="validation-color" id="error"></span>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="form-group row">
                        <label for="Discount" class="col-sm-4">Discount%<span class="validation-color">*</span></label>
                        <div class="col-sm-8">
                           <input class="form-control">
                        </div>
                        <span class="validation-color" id="error"></span>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="form-group row">
                        <label for="Discount Amount" class="col-sm-4">Discount Amt<span class="validation-color">*</span></label>
                        <div class="col-sm-8">
                           <input class="form-control">
                        </div>
                        <span class="validation-color" id="error"></span>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="form-group row">
                        <label for="Total Paid" class="col-sm-4">Total Paid<span class="validation-color">*</span></label>
                        <div class="col-sm-8">
                           <input class="form-control">
                        </div>
                        <span class="validation-color" id="error"></span>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="form-group row">
                        <label for="Round Off" class="col-sm-4">Round Off<span class="validation-color">*</span></label>
                        <div class="col-sm-8">
                           <input class="form-control">
                        </div>
                        <span class="validation-color" id="error"></span>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="form-group row">
                        <label for="Balance" class="col-sm-4">Balance<span class="validation-color">*</span></label>
                        <div class="col-sm-8">
                           <input class="form-control">
                        </div>
                        <span class="validation-color" id="error"></span>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="form-group row">
                        <label for="Total Value" class="col-sm-4">Total Value</label>
                        <div class="col-sm-8">
                           <input class="form-control">
                        </div>
                     </div>
                  </div>
                </div>              
                  <div class="col-sm-4 box-border">
                     <h4>Last Bill Number : ***********</h4>
                  </div>
                  <div class="col-sm-4 box-border">
                     <h4>Amount : ******45/-</h4>
                  </div>
                  <div class="col-sm-4 box-border">
                     <button type="button" class="btn btn-small btn-primary" style="margin: 3.5px 0;">Payment Methods</button>
                  </div>
            </form>
         </div>
      </div>
   </section>
</div>
<?php
   $this->load->view('layout/footer');
?>
<script type="text/javascript">
   $(document).ready(function() {
      // Setup - add a text input to each footer cell
      $('#billing tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input type="text" style="width:100px" placeholder="Search '+title+'" />' );
      } );
   
      // DataTable
      var table = $('#billing').DataTable({
      	  "scrollY": "170px",          
          "paging":   false,
          "ordering": false,
          "info":     false,
          // "searching": false,
          // "filter": true,
      });
   
      // Apply the search
      table.columns().every( function () {
          var that = this;
   
          $( 'input', this.footer() ).on( 'keyup change clear', function () {
              if ( that.search() !== this.value ) {
                  that
                      .search( this.value )
                      .draw();
              }
          } );
      } );
   } );
</script>