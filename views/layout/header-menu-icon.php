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
$GLOBALS['common_settings_amount_precision'] = $access_common_settings[0]->amount_precision;
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
        <!-- Select2 -->
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/select2/select2.min.css">
        <!-- DataTables -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/fixedHeader.dataTables.min.css">
        <link href="<?php echo base_url(); ?>assets/plugins/datatables/responsive.bootstrap.min.css" media="all" type="text/css" rel="stylesheet"/>
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>dist/css/skins/_all-skins.min.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
                folder instead of downloading all of them to reduce the load. -->
        <!-- bootstrap wysihtml5 - text editor -->
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
            #menu {
                position: absolute;
                z-index: 1111;
                color: #fff;
                cursor: pointer;
                font-size: 20px;
                top: 22%
            }
            .remaining_class, .add_row_button {
                color: #28751f;
            }
            .remove_field {
                color: red;
            }
            .mt-20 {
                margin-top: -20px
            }
            .h-130{
                min-height: 130px;
            }
            .h-200{
                min-height: 230px;
            }
            .navbar-nav > .user-menu > .dropdown-menu > .user-footer {
                background-color: #fff;
                padding: 10px;
                border-bottom: solid 1px #336b87;
                color: #336b87;
            }
            .navbar-nav > .user-menu > .dropdown-menu > .user-footer:hover {
                background-color: #d2d6de;
                padding: 10px;
                border-bottom: solid 1px #4d839e;
                color: #4d839e;
                cursor: pointer;
            }
            .notify > li > a {
                color: #4d839e;
            }
            .navbar-nav > .user-menu > .notify > .user-footer > a:hover {
                background-color: #d2d6de;
                color: #4d839e;
                cursor: pointer;
            }
            .modal-open {
                overflow: auto;
                padding-right: 0px !important;
            }
            .select2-container--open {
                z-index: 999999999;
            }
            body {
                padding-right: inherit !important;
            }
            .display-password-div:hover  + #show-password {
                display: block
            }
            .fa-bell {
                font-size: 18px
            }
            .notification-count {
                position: absolute;
                top: 6px;
                left: 25px;
                z-index: 2;
                font-size: 14px;
                padding: 0px 5px;
                color: #fff;
                border-radius: 10px;
                background: #ef0909;
            }
            .dropdown-menu-noti {
                right: 175px;
                left: auto;
            }            
            fieldset {
                display: block;
                margin-left: 2px;
                margin-right: 2px;
                padding-top: 0.35em;
                padding-bottom: 0.625em;
                padding-left: 0.75em;
                padding-right: 0.75em;
                border: 1px solid #fefefe;
            }   
            legend{
                display: block;
                width: auto;
                padding: 0;
                margin-bottom: 0px;               
                line-height: inherit;
                color: #ffeb00;
                border: 0;               
                border-bottom: none; 
            }

            legend h3{
                font-size: 20px;
                margin: 0;
                padding: 0 4px;
            }            
        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="loader">
        </div>
        <div class="wrapper">
            <header class="main-header">
                <a href="<?php echo base_url() ?>auth/dashboard" class="logo">
                    <span style="font-size: 24px;">
                        <img style='width:110px' src = "<?= base_url('assets/images/Aodry- white-09.svg') ?>">
                    </span>
                </a>
                <nav class="navbar navbar-static-top">
                    <a href="javascript:void();" onclick="openNav()" class="sidebar-toggle" data-toggle="push-menu" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a> 
                    <div class="col-sm-2"  id="financial_year_view" style="display: <?php
                    if (!$this->session->userdata("SESS_F_Y_PASSWORD")) {
                        echo "none";
                    }
                    ?>;">
                        <div class="financial-block">
                            <div class="form-group"  >
                                <?php
                                $financial_year_head = $this->db->where('delete_status', 0)->get('financial_year')->result();
                                ?>
                                <select class="form-control select2" id="financial_year_head">
                                    <?php
                                    foreach ($financial_year_head as $key => $value) {
                                        ?>
                                        <option value="<?= $value->financial_year_id ?>" 
                                        <?php
                                        if ($value->financial_year_id == $this->session->userdata('SESS_FINANCIAL_YEAR_ID')) {
                                            echo "selected";
                                        }
                                        ?>>FY<?= $value->financial_year_title ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 col-sm-offset-3" id="submit_f_y" style="display:<?php
                    if ($this->session->userdata("SESS_F_Y_PASSWORD")) {
                        echo "none";
                    }
                    ?>">
                        <div style="padding-top: 10px;">
                            <button class="btn btn-default btn-sm" > <!-- data-toggle="modal" data-target="#financial_year_model" -->
                                <?php echo 'FY ' . $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'); ?>
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="navbar-custom-menu">
                            <ul class="nav navbar-nav">
                                <li class="dropdown notifications-menu">
                                    <a href="javascript:void();" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-fw fa-bell-o">
                                        </i>
                                        <span class="label label-warning notification-count" id="n_total">
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="header">You have 1675 notifications
                                        </li>
                                        <li>
                                            <ul class="menu">
                                                <li>
                                                    <a href="<?= base_url('Notification/partial_sales') ?>" > 
                                                        <i class="fa fa-fw fa-shopping-cart text-aqua">
                                                        </i> Sales Pending - 
                                                        <span id="s_pending">
                                                            <?= $sales ?>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?= base_url('Notification/notify_purchase') ?>"> 
                                                        <i class="fa fa-fw fa-shopping-cart text-aqua">
                                                        </i> Purchase Details - 
                                                        <span id="p_pending">
                                                            <?= $pur ?>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li> 
                                                    <a href="<?= base_url('Notification/notify_expensebill') ?>" >
                                                        </i> 
                                                        <i class="fa fa-fw fa-shopping-cart text-aqua">
                                                        </i> Expense Bills - 
                                                        <span id="e_pending">
                                                            <?= $exp ?> 
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="divider">
                                        </li>
                                        <li class="footer">
                                            <a href="javascript:void();">View all
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="user user-menu">
                                    <a href="javascript:void();">
                                        <img src="<?php echo base_url(); ?>assets/dist/img/person2.jpg" class="user-image" alt="User Image">
                                        <span class="hidden-xs">
                                            <?php echo $this->session->userdata('SESS_USERNAME'); ?>
                                        </span>
                                    </a>
                                </li>
                                <li class="user user-menu">
                                    <a href="<?php echo base_url() ?>auth/logout" >
                                        <i class="fa fa-fw fa-sign-out" >
                                        </i>Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>
        </div>
        <div class="backdrop">
        </div>
        <div id="myCustomNav" class="overlay">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×
            </a>
            <div class="overlayCustom-content">                                                                        
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">                                                              
                            <?php
                            $section = $this->config->item('section');
                            ?>
                            <?php
                            if (!isset($active_add)) {
                                $active_add = array();
                            }
                            if (!isset($active_edit)) {
                                $active_edit = array();
                            }
                            if (!isset($active_delete)) {
                                $active_delete = array();
                            }
                            if (!isset($active_view)) {
                                $active_view = array();
                            }
                            if (in_array($this->config->item('quotation_module'), $active_modules)) {
                                if (in_array($this->config->item('quotation_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-170">                                                                                              
                                        <fieldset>
                                            <legend><h3 class="yellow">Sales</h3></legend>
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('quotation_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('quotation'); ?>">
                                                            <span class="fa fa-eye"> View Quotation</span>
                                                        </a>
                                                    </li>
                                                <?php }
                                                ?>
                                                <?php
                                                if (in_array($this->config->item('quotation_module'), $active_add)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('quotation/add'); ?>">
                                                            <span class="fa fa-plus"> Add Quotation
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php }
                                                ?>
                                                <?php
                                                if (in_array($this->config->item('sales_module'), $active_modules)) {
                                                    if (in_array($this->config->item('sales_module'), $active_view)) {
                                                        ?>
                                                        <?php
                                                        if (in_array($this->config->item('sales_module'), $active_view)) {
                                                            ?>
                                                            <li>
                                                                <a href="<?php echo base_url('sales'); ?>">
                                                                    <span class="fa fa-eye"> view Sales
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php
                                                        if (in_array($this->config->item('sales_module'), $active_add)) {
                                                            ?>
                                                            <li>
                                                                <a href="<?php echo base_url('sales/add'); ?>">
                                                                    <span class="fa fa-plus"> Add Sales
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </ul>  
                                        </fieldset>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <?php
                            if (in_array($this->config->item('credit_note_module'), $active_modules) || in_array($this->config->item('debit_note_module'), $active_modules)) {
                                if (in_array($this->config->item('credit_note_module'), $active_view) || in_array($this->config->item('debit_note_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-170">
                                        <ul class="list-icons">
                                            <li>
                                                <h3 class="yellow nav-custom-title text-center">
                                                    <span>Sales Credit/Debit Note
                                                    </span>
                                                </h3>
                                                <ul class="list-icons check-square">
                                                    <?php
                                                    if (in_array($this->config->item('credit_note_module'), $active_view)) {
                                                        ?>
                                                        <li>
                                                            <a href="<?php echo base_url('credit_note'); ?>">
                                                                <span class="fa fa-eye"> view Credit Note
                                                                </span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php
                                                    if (in_array($this->config->item('credit_note_module'), $active_add)) {
                                                        ?>
                                                        <li>
                                                            <a href="<?php echo base_url('credit_note/add'); ?>">
                                                                <span class="fa fa-plus"> Add Credit Note
                                                                </span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php
                                                    if (in_array($this->config->item('debit_note_module'), $active_view)) {
                                                        ?>
                                                        <li>
                                                            <a href="<?php echo base_url('debit_note'); ?>">
                                                                <span class="fa fa-eye"> view Debit Note
                                                                </span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php
                                                    if (in_array($this->config->item('debit_note_module'), $active_add)) {
                                                        ?>
                                                        <li>
                                                            <a href="<?php echo base_url('debit_note/add'); ?>">
                                                                <span class="fa fa-plus"> Add Debit Note
                                                                </span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <div class="col-md-4 h-170">
                                <fieldset>
                                    <legend>
                                        <h3 class="yellow">Sales Credit/Debit Note</h3>
                                    </legend>                                                                                               
                                    <ul class="list-icons check-square">
                                        <li>
                                            <a href="<?php echo base_url(''); ?>sales_credit_note">
                                                <i class="fa fa-eye"> View Credit Note</i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(''); ?>sales_credit_note/add">
                                                <i class="fa fa-plus"> Add View Credit Note</i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(''); ?>sales_debit_note">
                                                <i class="fa fa-eye">   View Debit Note</i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(''); ?>sales_debit_note/add">
                                                <i class="fa fa-plus"> Add View Debit Note</i>
                                            </a>
                                        </li>
                                    </ul>
                                </fieldset>
                            </div>
                            <div class="col-md-4 h-170">                                                                                                
                                <fieldset>
                                    <legend><h3 class="yellow">Purchase</h3></legend>
                                    <?php
                                    if (in_array($this->config->item('purchase_order_module'), $active_modules)) {
                                        if (in_array($this->config->item('purchase_order_module'), $active_view)) {
                                            ?>
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('purchase_order_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('purchase_order'); ?>">
                                                            <span class="fa fa-eye"> view Purchase Order
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('purchase_order_module'), $active_add)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('purchase_order/add'); ?>">
                                                            <span class="fa fa-plus"> Add Purchase Order
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>                                                                                                                
                                            </ul>                                                                                                   
                                            <?php
                                        }
                                    }
                                    ?>
                                    <?php
                                    if (in_array($this->config->item('purchase_module'), $active_modules)) {
                                        if (in_array($this->config->item('purchase_module'), $active_view)) {
                                            ?>                                                                                            
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('purchase_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('purchase'); ?>">
                                                            <span class="fa fa-eye"> view Purchase
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('purchase_module'), $active_add)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('purchase/add'); ?>">
                                                            <span class="fa fa-plus"> Add Purchase
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </fieldset>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <?php
                            if (in_array($this->config->item('purchase_credit_note_module'), $active_modules) || in_array($this->config->item('purchase_debit_note_module'), $active_modules)) {
                                if (in_array($this->config->item('purchase_credit_note_module'), $active_view) || in_array($this->config->item('purchase_debit_note_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-170">
                                        <fieldset>
                                            <legend><h3 class="yellow">Purchase Credit/Debit Note</h3></legend>
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('purchase_credit_note_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('purchase_credit_note'); ?>">
                                                            <span class="fa fa-eye"> view Credit Note
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('purchase_credit_note_module'), $active_add)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('purchase_credit_note/add'); ?>">
                                                            <span class="fa fa-plus"> Add Credit Note
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('purchase_debit_note_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('purchase_debit_note'); ?>">
                                                            <span class="fa fa-eye"> view Debit Note
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('purchase_debit_note_module'), $active_add)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('purchase_debit_note/add'); ?>">
                                                            <span class="fa fa-plus"> Add Debit Note
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </fieldset>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <div class="col-md-4 h-170">
                                <fieldset>
                                    <legend>
                                        <h3 class="yellow">Stocks</h3>
                                    </legend>                                                                                               
                                    <ul class="list-icons check-square">
                                        <li>
                                            <a href="<?php echo base_url(''); ?>product_stock">
                                                <i class="fa fa-bar-chart"></i>Products Stock
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(''); ?>product/sales_product">
                                                <i class="fa fa-area-chart" aria-hidden="true"></i> Sales Stock</i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(''); ?>damaged_stock">
                                               <i class="fa fa-pie-chart" aria-hidden="true"></i> Damaged stock</i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(''); ?>missing_stock">
                                               <i class="fa fa-bar-chart" aria-hidden="true"></i> Missing stock</i>
                                            </a>
                                        </li>                    
                                    </ul>
                                </fieldset>
                            </div>
                            <div class="col-md-4 h-170">                                                                                                
                                <fieldset>
                                    <legend><h3 class="yellow">Balance Reports</h3></legend>                                                                                               
                                    <ul class="list-icons check-square">
                                        <li>
                                            <a href="<?php echo base_url('trial_balance'); ?>">
                                                <span class="fa-balance-scale">Trial Balance</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url('balance_sheet'); ?>">
                                                <i class="fa fa-window-restore" aria-hidden="true"></i> Balance Sheet
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url('ledger_view'); ?>">
                                                <i class="fa fa-eye"></i>Ledger View
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url('profit_loss'); ?>">
                                                <span class="fa fa-">Profit Loss</span>
                                            </a>
                                        </li>
                                    </ul>
                                </fieldset>
                            </div>
                            <?php
                            if (in_array($this->config->item('purchase_return_module'), $active_modules)) {
                                if (in_array($this->config->item('purchase_return_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-130">                                                                                                
                                        <fieldset>
                                            <legend><h3 class="yellow">Purchase Return</h3></legend>                                                                                                
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('purchase_return_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('purchase_return'); ?>">
                                                            <span class="fa fa-eye"> view Purchase Return
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('purchase_return_module'), $active_add)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('purchase_return/add'); ?>">
                                                            <span class="fa fa-plus"> Add Purchase Return
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </fieldset>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <?php
                            if (in_array($this->config->item('expense_bill_module'), $active_modules) || in_array($this->config->item('expense_module'), $active_modules)) {
                                if (in_array($this->config->item('expense_bill_module'), $active_view) || in_array($this->config->item('expense_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-130">                                                                                               
                                        <fieldset>
                                            <legend><h3 class="yellow">Expense</h3></legend>                                                                                                        
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('expense_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url("expense"); ?>">
                                                            <span>Expense
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('expense_bill_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('expense_bill'); ?>">
                                                            <span>Expense Bill
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>                                                                                                   
                                            </ul>
                                        </fieldset>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <?php
                            if (in_array($this->config->item('delivery_challan_module'), $active_modules)) {
                                if (in_array($this->config->item('delivery_challan_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-130">

                                        <fieldset>
                                            <legend><h3 class="yellow">Delivery Challan</h3></legend>                                                                                                  
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('delivery_challan_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('delivery_challan'); ?>">
                                                            <span class="fa fa-eye"> view Delivery Challan
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('delivery_challan_module'), $active_add)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('delivery_challan/add'); ?>">
                                                            <span class="fa fa-plus"> Add Delivery Challan
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </fieldset>
                                    </div>
                                    <?php
                                }
                            }
                            ?>                                                                                   

                            <?php
                            if (in_array($this->config->item('receipt_voucher_module'), $active_modules) || in_array($this->config->item('payment_voucher_module'), $active_modules) || in_array($this->config->item('advance_voucher_module'), $active_modules) || in_array($this->config->item('refund_voucher_module'), $active_modules)) {
                                if (in_array($this->config->item('receipt_voucher_module'), $active_view) ||
                                        in_array($this->config->item('payment_voucher_module'), $active_view) ||
                                        in_array($this->config->item('advance_voucher_module'), $active_view) ||
                                        in_array($this->config->item('refund_voucher_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-200">
                                        <fieldset><legend><h3 class="yellow">Vouchers</h3></legend>                                                                                               
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('advance_voucher_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('advance_voucher'); ?>">
                                                            <span>Advance Voucher
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('receipt_voucher_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('receipt_voucher'); ?>">
                                                            <span>Receipt Voucher
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('payment_voucher_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('payment_voucher'); ?>">
                                                            <span>Payment Voucher
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?php echo base_url('boe_voucher'); ?>">
                                                            <span>BOE Voucher
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('refund_voucher_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('refund_voucher'); ?>">
                                                            <span>Refund Voucher
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>                                                                                                            
                                                <li> 
                                                    <a href="<?php echo base_url(''); ?>expense_voucher">
                                                        Expense Voucher
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo base_url(''); ?>general_voucher">
                                                        Journal Voucher
                                                    </a>
                                                </li>                                                                                                            
                                            </ul>
                                        </fieldset>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <div class="col-md-4 h-200">
                                <fieldset>
                                    <legend>
                                        <h3 class="yellow">Sales Vouchers</h3>
                                    </legend>                                                                                               
                                    <ul class="list-icons check-square">
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>sales_voucher">
                                                Sales Voucher 
                                            </a>
                                        </li>
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>sales_credit_note_voucher">
                                                Sales Credit Note Voucher 
                                            </a>
                                        </li>
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>sales_debit_note_voucher">
                                                Sales Debit Note Voucher
                                            </a>
                                        </li>
                                    </ul>
                                </fieldset>
                            </div>
                            <div class="col-md-4 h-200">
                                <fieldset>
                                    <legend>
                                        <h3 class="yellow">Purchase Vouchers</h3>
                                    </legend>                                                                                               
                                    <ul class="list-icons check-square">
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>purchase_voucher">
                                                Purchase Voucher 
                                            </a>
                                        </li>
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>purchase_credit_note_voucher">
                                                Purchase Credit Note Voucher 
                                            </a>
                                        </li>
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>purchase_debit_note_voucher">
                                                Purchase Debit Note Voucher 
                                            </a>
                                        </li>                                                                                           
                                    </ul>
                                </fieldset>
                            </div>  
                            <div class="col-md-4 h-200">
                                <fieldset>
                                    <legend>
                                        <h3 class="yellow">Ledgers</h3>
                                    </legend>                                                                                               
                                    <ul class="list-icons check-square">
                                        <li>
                                            <a href="<?php echo base_url(''); ?>advance_ledger">
                                                Advance Ledger
                                            </a>
                                        </li>
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>payment_ledger">
                                                Payment Ledger
                                            </a>
                                        </li> 
                                        <li>
                                            <a href="<?php echo base_url(''); ?>general_ledger">
                                                Journal Ledger
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(''); ?>refund_ledger">
                                                Refund Ledger
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url(''); ?>expense_ledgers">
                                                Expense Ledger
                                            </a>
                                        </li>
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>receipt_ledgers">
                                                Receipt Ledger
                                            </a>
                                        </li>
                                    </ul>
                                </fieldset>
                            </div>   
                            <div class="col-md-4 h-200">
                                <fieldset>
                                    <legend>
                                        <h3 class="yellow">Sales Ledgers</h3>
                                    </legend>                                                                                               
                                    <ul class="list-icons check-square">
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>sales_ledger">
                                                Sales Ledger
                                            </a>
                                        </li>
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>sales_credit_ledgers">
                                                Sales Credit Note Ledger
                                            </a>
                                        </li>
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>sales_debit_ledgers">
                                                Sales Debit Note Ledger 
                                            </a>
                                        </li>
                                    </ul>
                                </fieldset>
                            </div>
                            <div class="col-md-4 h-200">
                                <fieldset>
                                    <legend>
                                        <h3 class="yellow">Purchase Ledgers</h3>
                                    </legend>                                                                                               
                                    <ul class="list-icons check-square">
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>/purchase_ledger">
                                                Purchase Ledger
                                            </a>
                                        </li>
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>purchase_credit_ledger">
                                                Purchase Credit Note Ledger 
                                            </a>
                                        </li>
                                        <li> 
                                            <a href="<?php echo base_url(''); ?>purchase_debit_ledger">
                                                Purchase Debit Note Ledger
                                            </a>
                                        </li>
                                    </ul>
                                </fieldset>
                            </div>
                            <div class="col-md-4 h-170">
                                <?php
                                if (in_array($this->config->item('product_module'), $active_modules) || in_array($this->config->item('service_module'), $active_modules)) {
                                    if (in_array($this->config->item('product_module'), $active_view) || in_array($this->config->item('service_module'), $active_view)) {
                                        ?>
                                        <fieldset><legend><h3 class="yellow">Items</h3></legend>
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('product_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('product'); ?>">
                                                            <span class="fa fa-eye"> view Products
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('product_module'), $active_add)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('product/add'); ?>">
                                                            <span class="fa fa-plus"> Add Product
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('service_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('service'); ?>">
                                                            <span class="fa fa-eye"> view Services
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('service_module'), $active_add)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('service/add'); ?>">
                                                            <span class="fa fa-plus"> Add Service
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>                                                                                                
                                        </fieldset>
                                        <?php
                                    }
                                }
                                ?>
                            </div>                                                                                
                            <?php
                            if (in_array($this->config->item('supplier_module'), $active_modules) || in_array($this->config->item('user_module'), $active_modules) || in_array($this->config->item('customer_module'), $active_modules)) {
                                if (in_array($this->config->item('supplier_module'), $active_view) ||
                                        in_array($this->config->item('user_module'), $active_view) || in_array($this->config->item('customer_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-170">
                                        <fieldset><legend><h3 class="yellow">Peoples</h3></legend>
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('user_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('auth'); ?>">
                                                            <span>Users
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('customer_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('customer'); ?>">
                                                            <span>Customers
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('supplier_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('supplier'); ?>">
                                                            <span>Suppliers
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <li>
                                                    <a href="<?php echo base_url('shipping_address'); ?>">
                                                        <span>Shipping Address
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul></fieldset>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <?php
                            if (in_array($this->config->item('category_module'), $active_modules) || in_array($this->config->item('subcategory_module'), $active_modules)) {
                                if (in_array($this->config->item('category_module'), $active_view) || in_array($this->config->item('subcategory_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-170">
                                        <fieldset>
                                            <legend>
                                                <h3 class="yellow">Category</h3></legend>
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('category_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url("category"); ?>">
                                                            <span class="fa fa-eye"> view Category
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?php echo base_url("category/add"); ?>">
                                                            <span class="fa fa-plus"> Add category
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('subcategory_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url("subcategory"); ?>">
                                                            <span class="fa fa-eye"> view Subcategory
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?php echo base_url("subcategory/add"); ?>">
                                                            <span class="fa fa-plus"> Add Subcategory
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </fieldset>
                                    </div>
                                    <?php
                                }
                            }
                            ?>

                            <?php
                            if (in_array($this->config->item('report_module'), $active_modules)) {
                                if (in_array($this->config->item('report_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-170">
                                        <fieldset><legend><h3 class="yellow">Reports</h3></legend>
                                            <ul class="list-icons check-square">
                                                <li>
                                                    <a href="<?php echo base_url('report/all_reports'); ?>">
                                                        <span>All Reports
                                                        </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo base_url('report/tds_report_sales'); ?>">
                                                        <span>TDS Sales Report
                                                        </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo base_url('report/tcs_report_sales'); ?>">
                                                        <span>TCS Sales Report
                                                        </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo base_url('report/tcs_report_purchase'); ?>">
                                                        <span>TCS Purchase Report
                                                        </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo base_url('report/tds_report_expense'); ?>">
                                                        <span>TDS Expense Report
                                                        </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo base_url('report/gst_report'); ?>">
                                                        <span>GST Report
                                                        </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo base_url('report/gst_credit_note_report'); ?>">
                                                        <span>GST Report (Sales Credit Note)
                                                        </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo base_url('report/gst_debit_note_report'); ?>">
                                                        <span>GST Report (Sales Debit Note)
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </fieldset>
                                    </div>                                                                                       
                                    <?php
                                }
                            }
                            ?>                                                                                    
                            <?php
                            if (in_array($this->config->item('tax_module'), $active_modules) || in_array($this->config->item('discount_module'), $active_modules) ||
                                    in_array($this->config->item('uqc_module'), $active_modules)
                            ) {
                                if (in_array($this->config->item('tax_module'), $active_view) ||
                                        in_array($this->config->item('discount_module'), $active_view) || in_array($this->config->item('uqc_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-170">
                                        <fieldset><legend><h3 class="yellow">Features</h3></legend>
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('tax_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url("tax"); ?>">
                                                            <span>Tax
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('discount_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url("discount"); ?>">
                                                            <span>Discount
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('uqc_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url("uqc"); ?>">
                                                            <span>UQC
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?php echo base_url("varients"); ?>">
                                                            <span>Variant
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <li><a href="<?php echo base_url("bank_account"); ?>">										<span>Bank Account</span>					</a>
                                                </li>	                                                                                                      
                                            </ul>
                                        </fieldset>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <div class="col-md-4 h-170">
                                <fieldset>
                                    <legend><h3 class="yellow">Settings</h3></legend>
                                    <?php
                                    if (in_array($this->config->item('user_module'), $active_modules) || in_array($this->config->item('email_module'), $active_modules) ||
                                            in_array($this->config->item('privilege_module'), $active_modules) ||
                                            in_array($this->config->item('log_module'), $active_modules)
                                    ) {
                                        if (in_array($this->config->item('user_module'), $active_view) ||
                                                in_array($this->config->item('email_module'), $active_view) || in_array($this->config->item('privilege_module'), $active_view) || in_array($this->config->item('log_module'), $active_view)) {
                                            ?>
                                            <ul class="list-icons check-square">
                                                <?php if (in_array($this->config->item('user_module'), $active_view)) { ?>
                                                    <li><a href="<?php echo base_url('company_setting') ?>">
                                                            <span>Company Settings</span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('email_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('email_setup'); ?>">
                                                            <span>Email Setup
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('privilege_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('module_settings'); ?>">
                                                            <span>Module Settings
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('log_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('log'); ?>">
                                                            <span>Logs
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>                                                                                               
                                            <?php
                                        }
                                    }
                                    ?>
                                    <?php
                                    if (in_array($this->config->item('email_module'), $active_modules) || in_array($this->config->item('notes_module'), $active_modules)
                                    ) {
                                        if (in_array($this->config->item('email_module'), $active_view) ||
                                                in_array($this->config->item('notes_module'), $active_view)) {
                                            ?>                                                                                          
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('notes_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('note_template'); ?>">
                                                            <span>Note Template
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php
                                                if (in_array($this->config->item('email_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('email_template'); ?>">
                                                            <span>Email Template
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                            <?php
                                        }
                                    }
                                    ?>
                                </fieldset>
                            </div>
                            <?php
                            if (in_array($this->config->item('news_updates_module'), $active_modules)) {
                                if (in_array($this->config->item('news_updates_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-170">
                                        <fieldset>
                                            <legend>
                                                <h3 class="yellow">News And Updates</h3>
                                            </legend>                                                                                            
                                            <ul class="list-icons check-square">
                                                <?php
                                                if (in_array($this->config->item('news_updates_module'), $active_view)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('newsupdates'); ?>">
                                                            <span class="fa fa-eye"> view News And Updates
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                                if (in_array($this->config->item('news_updates_module'), $active_add)) {
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo base_url('newsupdates/add'); ?>">
                                                            <span class="fa fa-plus"> Adds And Updates
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </fieldset>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <?php
                            if (in_array($this->config->item('bank_account_module'), $active_modules) || in_array($this->config->item('bank_reconciliation_module'), $active_modules)) {
                                if (in_array($this->config->item('bank_account_module'), $active_view) || in_array($this->config->item('bank_reconciliation_module'), $active_view)) {
                                    ?>
                                    <div class="col-md-4 h-170">
                                        <fieldset>
                                            <legend>
                                                <h3 class="yellow">Banking</h3>
                                            </legend>                                                                                               
                                            <ul class="list-icons check-square">
                                                <?php if (in_array($this->config->item('bank_account_module'), $active_view)) { ?>
                                                    <li>
                                                        <a href="<?php echo base_url("bank_account"); ?>"><span>Bank Account</span></a>
                                                    </li>
                                                    <li>
                                                        <a href="<?php echo base_url(''); ?>financial_year">
                                                            Financial Year                                                                                  
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?php echo base_url(''); ?>groupLedgers">
                                                            Group and Ledgers                                                                                   
                                                        </a>
                                                    </li>
                                                <?php } ?>	                                                                                                       		
                                            </ul>                                                                                                   
                                        </fieldset>
                                    </div>
                                    <?php
                                }
                            }
                            ?>                                                                                                                                                               
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="financial_year_model" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;
                        </button>
                        <h4 class="modal-title">Authentication Password
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input class="form-control" type="password" class="display-password-div" id="f_y_password" placeholder="please enter password" name="password">
                            <p class="text-danger">
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" id="get_password">Submit
                        </button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <aside class="main-sidebar">
            <section class="sidebar">
                <ul class="sidebar-menu tree" data-widget="tree">
                    <li class="treeview">
                        <a href="<?php echo base_url() ?>auth/dashboard">
                            <i class="fa fa-fw fa-dashboard">
                            </i> 
                            <span>Dashboard
                            </span>
                        </a>
                    </li>                                                                            
                    <li class="treeview">
                        <a href="javascript:void();">
                            <i class="fa fa-fw fa-money">
                            </i> 
                            <span>Ledger
                            </span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right">
                                </i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="<?php echo base_url("advance_ledger"); ?>">
                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i>Advance Ledger
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('payment_ledger'); ?>">
                                    <i class="fa fa-fw fa-gg" aria-hidden="true">
                                    </i>Payment Ledger
                                </a>
                            </li> 
                            <li>
                                <a href="<?php echo base_url("general_ledger"); ?>">
                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i> Journal Ledger
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url("refund_ledger"); ?>">
                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i> Refund Ledger
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url(); ?>expense_ledgers">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Expense Ledger
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('receipt_ledgers'); ?>">
                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i>Receipt Ledger
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('purchase_ledger'); ?>">
                                    <i class="fa fa-fw fa-gg" aria-hidden="true">
                                    </i>Purchase Ledger
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('purchase_credit_ledger'); ?>">
                                    <i class="fa fa-fw fa-gg" aria-hidden="true">
                                    </i>Purchase Credit Note Ledger 
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('purchase_debit_ledger'); ?>">
                                    <i class="fa fa-fw fa-gg" aria-hidden="true">
                                    </i>Purchase Debit Note Ledger
                                </a>
                            </li>
                            <li> 
                                <a href="<?= base_url(); ?>sales_ledger">
                                    <i class="fa fa-fw fa-calendar-plus-o" aria-hidden="true">
                                    </i>Sales Ledger
                                </a>
                            </li>
                            <li> 
                                <a href="<?= base_url(); ?>sales_credit_ledgers">
                                    <i class="fa fa-fw fa-calendar-plus-o" aria-hidden="true">
                                    </i>Sales Credit Note Ledger
                                </a>
                            </li>
                            <li> 
                                <a href="<?= base_url(); ?>sales_debit_ledgers">
                                    <i class="fa fa-fw fa-calendar-plus-o" aria-hidden="true">
                                    </i>Sales Debit Note Ledger 
                                </a>
                            </li>
                        </ul>
                    </li>                                                                          
                    <li class="treeview">
                        <a href="javascript:void();">
                            <i class="fa fa-fw fa-files-o">
                            </i>
                            <span>Vouchers
                            </span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right">
                                </i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li> 
                                <a href="<?php echo base_url('sales_voucher'); ?>">
                                    <i class="fa fa-fw fa-money" aria-hidden="true">
                                    </i>Sales Voucher 
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('sales_credit_note_voucher'); ?>">
                                    <i class="fa fa-fw fa-money" aria-hidden="true">
                                    </i>Sales Credit Note Voucher 
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('sales_debit_note_voucher'); ?>">
                                    <i class="fa fa-fw fa-money" aria-hidden="true">
                                    </i>Sales Debit Note Voucher
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('purchase_voucher'); ?>">
                                    <i class="fa fa-fw fa-money" aria-hidden="true">
                                    </i>Purchase Voucher 
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('purchase_credit_note_voucher'); ?>">
                                    <i class="fa fa-fw fa-money" aria-hidden="true">
                                    </i>Purchase Credit Note Voucher 
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('purchase_debit_note_voucher'); ?>">
                                    <i class="fa fa-fw fa-money" aria-hidden="true">
                                    </i>Purchase Debit Note Voucher 
                                </a>
                            </li>
                            <li> <a href="<?php echo base_url('payment_voucher'); ?>">				<i class="fa fa-fw fa-money" aria-hidden="true">					</i>Payment Voucher </a>
                            </li>
                            <li> <a href="<?php echo base_url('boe_voucher'); ?>">
                                    <i class="fa fa-fw fa-money" aria-hidden="true">
                                    </i>BOE Voucher </a>
                            </li>
                            <li>
                                <a href="<?= base_url('advance_voucher'); ?>">
                                    <i class="fa fa-fw fa-group">
                                    </i> 
                                    <span>Advance Voucher
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('refund_voucher'); ?>">
                                    <i class="fa fa-fw fa-group">
                                    </i> 
                                    <span>Refund Voucher
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('receipt_voucher'); ?>">
                                    <i class="fa fa-fw fa-group">
                                    </i> 
                                    <span>Receipt voucher
                                    </span>                                                                                 
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('expense_voucher'); ?>">
                                    <i class="fa fa-fw fa-gg" aria-hidden="true">
                                    </i>Expense Voucher 
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url("general_voucher"); ?>">
                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i> Journal Voucher
                                </a>
                            </li>                                                                                    
                        </ul>
                    </li>                                                                            
                    <li class="treeview">
                        <a href="javascript:void();">
                            <i class="fa fa-fw fa-shopping-cart">
                            </i>
                            <span>Sales
                            </span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right">
                                </i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="<?= base_url(); ?>sales">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Add / 
                                    <i class="fa fa-fw fa-eye">
                                    </i>View Sales
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url(); ?>quotation">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Add / 
                                    <i class="fa fa-fw fa-eye">
                                    </i>View Quotation
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url(); ?>sales_credit_note">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Add / 
                                    <i class="fa fa-fw fa-eye">
                                    </i>View Credit Note
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url(); ?>sales_debit_note">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Add / 
                                    <i class="fa fa-fw fa-eye">
                                    </i>View Debit Note
                                </a>
                            </li>
                        </ul>
                    </li>                                                                            
                    <li>
                        <a href="javascript:void();">
                            <i class="fa fa-fw fa-bar-chart">
                            </i> 
                            <span>Purchase
                            </span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right">
                                </i>
                            </span>
                        </a>
                        <ul class="treeview-menu">  
                            <li>
                                <a href="<?= base_url(); ?>purchase_order">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Add / 
                                    <i class="fa fa-fw fa-eye">
                                    </i>View Purchase Order
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url(); ?>purchase">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Add / 
                                    <i class="fa fa-fw fa-eye">
                                    </i>View Purchase
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url(); ?>purchase_credit_note">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Add / 
                                    <i class="fa fa-fw fa-eye">
                                    </i>View Credit Note
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url(); ?>purchase_debit_note">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Add / 
                                    <i class="fa fa-fw fa-eye">
                                    </i>View Debit Note
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url(); ?>delivery_challan">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Add / 
                                    <i class="fa fa-fw fa-eye">
                                    </i> Delivery Challan
                                </a>
                            </li>																							
                            <li>
                                <a href="<?= base_url('purchase_return'); ?>">
                                    <i class="fa fa-fw fa-plus" aria-hidden="true">
                                    </i>Purchase Return
                                </a>
                            </li> 
                            <li>
                                <a href="<?= base_url(); ?>boe">
                                    <i class="fa fa-fw fa-plus">
                                    </i>BOE
                                </a>
                            </li>
                            <!--<li><a href="javascript:void(0);"><i class="fa fa-fw fa-credit-card" aria-hidden="true"></i>Purchase Order</a></li>-->
                        </ul>
                    </li>
                    <li>
                        <a href="<?= base_url('expense_bill'); ?>">
                            <i class="fa fa-fw fa-group">
                            </i> 
                            <span>Expense
                            </span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right">
                                </i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="<?= base_url('expense'); ?>">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Add / 
                                    <i class="fa fa-fw fa-eye">
                                    </i>View Expense
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('expense_bill'); ?>">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Add / 
                                    <i class="fa fa-fw fa-eye">
                                    </i>View Expense Bill
                                </a>
                            </li>                                                                              
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void();">
                            <i class="fa fa-fw fa-bar-chart">
                            </i> 
                            <span>Stocks
                            </span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right">
                                </i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="<?= base_url('product_stock'); ?>">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Products Stock
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('product/sales_product'); ?>">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Sales Stock
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('damaged_stock'); ?>">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Damaged stock
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('missing_stock'); ?>">
                                    <i class="fa fa-fw fa-plus">
                                    </i>Missing stock
                                </a>
                            </li>            
                        </ul>
                    </li> 
                    <li>
                        <a href="<?= base_url(); ?>financial_year">
                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                            <span>Financial Year
                            </span>                                                                                   
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url(); ?>groupLedgers">
                            <i class="fa fa-fw fa-group">
                            </i> 
                            <span>Group and Ledgers
                            </span>                                                                                   
                        </a>
                    </li>  

                    <li class="treeview" id="report-menu">
                        <a href="javascript:void();">
                            <i class="fa fa-fw fa-file-text-o"></i> <span>Reports</span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu" id="report-menu-items">
                            <li> <a href="<?= base_url(); ?>report/all_reports"><i class="fa fa-file-excel-o" aria-hidden="true"></i>All Reports </a></li>

                            <li>
                                <a href="<?php echo base_url('report/tds_report_sales'); ?>">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>TDS Sales Report
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url('report/tcs_report_sales'); ?>">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>TCS Sales Report
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url('report/tcs_report_purchase'); ?>">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>TCS Purchase Report
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url('report/tds_report_expense'); ?>">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>TDS Expense Report
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url('report/gst_report'); ?>">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>GST Report
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url('report/gst_credit_note_report'); ?>">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>GST Report (Sales Credit Note)
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url('report/gst_debit_note_report'); ?>">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>GST Report (Sales Debit Note)
                                </a>
                            </li>																					
                        </ul>
                    </li>
                    <li class="treeview" id="report-menu">
                        <a href="javascript:void();">
                            <i class="fa fa-fw fa-file-text-o"></i> <span>Balance Reports</span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu" id="report-menu-items">
                            <li>
                                <a href="<?php echo base_url('trial_balance'); ?>">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>Trial Balance
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url('balance_sheet'); ?>">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>Balance Sheet
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url('ledger_view'); ?>">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>Ledger View
                                </a>
                            </li>		
                            <li>
                                <a href="<?php echo base_url('profit_loss'); ?>">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>Profit Loss
                                </a>
                            </li>	
                        </ul>
                    </li>

                    <li class="treeview">
                        <a href="javascript:void();">
                            <i class="fa fa-fw fa-cogs">
                            </i>
                            <span>Settings
                            </span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right">
                                </i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="list_item1">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-fw fa-cog" aria-hidden="true">
                                    </i>Settings 
                                    <span class="fa fa-fw fa-arrow-right">
                                    </span>
                                </a>
                                <ul class="treeview-menu treeview-sub-menu">
                                    <li> 
                                        <a href="<?php echo base_url() ?>company_setting">Company Settings
                                            <span class="fa fa-fw fa-arrow-left">
                                            </span>
                                        </a>
                                    </li>                                                                                           
                                    <li> 
                                        <a href="<?php echo base_url() ?>module_settings">
                                            <i class="fa fa-fw fa-newspaper-o" aria-hidden="true">
                                            </i>Module Settings
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="list_item2">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-fw fa-envelope-o" aria-hidden="true">
                                    </i>E-mail Setting 
                                    <span class="fa fa-fw fa-arrow-right">
                                    </span>
                                </a>
                                <ul class="treeview-menu treeview-sub-menu">
                                    <!-- <li> 
     <a href="#"><i class="fa fa-fw fa-sliders" aria-hidden="true"></i>Setup E-mail	<span class="fa fa-fw fa-arrow-left"></span></a></li> -->

                                    <li> <a href="<?= base_url(); ?>email_template"> <i class="fa fa-fw fa-envelope-o" aria-hidden="true"> </i>E-mail Template<span class="fa fa-fw fa-arrow-left"></span></a></li>
                                    <li><a href="<?= base_url(); ?>note_template"><i class="fa fa-fw fa-hashtag" aria-hidden="true"></i>Note Template</a></li>
                                </ul>
                            </li>
                            <li class="list_item3">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-fw fa-object-group" aria-hidden="true">
                                    </i>Category 
                                    <span class="fa fa-fw fa-arrow-right">
                                    </span>
                                </a>
                                <ul class="treeview-menu treeview-sub-menu">
                                    <li> 
                                        <a href="<?= base_url(); ?>category">
                                            <i class="fa fa-fw fa-plus">
                                            </i>Add / 
                                            <i class="fa fa-fw fa-eye">
                                            </i>View Category
                                            <span class="fa fa-fw fa-arrow-left">
                                            </span>
                                        </a>
                                    </li>
                                    <li> 
                                        <a href="<?= base_url(); ?>subcategory">
                                            <i class="fa fa-fw fa-plus">
                                            </i>Add / 
                                            <i class="fa fa-fw fa-eye">
                                            </i>View Sub Category
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="list_item4">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-fw fa-users" aria-hidden="true">
                                    </i>People 
                                    <span class="fa fa-fw fa-arrow-right">
                                    </span>
                                </a>
                                <ul class="treeview-menu treeview-sub-menu">
                                    <li> 
                                        <a href="<?= base_url(); ?>auth">
                                            <i class="fa fa-fw fa-users" aria-hidden="true">
                                            </i>Users
                                            <span class="fa fa-fw fa-arrow-left">
                                            </span>
                                        </a>
                                    </li>
                                    <li> 
                                        <a href="<?= base_url(); ?>customer">
                                            <i class="fa fa-fw fa-users" aria-hidden="true">
                                            </i>Customers
                                        </a>
                                    </li>
                                    <li> 
                                        <a href="<?= base_url(); ?>supplier">
                                            <i class="fa fa-fw fa-users" aria-hidden="true">
                                            </i>Suppliers
                                        </a>
                                    </li>
                                    <li> 
                                        <a href="<?= base_url(); ?>shipping_address">
                                            <i class="fa fa-fw fa-users" aria-hidden="true">
                                            </i>Shipping Address
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="list_item5">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-fw fa-sitemap" aria-hidden="true">
                                    </i>Items & Services 
                                    <span class="fa fa-fw fa-arrow-right">
                                    </span>
                                </a>
                                <ul class="treeview-menu treeview-sub-menu">
                                    <li> 
                                        <a href="<?= base_url(); ?>product">
                                            <i class="fa fa-fw fa-plus">
                                            </i>Add / 
                                            <i class="fa fa-fw fa-eye">
                                            </i>View Product
                                            <span class="fa fa-fw fa-arrow-left">
                                            </span>
                                        </a>
                                    </li>
                                    <li> 
                                        <a href="<?= base_url(); ?>service">
                                            <i class="fa fa-fw fa-plus">
                                            </i>Add / 
                                            <i class="fa fa-fw fa-eye">
                                            </i>View Services
                                        </a>
                                    </li>
                                </ul>
                            </li>                                                                                    
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="javascript:void();">
                            <i class="fa fa-fw fa-cubes">
                            </i> 
                            <span>Features
                            </span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right">
                                </i>
                            </span>
                        </a>
                        <ul class="treeview-menu">                                                                                   
                            <li> 
                                <a href="<?= base_url(); ?>tax">
                                    <i class="fa fa-fw fa-calculator" aria-hidden="true">
                                    </i>Tax
                                </a>
                            </li>
                            <li> 
                                <a href="<?= base_url(); ?>discount">
                                    <i class="fa fa-fw fa-percent" aria-hidden="true">
                                    </i>Discount
                                </a>
                            </li>
                            <li> 
                                <a href="<?= base_url(); ?>uqc">
                                    <i class="fa fa-fw fa-crosshairs" aria-hidden="true">
                                    </i>UQC
                                </a>
                            </li>
                            <li> 
                                <a href="<?= base_url(); ?>hsn">
                                    <i class="fa fa-fw fa-crosshairs" aria-hidden="true">
                                    </i>HSN
                                </a>
                            </li>
                            <li> 
                                <a href="<?= base_url(); ?>varients">
                                    <i class="fa fa-fw fa-crosshairs" aria-hidden="true">
                                    </i>Variant
                                </a>
                            </li>
                            <!-- <li> <a href="javascript:void(0);"><i class="fa fa-fw fa-newspaper-o" aria-hidden="true"></i>News & Updates</a></li> -->
                        </ul>
                    </li>                                                                                                                                                      
                    <li class="treeview">
                        <a href="javascript:void();">
                            <i class="fa fa-fw fa-money"></i> <span>Banking</span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li> <a href="<?= base_url(); ?>bank_account"><i class="fa fa-fw fa-university" aria-hidden="true"></i>Bank Account </a></li>
                            <!-- <li> <a href="javascript:void(0);"><i class="fa fa-fw fa-bookmark-o" aria-hidden="true"></i>Bank Statement </a></li>
                            <li> <a href="javascript:void(0);"><i class="fa fa-fw fa-object-group" aria-hidden="true"></i>Bank to Group </a></li>
                            <li> <a href="javascript:void(0);"><i class="fa fa-fw fa-qrcode" aria-hidden="true"></i>Bank Reconciliation </a></li> -->
                        </ul>
                    </li>
                    <!-- <li><a href="pages/calendar.html">
                                                            <i class="fa fa-fw fa-clone"></i> <span>Expense</span>
                                                            <span class="pull-right-container">
                                                            <i class="fa fa-fw fa-angle-left pull-right"></i>
                                                            </span>
                                                            </a>
                                                            <ul class="treeview-menu">
                                                            <li> <a href="javascript:void(0);">Bank Account </a></li>
                                                            <li> <a href="javascript:void(0);"><i class="fa fa-fw fa-plus"></i>Add / <i class="fa fa-fw fa-eye"></i>View Expense Type</a></li>
                                                            <li> <a href="javascript:void(0);"><i class="fa fa-fw fa-plus"></i>Add / <i class="fa fa-fw fa-eye"></i>View Expense Bill</a></li>
                                                            </ul>
                                                    </li> -->
                    <!-- <li class="treeview" id="account-menu">
                            <a href="javascript:void();">
                                    <i class="fa fa-fw fa-server"></i> <span>Accounts</span>
                                    <span class="pull-right-container">
                                            <i class="fa fa-fw fa-angle-left pull-right"></i>
                                    </span>
                            </a>
                            <ul class="treeview-menu" id="account-menu-items">                                                                                   
                                    <li> <a href="javascript:void(0);"><i class="fa fa-fw fa-users" aria-hidden="true"></i>Account Group </a></li>
                                    <li> <a href="javascript:void(0);"><i class="fa fa-fw fa-object-group" aria-hidden="true"></i>Account Sub Group </a></li>
                                    <li> <a href="javascript:void(0);"><i class="fa fa-fw fa-user" aria-hidden="true"></i>Ledger </a></li>
                            </ul>
                    </li>   -->                                                                           
                </ul>
            </section>
        </aside>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#get_password").click(function () {
                    var f_y_password = $("#f_y_password").val();
                    $.ajax({
                        type: "post",
                        url: "<?= base_url('financialyear') ?>",
                        data: {
                            f_y_password: f_y_password},
                        success: function (data) {
                            if (data == "sucess") {
                                $("#financial_year_view").show();
                                $("#submit_f_y").hide();
                                $("#financial_year_model").hide();
                                $(".modal-backdrop ").hide();
                                setTimeout(function () {
                                    location.reload();
                                });
                            } else if (data == "change") {
                                $(".text-danger").text('Please Set your password in Company settings');
                            } else {
                                $(".text-danger").text('Wrong Password');
                            }
                        }
                    });
                }
                );
                var s_count = $('#s_pending').text();
                var p_count = $('#p_pending').text();
                var e_pending = $('#e_pending').text();
                var total = parseInt(s_count, 10) + parseInt(p_count, 10) + parseInt(e_pending, 10);
                $('#n_total').text(total);
            }
            );
        </script>                                                                
        <script type="text/javascript">
            $(document).ready(function () {
                $(".content").click(function () {
                    $(".treeview-menu, .treeview-sub-menu").css({
                        "display": "none",
                    });
                });
            });
            $(".skin-blue .sidebar-menu>li").click(function () {
                $(".skin-blue .sidebar-menu>li>.treeview-menu").css("margin-top", "-42px");
            }
            );
            $(".sidebar-toggle").click(function () {
                $(".skin-blue .sidebar-menu>li>.treeview-menu").css("margin-top", "0");
            }
            );
            // $(".fa-arrow-right").click(function(){
            //     $(".skin-blue .sidebar-menu>li>.treeview-menu").fadeOut();
            // });
            var list_item_master = $(".list_item1, .list_item2, .list_item3, .list_item4, .list_item5, .list_item6");
            var list_item_master1 = $(".list_item2, .list_item3, .list_item4, .list_item5, .list_item6");
            var list_item_master2 = $(".list_item1, .list_item3, .list_item4, .list_item5, .list_item6");
            var list_item_master3 = $(".list_item1, .list_item2, .list_item4, .list_item5, .list_item6");
            var list_item_master4 = $(".list_item1, .list_item2, .list_item3, .list_item5, .list_item6");
            var list_item_master5 = $(".list_item1, .list_item2, .list_item3, .list_item4, .list_item6");
            var list_item_master6 = $(".list_item1, .list_item2, .list_item3, .list_item4, .list_item5");
            $(".sidebar-menu li>a>.fa-arrow-left").click(function (event) {
                $(".treeview-sub-menu").hide();
                $(list_item_master).show();
                event.preventDefault();
                return false;
            }
            );
            $(".list_item1").click(function () {
                $(this).show();
                $(list_item_master1).hide();
            }
            );
            $(".list_item2").click(function () {
                $(this).show();
                $(list_item_master2).hide();
            }
            );
            $(".list_item3").click(function () {
                $(this).show();
                $(list_item_master3).hide();
            }
            );
            $(".list_item4").click(function () {
                $(this).show();
                $(list_item_master4).hide();
            }
            );
            $(".list_item5").click(function () {
                $(this).show();
                $(list_item_master5).hide();
            }
            );
            $(".list_item6").click(function () {
                $(this).show();
                $(list_item_master6).hide();
            }
            );
        </script>