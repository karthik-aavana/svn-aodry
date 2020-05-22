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
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>AODRY Accounting Software</title>
        <link rel="shortcut icon" type="image/png" href="<?php echo base_url('assets/images/favicon.png'); ?>" />        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>lib/font-awesome/4.5.0/css/font-awesome.min.css">
        <link href="<?php echo base_url('assets/'); ?>dist/css/jquery.mCustomScrollbar.css" media="all" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/moving-letters.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>lib/ionicons/css/ionicons.min.css">
        <link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/fullcalendar/fullcalendar.min.css">
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
        <link type="text/css" media="all" href="<?php echo base_url('assets/'); ?>css/reset.css" rel="stylesheet" />
        <link type="text/css" media="all" href="<?php echo base_url('assets/'); ?>css/style.css" rel="stylesheet" />
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/select2/select2.min.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/PNotifyBrightTheme.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/animate.css">
        <!-- DataTables -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/fixedHeader.dataTables.min.css">       
        <link href="<?php echo base_url(); ?>assets/plugins/datatables/responsive.bootstrap.min.css" media="all" type="text/css" rel="stylesheet"/>
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>dist/css/skins/_all-skins.min.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>dist/css/AdminLTE.min.css"><link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>documentation/style.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/plugins/autocomplite/') ?>jquery.auto-complete.css">
        <link href="<?php echo base_url('assets/'); ?>dist/css/pnotify.custom.min.css" media="all" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>plugins/tagsinput/bootstrap-tagsinput.css">
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/custom.css">       
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
            .box-primary .box-body,  .box-danger .box-body{    
                box-shadow: 1px 1px 0px #e7e7e7, 2px 2px 0px #e4e4e4, 3px 3px 0px #e4e4e4, 4px 4px 0px #e4e4e4, 5px 5px 0px #e4e4e4, 6px 6px 0px #e4e4e4;
                border-radius: 10px;
            }
            .box.box-primary, .box.box-danger{
                border-radius: 10px;
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
            .content{
                background: #ffffff;
            }
            .disable_menu{pointer-events: none;opacity: 0.7;}              
        </style>
        <!-- jQuery 3.1.1 -->
        <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-3.1.1.js"></script> 
        <script src="<?php echo base_url(); ?>assets/lib/jquery-ui/jquery-ui.min.js">
        </script>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <script type="text/javascript">var default_ledger = '<?= (@$default_ledger_title ? $default_ledger_title : ''); ?>';</script>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="loader">
        </div>
        <div class="wrapper">
            <header class="main-header">
                <a href="javascript:void(0);" class="logo">
                    <img style='width: 135px;float: left; padding: 4px;' src = "<?= base_url('assets/images/Aodry- white-09.svg') ?>" class="add_style_image remove_style_image">
                </a>
                <div class="cd-dropdown-wrapper">                   
                    <a class="cd-dropdown-trigger" href="javascript:void();">&nbsp;</a>
                    <nav class="cd-dropdown <?=($this->session->userdata('SESS_PACKAGE_STATUS') == '0' ? 'disable_menu' : '');?>">                       
                        <a href="javascript:void();" class="cd-close">Close</a>
                        <ul class="cd-dropdown-content">
                            <!--  <li>
                                 <form class="cd-search">
                                     <input type="search" placeholder="Search...">
                                 </form>
                             </li> -->
                            <?php if(in_array($this->config->item('quotation_module'), $active_modules) || in_array($this->config->item('sales_module'), $active_modules) || in_array($this->config->item('sales_credit_note_module'), $active_modules) || in_array($this->config->item('sales_debit_note_module'), $active_modules) || in_array($this->config->item('expense_module'), $active_modules) || in_array($this->config->item('expense_bill_module'), $active_modules) || in_array($this->config->item('purchase_order_module'), $active_modules) || in_array($this->config->item('purchase_module'), $active_modules) || in_array($this->config->item('purchase_credit_note_module'), $active_modules) || in_array($this->config->item('purchase_debit_note_module'), $active_modules) || in_array($this->config->item('purchase_return_module'), $active_modules) || in_array($this->config->item('delivery_challan_module'), $active_modules)){
                            ?>
                            <li class="has-children">
                                <a href="">Billing</a>
                                <ul class="cd-secondary-dropdown is-hidden">
                                    <!-- <li class="go-back"><a href="#0">Menu</a></li>
                                    <li class="see-all"><a href="">All Clothing</a></li> -->
                                    <?php if(in_array($this->config->item('quotation_module'), $active_modules) || in_array($this->config->item('sales_module'), $active_modules) || in_array($this->config->item('sales_credit_note_module'), $active_modules) || in_array($this->config->item('sales_debit_note_module'), $active_modules) || in_array($this->config->item('expense_module'), $active_modules) || in_array($this->config->item('expense_bill_module'), $active_modules)){
                                    ?>
                                    <li class="has-children">
                                        <?php if(in_array($this->config->item('quotation_module'), $active_modules) || in_array($this->config->item('sales_module'), $active_modules) || in_array($this->config->item('sales_credit_note_module'), $active_modules) || in_array($this->config->item('sales_debit_note_module'), $active_modules) || in_array($this->config->item('delivery_challan_module'), $active_modules)){
                                        ?>
                                        <a href="">Sales</a>
                                        <ul class="is-hidden" style="padding-bottom: 9px;">
                                            <?php
                                                if (in_array($this->config->item('quotation_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('quotation'); ?>">
                                                    <span>Quotation</span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('sales_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('sales'); ?>">
                                                    <span>Sales </span>
                                                </a>                                           
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('sales_credit_note_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>sales_credit_note">
                                                    Sales Credit Note
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('sales_debit_note_module'), $active_modules))
                                            {
                                            ?>                                        
                                            <li>
                                                <a href="<?php echo base_url(''); ?>sales_debit_note">
                                                    Sales Debit Note
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('delivery_challan_module'), $active_modules)){
                                                if($this->config->item('LeatherCraft') == $this->session->userdata("SESS_BRANCH_ID")){
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('delivery_challan'); ?>">
                                                    <span>Delivery Challan
                                                    </span>
                                                </a>
                                            </li>                                            
                                            <?php }}
                                            ?>
                                        </ul>
                                        <?php }
                                        if(in_array($this->config->item('expense_module'), $active_modules) || in_array($this->config->item('expense_bill_module'), $active_modules)){ ?>
                                        <a href="">Expense</a>
                                        <ul class="is-hidden" >
                                            <?php
                                                if (in_array($this->config->item('expense_module'), $active_modules))
                                                {
                                            ?>                               
                                            <li>
                                                <a href="<?= base_url('expense'); ?>">
                                                    Expense
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('expense_bill_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?= base_url('expense_bill'); ?>">
                                                    Expense Bill
                                                </a>
                                            </li>
                                            <?php }
                                            ?>   
                                        </ul>
                                        <?php }
                                        ?>
                                    </li>
                                    <?php }
                                    if(in_array($this->config->item('purchase_order_module'), $active_modules) || in_array($this->config->item('purchase_module'), $active_modules) || in_array($this->config->item('purchase_credit_note_module'), $active_modules) || in_array($this->config->item('purchase_debit_note_module'), $active_modules) || in_array($this->config->item('purchase_return_module'), $active_modules) || in_array($this->config->item('delivery_challan_module'), $active_modules))
                                    {?>
                                    <li class="has-children">
                                        <a href="">Purchase</a>
                                        <ul class="is-hidden">
                                            <?php
                                                if (in_array($this->config->item('purchase_order_module'), $active_modules))
                                                {
                                            ?>                               
                                            <li>
                                                <a href="<?php echo base_url('purchase_order'); ?>">
                                                    <span>Purchase Order
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('purchase_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('purchase'); ?>">
                                                    <span>Purchase
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('purchase_credit_note_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('purchase_credit_note'); ?>">
                                                    <span>Purchase Credit Note
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('purchase_debit_note_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('purchase_debit_note'); ?>">
                                                    <span>Purchase Debit Note
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('purchase_return_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('purchase_return'); ?>">
                                                    <span>Purchase Return
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('delivery_challan_module'), $active_modules)){
                                                    if($this->config->item('LeatherCraft') != $this->session->userdata("SESS_BRANCH_ID")){
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('delivery_challan'); ?>">
                                                    <span>Delivery Challan
                                                    </span>
                                                </a>
                                            </li>                                            
                                            <?php }}
                                                if (in_array($this->config->item('BOE_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?= base_url(); ?>boe">
                                                    <i class="">
                                                    </i>BOE (Bill of Entry)
                                                </a>
                                            </li> 
                                            <?php }
                                                ?>       

                                        </ul>
                                    </li>
                                    <?php }
                                    ?>                                    
                                </ul> 
                            </li> 
                            <?php } 
                            if(in_array($this->config->item('stock_module'), $active_modules) || in_array($this->config->item('missing_stock_module'), $active_modules) || in_array($this->config->item('damaged_stock_module'), $active_modules) || in_array($this->config->item('product_module'), $active_modules) || in_array($this->config->item('service_module'), $active_modules) || in_array($this->config->item('category_module'), $active_modules) || in_array($this->config->item('subcategory_module'), $active_modules)){ ?>
                            <li class="has-children">
                                <a href="">Inventory</a>
                                <ul class="cd-secondary-dropdown is-hidden">
                                    <?php 
                                    if(in_array($this->config->item('sales_stock_report'), $active_modules) ||in_array($this->config->item('purchase_stock_report'), $active_modules) || in_array($this->config->item('closing_stock_report'), $active_modules) || in_array($this->config->item('stock_module'), $active_modules) || in_array($this->config->item('damaged_stock_module'), $active_modules) || in_array($this->config->item('missing_stock_module'), $active_modules)){?>
                                    <li class="has-children">
                                        <a href="">Stock Management</a>
                                        <?php if ($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('LeatherCraft')) { 
                                            if(in_array($this->config->item('sales_stock_report'), $active_modules) || in_array($this->config->item('purchase_stock_report'), $active_modules) || in_array($this->config->item('closing_stock_report'), $active_modules)){ ?>
                                            <ul class="is-hidden">
                                                <?php 
                                                    if (in_array($this->config->item('sales_stock_report'), $active_modules))
                                                    {
                                                ?>                          
                                                <li>
                                                    <a href="<?= base_url('stock'); ?>">
                                                        <i class="">
                                                        </i>Sales Stock
                                                    </a>
                                                </li>
                                                <?php }
                                                    if (in_array($this->config->item('purchase_stock_report'), $active_modules))
                                                    {
                                                ?>
                                                <li>
                                                    <a href="<?= base_url('stock/purchase_stock'); ?>">
                                                        <i class="">
                                                        </i>Purchase Stock
                                                    </a>
                                                </li>
                                                <?php }
                                                    if (in_array($this->config->item('closing_stock_report'), $active_modules))
                                                    {
                                                ?>
                                                <li>
                                                    <a href="<?= base_url('stock/closing_stock'); ?>">
                                                        <i class="">
                                                        </i>Closing stock
                                                    </a>
                                                </li>
                                                <?php } ?>     
                                            </ul>
                                            <?php } } else { 
                                                if(in_array($this->config->item('stock_module'), $active_modules) || in_array($this->config->item('sales_stock_report'), $active_modules) || in_array($this->config->item('damaged_stock_module'), $active_modules) || in_array($this->config->item('missing_stock_module'), $active_modules)) {?>
                                            <ul class="is-hidden">
                                                <?php
                                                    if (in_array($this->config->item('stock_module'), $active_modules))
                                                    {
                                                ?>
                                                <li>
                                                    <a href="<?= base_url('product_stock'); ?>">
                                                        <i class="">
                                                        </i>Products Stock
                                                    </a>
                                                </li>
                                                <?php }
                                                    if (in_array($this->config->item('sales_stock_report'), $active_modules))
                                                    {
                                                ?>
                                                <li>
                                                    <a href="<?= base_url('product/sales_product'); ?>">
                                                        <i class="">
                                                        </i>Sales Stock
                                                    </a>
                                                </li>
                                                <?php }
                                                    if (in_array($this->config->item('damaged_stock_module'), $active_modules))
                                                    {
                                                ?>
                                                <li>
                                                    <a href="<?= base_url('damaged_stock'); ?>">
                                                        <i class="">
                                                        </i>Damaged stock
                                                    </a>
                                                </li>
                                                <?php }
                                                    if (in_array($this->config->item('missing_stock_module'), $active_modules))
                                                    {
                                                ?>
                                                <li>
                                                    <a href="<?= base_url('missing_stock'); ?>">
                                                        <i class="">
                                                        </i>Missing stock
                                                    </a>
                                                </li> 
                                                <?php } 
                                                if (in_array($this->config->item('product_module'), $active_modules)){?>  
                                                    <li>
                                                        <a href="<?= base_url('product/stock_movement'); ?>">
                                                            <i class="">
                                                            </i>Stock Movement
                                                        </a>
                                                    </li> 
                                                <?php } ?>
                                            </ul>
                                            <?php }} ?>
                                    </li> 
                                    <?php } 
                                    if(in_array($this->config->item('product_module'), $active_modules) || in_array($this->config->item('service_module'), $active_modules) || in_array($this->config->item('category_module'), $active_modules) || in_array($this->config->item('subcategory_module'), $active_modules)) {?> 
                                    <li class="has-children">
                                        <?php if(in_array($this->config->item('product_module'), $active_modules) || in_array($this->config->item('service_module'), $active_modules))
                                        { ?>
                                        <a href="">Items & Services</a>
                                        <ul class="is-hidden" style="padding-bottom: 9px;"> 
                                            <?php
                                            if (in_array($this->config->item('product_module'), $active_modules)){?>                              
                                                <li> 
                                                    <a href="<?= base_url(); ?>product">
                                                        Product 
                                                    </a>
                                                </li>
                                            <?php }
                                            if (in_array($this->config->item('service_module'), $active_modules)) { ?>
                                                <li> 
                                                    <a href="<?= base_url(); ?>service">
                                                        Services
                                                    </a>
                                                </li>
                                            <?php } ?>
                                            <?php if ($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('Sanath') || $this->session->userdata('SESS_BRANCH_ID') == $this->config->item('LeatherCraft')) { 
                                                if (in_array($this->config->item('brand_module'), $active_modules)){?>
                                                    <li>
                                                        <a href="<?= base_url(); ?>brand">
                                                            Brand 
                                                        </a>
                                                    </li>
                                                <?php }
                                                } ?>
                                            <?php if ($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('ILKKA')) { 
                                                if (in_array($this->config->item('brand_module'), $active_modules)){?>
                                                    <li>
                                                        <a href="<?= base_url(); ?>brand">
                                                            Manufacture 
                                                        </a>
                                                    </li>
                                                <?php }
                                            } ?>
                                        </ul>
                                        <?php }
                                        if(in_array($this->config->item('category_module'), $active_modules) || in_array($this->config->item('subcategory_module'), $active_modules))
                                            { ?>
                                        <a href="">Product & Service Category</a>
                                        <ul class="is-hidden"> 
                                            <?php 
                                                if (in_array($this->config->item('category_module'), $active_modules))
                                                {
                                            ?>                              
                                            <li>
                                                <a href="<?php echo base_url("category"); ?>">
                                                    <span>Category
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('subcategory_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url("subcategory"); ?>">
                                                    <span>Sub Category
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                            ?>
                                        </ul>
                                        <?php }
                                            ?>
                                    </li>
                                    <?php }
                                    ?>
                                </ul> 
                            </li>
                            <?php }
                            if(in_array($this->config->item('advance_voucher_module'), $active_modules) || in_array($this->config->item('receipt_voucher_module'), $active_modules) || in_array($this->config->item('payment_voucher_module'), $active_modules) || in_array($this->config->item('BOE_module'), $active_modules) || in_array($this->config->item('refund_voucher_module'), $active_modules) || in_array($this->config->item('expense_voucher_module'), $active_modules) || in_array($this->config->item('general_voucher_module'), $active_modules) || in_array($this->config->item('bank_voucher_module'), $active_modules) || in_array($this->config->item('cash_voucher_module'), $active_modules) || in_array($this->config->item('contra_voucher_module'), $active_modules) || in_array($this->config->item('journal_voucher_module'), $active_modules) ||in_array($this->config->item('sales_voucher_module'), $active_modules) || in_array($this->config->item('sales_credit_note_voucher'), $active_modules) || in_array($this->config->item('sales_debit_note_voucher'), $active_modules) || in_array($this->config->item('purchase_voucher_module'), $active_modules) || in_array($this->config->item('purchase_credit_note_voucher'), $active_modules) || in_array($this->config->item('purchase_debit_note_voucher'), $active_modules)){?>
                            <li class="has-children">
                                <a href="">Vouchers & Ledgers</a>                              
                                <ul class="cd-secondary-dropdown is-hidden spl-hgt">
                                    <?php if(in_array($this->config->item('advance_voucher_module'), $active_modules) || in_array($this->config->item('receipt_voucher_module'), $active_modules) || in_array($this->config->item('payment_voucher_module'), $active_modules) || in_array($this->config->item('BOE_module'), $active_modules) || in_array($this->config->item('refund_voucher_module'), $active_modules) || in_array($this->config->item('expense_voucher_module'), $active_modules) || in_array($this->config->item('general_voucher_module'), $active_modules) || in_array($this->config->item('bank_voucher_module'), $active_modules) || in_array($this->config->item('cash_voucher_module'), $active_modules) || in_array($this->config->item('contra_voucher_module'), $active_modules) || in_array($this->config->item('journal_voucher_module'), $active_modules))
                                    { ?>
                                    <li class="has-children">
                                        <a href="">Vouchers</a>
                                        <ul class="is-hidden">
                                            <?php
                                                if (in_array($this->config->item('advance_voucher_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('advance_voucher'); ?>">
                                                    <span>Advance Voucher
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('receipt_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('receipt_voucher'); ?>">
                                                    <span>Receipt Voucher
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('payment_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('payment_voucher'); ?>">
                                                    <span>Payment Voucher
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('BOE_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('boe_voucher'); ?>">
                                                    <span>BOE Voucher
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('refund_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('refund_voucher'); ?>">
                                                    <span>Refund Voucher
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('expense_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li> 
                                                <a href="<?php echo base_url(''); ?>expense_voucher">
                                                    Expense Voucher
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('general_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>general_voucher">
                                                    Journal Voucher
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('bank_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>bank_voucher">
                                                    Bank Voucher
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('cash_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>cash_voucher">
                                                    Cash Voucher
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('contra_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>contra_voucher">
                                                    Contra Voucher
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('journal_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>general_voucher/general_voucher_list">
                                                    General Voucher
                                                </a>
                                            </li>
                                            <?php }
                                            ?>
                                        </ul>
                                    </li>
                                    <?php }
                                    if(in_array($this->config->item('sales_voucher_module'), $active_modules) || in_array($this->config->item('sales_credit_note_voucher'), $active_modules) || in_array($this->config->item('sales_debit_note_voucher'), $active_modules) || in_array($this->config->item('purchase_voucher_module'), $active_modules) || in_array($this->config->item('purchase_credit_note_voucher'), $active_modules) || in_array($this->config->item('purchase_debit_note_voucher'), $active_modules) || in_array($this->config->item('advance_voucher_module'), $active_modules) || in_array($this->config->item('payment_voucher_module'), $active_modules) || in_array($this->config->item('general_voucher_module'), $active_modules) || in_array($this->config->item('bank_voucher_module'), $active_modules) || in_array($this->config->item('cash_voucher_module'), $active_modules) || in_array($this->config->item('contra_voucher_module'), $active_modules) || in_array($this->config->item('refund_voucher_module'), $active_modules) || in_array($this->config->item('expense_voucher_module'), $active_modules) || in_array($this->config->item('receipt_voucher_module'), $active_modules) || in_array($this->config->item('journal_voucher_module'), $active_modules)){ ?>
                                    <li class="has-children">
                                        <a href="">Ledgers</a>
                                        <ul class="is-hidden">
                                            <?php
                                                if (in_array($this->config->item('sales_voucher_module'), $active_modules))
                                                {
                                            ?>
                                            <li> 
                                                <a href="<?php echo base_url(''); ?>sales_ledger">
                                                    Sales Ledger
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('sales_credit_note_voucher'), $active_modules))
                                                {
                                            ?>
                                            <li> 
                                                <a href="<?php echo base_url(''); ?>sales_credit_ledgers">
                                                    Sales Credit Note Ledger
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('sales_debit_note_voucher'), $active_modules))
                                                {
                                            ?>
                                            <li> 
                                                <a href="<?php echo base_url(''); ?>sales_debit_ledgers">
                                                    Sales Debit Note Ledger 
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('purchase_voucher_module'), $active_modules))
                                                {
                                            ?>
                                            <li> 
                                                <a href="<?php echo base_url(''); ?>/purchase_ledger">
                                                    Purchase Ledger
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('purchase_credit_note_voucher'), $active_modules))
                                                {
                                            ?>
                                            <li> 
                                                <a href="<?php echo base_url(''); ?>purchase_credit_ledger">
                                                    Purchase Credit Note Ledger 
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('purchase_debit_note_voucher'), $active_modules))
                                                {
                                            ?>
                                            <li> 
                                                <a href="<?php echo base_url(''); ?>purchase_debit_ledger">
                                                    Purchase Debit Note Ledger
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('advance_voucher_module'), $active_modules))
                                            {
                                            ?>  
                                            <li>
                                                <a href="<?php echo base_url(''); ?>advance_ledger">
                                                    Advance Ledger
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('payment_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li> 
                                                <a href="<?php echo base_url(''); ?>payment_ledger">
                                                    Payment Ledger
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('general_voucher_module'), $active_modules))
                                            {
                                            ?> 
                                            <li>
                                                <a href="<?php echo base_url(''); ?>general_ledger">
                                                    Journal Ledger
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('bank_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>bank_ledger">
                                                    Bank Ledger
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('cash_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>cash_ledger">
                                                    Cash Ledger
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('contra_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>contra_ledger">
                                                    Contra Ledger
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('refund_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>refund_ledger">
                                                    Refund Ledger
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('expense_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>expense_ledgers">
                                                    Expense Ledger
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('receipt_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li> 
                                                <a href="<?php echo base_url(''); ?>receipt_ledgers">
                                                    Receipt Ledger
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('journal_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>journal_ledger">
                                                    General Ledger
                                                </a>
                                            </li>
                                            <?php }
                                            ?>
                                        </ul>
                                    </li>
                                    <?php }
                                    ?>
                                </ul>                            
                            </li>
                            <?php }
                            if(in_array($this->config->item('tds_sales_report_module'), $active_modules) || in_array($this->config->item('tcs_sales_report_module'), $active_modules) || in_array($this->config->item('tcs_purchase_report_module'), $active_modules) || in_array($this->config->item('tds_expense_report_module'), $active_modules) || in_array($this->config->item('gst_report'), $active_modules) || in_array($this->config->item('gst_sales_credit_note_report'), $active_modules) || in_array($this->config->item('gst_sales_debit_note_report'), $active_modules) || in_array($this->config->item('hsn_report_module'), $active_modules) || in_array($this->config->item('sales_report_module'), $active_modules) || in_array($this->config->item('sales_debit_note_report_module'), $active_modules) || in_array($this->config->item('sales_credit_note_report_module'), $active_modules) || in_array($this->config->item('purchase_report_module'), $active_modules) || in_array($this->config->item('purchase_debit_note_report_module'), $active_modules) || in_array($this->config->item('purchase_credit_note_report_module'), $active_modules) || in_array($this->config->item('expense_bill_report_module'), $active_modules) || in_array($this->config->item('advance_voucher_report_module'), $active_modules) || in_array($this->config->item('refund_voucher_report_module'), $active_modules) || in_array($this->config->item('receipt_voucher_report_module'), $active_modules) || in_array($this->config->item('payment_voucher_report_module'), $active_modules) || in_array($this->config->item('closing_balance_report_module'), $active_modules) || in_array($this->config->item('ledger_view_report_module'), $active_modules) || in_array($this->config->item('trial_balance_report_module'), $active_modules) || in_array($this->config->item('profit_loss_report_module'), $active_modules) || in_array($this->config->item('balance_sheet_report_module'), $active_modules) || in_array($this->config->item('gstr1_report_module'), $active_modules)) {?>
                            <li class="has-children">
                                <a href="">Reports</a>                              
                                <ul class="cd-secondary-dropdown is-hidden spl-rpt-hgt">
                                    <?php if(in_array($this->config->item('tds_sales_report_module'), $active_modules) || in_array($this->config->item('tcs_sales_report_module'), $active_modules) || in_array($this->config->item('tcs_purchase_report_module'), $active_modules) || in_array($this->config->item('tds_expense_report_module'), $active_modules) || in_array($this->config->item('gst_report'), $active_modules) || in_array($this->config->item('gst_sales_credit_note_report'), $active_modules) || in_array($this->config->item('gst_sales_debit_note_report'), $active_modules) || in_array($this->config->item('hsn_report_module'), $active_modules) || in_array($this->config->item('gstr1_report_module'), $active_modules)){ ?>
                                    <li class="has-children">
                                        <a href="">GST Register</a>
                                        <ul class="is-hidden">
                                            <?php
                                                if (in_array($this->config->item("gstr1_report_module"),$active_modules)) {
                                                    
                                                }
                                             ?>   
                                            <li>
                                                <a href="<?= base_url(); ?>report/all_reports"> 
                                                    <!-- <i class="fa fa-file-excel-o" aria-hidden="true"></i> -->
                                                        Compliance Register </a>
                                            </li>
                                            <?php
                                                if (in_array($this->config->item('gstr1_report_module'), $active_modules))
                                                {
                                            ?>                                            
                                            <li>
                                                <a href="<?php echo base_url('gst_report'); ?>">
                                <!--                             <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                                    GSTR1 Register
                                                </a>
                                            </li>
                                            <?php } 
                                                if (in_array($this->config->item('tds_sales_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('report/tds_report_sales'); ?>">
                <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                                    TDS Sales Register
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('tcs_sales_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('report/tcs_report_sales'); ?>">
                <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                                    TCS Sales Register
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('tcs_purchase_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('report/tcs_report_purchase'); ?>">
                <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                                    TCS Purchase Register
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('tds_expense_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('report/tds_report_expense'); ?>">
                <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                                    TDS Expense Register
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('gst_report'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('report/gst_report'); ?>">
                <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                                    GST Register
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('gst_sales_credit_note_report'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('report/gst_credit_note_report'); ?>">
                <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                                    GST Register (Sales CN)
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('gst_sales_debit_note_report'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('report/gst_debit_note_report'); ?>">
                <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                                    GST Register (Sales DN)
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('hsn_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('report/hsn_report'); ?>">
                <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                                    HSN Register
                                                </a>
                                            </li>
                                            <?php } ?> 
                                        </ul>
                                    </li>
                                    <?php } 
                                    if(in_array($this->config->item('sales_report_module'), $active_modules) || in_array($this->config->item('sales_debit_note_report_module'), $active_modules) || in_array($this->config->item('sales_credit_note_report_module'), $active_modules) || in_array($this->config->item('purchase_report_module'), $active_modules) || in_array($this->config->item('purchase_debit_note_report_module'), $active_modules) || in_array($this->config->item('purchase_credit_note_report_module'), $active_modules) || in_array($this->config->item('expense_bill_report_module'), $active_modules) || in_array($this->config->item('advance_voucher_report_module'), $active_modules) || in_array($this->config->item('refund_voucher_report_module'), $active_modules) || in_array($this->config->item('receipt_voucher_report_module'), $active_modules) || in_array($this->config->item('payment_voucher_report_module'), $active_modules)){?> 
                                    <li class="has-children">
                                        <a href="">Transaction Reports</a>
                                        <ul class="is-hidden">
                                            <?php
                                                if (in_array($this->config->item('sales_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?= base_url('report/sales_test') ?>">Sales Register</a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('sales_debit_note_report_module'), $active_modules))
                                                {
                                            ?>                                 
                                            <li>
                                                <a href="<?= base_url('report/debit_note_report') ?>">Sales Debit Note Register</a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('sales_credit_note_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?= base_url('report/credit_note_report') ?>">Sales Credit Note Register</a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('purchase_report_module'), $active_modules))
                                                {
                                            ?> 
                                            <li>
                                                <a href="<?= base_url('report/purchase_test') ?>">Purchase Register</a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('purchase_debit_note_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?= base_url('report/purchase_debit_note_report') ?>">Purchase Debit Note Register</a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('purchase_credit_note_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li><a href="<?= base_url('report/purchase_credit_note_report') ?>">Purchase Credit Note Register</a></li>
                                            <?php }
                                                if (in_array($this->config->item('expense_bill_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?= base_url('report/expense_bill_report') ?>">Expense Bill Register</a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('advance_voucher_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li><a href="<?= base_url('report/advance_voucher_report') ?>">Advance Voucher Register</a></li>
                                            <?php }
                                                if (in_array($this->config->item('refund_voucher_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li><a href="<?= base_url('report/refund_voucher_report') ?>">Refund Voucher Register</a></li>
                                            <?php }
                                                if (in_array($this->config->item('receipt_voucher_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li><a href="<?= base_url('report/receipt_voucher_report') ?>">Receipt Voucher Register</a></li>
                                            <?php }
                                                if (in_array($this->config->item('payment_voucher_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li><a href="<?= base_url('report/payment_voucher_report') ?>">Payment Voucher Register</a></li> 
                                            <?php }?>                                           
                                        </ul>                                       
                                    </li>
                                    <?php }
                                    if(in_array($this->config->item('closing_balance_module'), $active_modules) || in_array($this->config->item('ledger_view_report_module'), $active_modules) || in_array($this->config->item('trial_balance_report_module'), $active_modules) || in_array($this->config->item('profit_loss_report_module'), $active_modules) || in_array($this->config->item('balance_sheet_report_module'), $active_modules)){?>
                                    <li class="has-children" style="margin-top: 36px;">
                                        <a href="">Accounting Reports</a>
                                        <ul class="is-hidden">
                                            <?php
                                                if (in_array($this->config->item('closing_balance_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('closing_balance'); ?>">
                                                    <span>Closing Balance</span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('ledger_view_report_module'), $active_modules))
                                                {
                                            ?>                                 
                                            <li>
                                                <a href="<?php echo base_url('ledger_view'); ?>">
                                                    <span>Ledger View</span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('trial_balance_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('trial_balance'); ?>">
                                                    <span>Trial Balance</span>
                                                </a>
                                            </li> 
                                            <?php }
                                                if (in_array($this->config->item('profit_loss_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('profit_loss'); ?>">
                                                    <span>Profit & Loss</span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('balance_sheet_report_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('balance_sheet'); ?>">
                                                    <span>Balance Sheet</span>
                                                </a>
                                            </li>
                                            <?php }
                                            ?>
                                        </ul>                                       
                                    </li>
                                    <?php }
                                    ?>                                    
                                </ul>
                            </li>
                            <?php }
                            if(in_array($this->config->item('company_setting_module'), $active_modules) || in_array($this->config->item('privilege_module'), $active_modules) || in_array($this->config->item('notes_module'), $active_modules) || in_array($this->config->item('email_module'), $active_modules) || in_array($this->config->item('bank_account_module'), $active_modules) || in_array($this->config->item('financial_year_module'), $active_modules) || in_array($this->config->item('group_ledgers_module'), $active_modules) || in_array($this->config->item('file_manager_module'), $active_modules) || in_array($this->config->item('user_module'), $active_modules) || in_array($this->config->item('customer_module'), $active_modules) || in_array($this->config->item('supplier_module'), $active_modules) || in_array($this->config->item('tax_module'), $active_modules) || in_array($this->config->item('discount_module'), $active_modules) || in_array($this->config->item('uqc_module'), $active_modules) || in_array($this->config->item('varients_module'), $active_modules) || in_array($this->config->item('barcode_module'), $active_modules) || in_array($this->config->item('investments_module'), $active_modules) || in_array($this->config->item('shareholder_module'), $active_modules) || in_array($this->config->item('fixed_assets_module'), $active_modules) || in_array($this->config->item('loan_module'), $active_modules) || in_array($this->config->item('deposit_module'), $active_modules) || in_array($this->config->item('journal_voucher_module'), $active_modules) || in_array($this->config->item('department_module'), $active_modules) || in_array($this->config->item('sub_department_module'), $active_modules)){?>
                            <li class="has-children">
                                <a href="">Settings</a>                              
                                <ul class="cd-secondary-dropdown is-hidden spl-hgt">
                                    <?php if(in_array($this->config->item('company_setting_module'), $active_modules) || in_array($this->config->item('privilege_module'), $active_modules) || in_array($this->config->item('notes_module'), $active_modules) || in_array($this->config->item('email_module'), $active_modules) || in_array($this->config->item('bank_account_module'), $active_modules) || in_array($this->config->item('financial_year_module'), $active_modules) || in_array($this->config->item('group_ledgers_module'), $active_modules) || in_array($this->config->item('file_manager_module'), $active_modules) || in_array($this->config->item('user_module'), $active_modules) || in_array($this->config->item('customer_module'), $active_modules) || in_array($this->config->item('supplier_module'), $active_modules)){ ?>
                                    <li class="has-children">
                                        <a href="">Settings</a>
                                        <ul class="is-hidden">
                                            <?php 
                                            if (in_array($this->config->item('company_setting_module'), $active_modules))
                                            {
                                            ?>
                                            <li><a href="<?php echo base_url('company_setting') ?>">
                                                    <span>Company Settings</span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('privilege_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('module_settings'); ?>">
                                                    <span>Module Settings
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('notes_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('note_template'); ?>">
                                                    <span>Note Template
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('email_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url('email_template'); ?>">
                                                    <span>Email Template
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('bank_account_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url("bank_account"); ?>">
                                                    <span>Bank Account</span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('financial_year_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>financial_year">
                                                    Financial Year                          
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('group_ledgers_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>groupLedgers">
                                                    Group and Ledgers
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('file_manager_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url(''); ?>Filemanager">
                                                    File Manager
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('user_module'), $active_modules))
                                            {
                                            ?>
                                            <li> 
                                                <a href="<?= base_url(); ?>auth">Users</a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('customer_module'), $active_modules))
                                            {
                                            ?>
                                            <li> 
                                                <a href="<?= base_url(); ?>customer">
                                                    Customers
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('supplier_module'), $active_modules))
                                            {
                                            ?>
                                            <li> 
                                                <a href="<?= base_url(); ?>supplier">
                                                    Suppliers
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('customer_module'), $active_modules))
                                            {
                                            ?>
                                            <li> 
                                                <a href="<?= base_url(); ?>shipping_address">
                                                    Shipping Address
                                                </a>
                                            </li>
                                            <?php }
                                            ?>
                                        </ul>
                                    </li>
                                    <?php }
                                    ?>
                                    <li class="has-children">
                                        <?php if(in_array($this->config->item('tax_module'), $active_modules) || in_array($this->config->item('discount_module'), $active_modules) || in_array($this->config->item('uqc_module'), $active_modules) || in_array($this->config->item('varients_module'), $active_modules) || in_array($this->config->item('barcode_module'), $active_modules)){ ?>
                                        <a href="">Features</a>
                                        <ul class="is-hidden" style="padding-bottom: 7px;">
                                            <?php
                                            if (in_array($this->config->item('tax_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url("tax"); ?>">
                                                    <span>Tax
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('discount_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url("discount"); ?>">
                                                    <span>Discount
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('uqc_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url("uqc"); ?>">
                                                    <span>UOM
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('varients_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url("varients"); ?>">
                                                    <span>Variant
                                                    </span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('barcode_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url("barcode"); ?>">
                                                    <span>Barcode</span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('hsn_module'), $active_modules))
                                                {?>
                                                <li> 
                                                    <a href="<?= base_url(); ?>hsn">
                                                        HSN
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <?php } 
                                        if(in_array($this->config->item('investments_module'), $active_modules) || in_array($this->config->item('shareholder_module'), $active_modules) || in_array($this->config->item('fixed_assets_module'), $active_modules) || in_array($this->config->item('loan_module'), $active_modules) || in_array($this->config->item('deposit_module'), $active_modules) || in_array($this->config->item('journal_voucher_module'), $active_modules) || in_array($this->config->item('department_module'), $active_modules) || in_array($this->config->item('sub_department_module'), $active_modules)){?>
                                        <a href="">General Voucher</a>
                                        <ul class="is-hidden">
                                            <?php
                                                if (in_array($this->config->item('investments_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url("investments"); ?>">
                                                    <span>Investments</span>
                                                </a>
                                            </li> 
                                            <?php }
                                                if (in_array($this->config->item('shareholder_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url("share_holder"); ?>">
                                                    <span>People</span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('fixed_assets_module'), $active_modules))
                                                {
                                            ?> 
                                            <li>
                                                <a href="<?php echo base_url("fixed_assets"); ?>">
                                                    <span>Fixed Assets</span>
                                                </a>
                                            </li> 
                                            <?php }
                                                if (in_array($this->config->item('loan_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url("loan"); ?>">
                                                    <span>Loan</span>
                                                </a>
                                            </li>
                                            <?php }
                                                if (in_array($this->config->item('deposit_module'), $active_modules))
                                                {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url("deposit"); ?>">
                                                    <span>Deposit</span>
                                                </a>
                                            </li>
                                            <?php }
                                            if (in_array($this->config->item('journal_voucher_module'), $active_modules))
                                            {
                                            ?>
                                            <li>
                                                <a href="<?php echo base_url("general_voucher/transaction_purpose_list"); ?>">
                                                    <span>Transaction Purpose</span>
                                                </a>
                                            </li>
                                            <?php }
                                            ?>
                                            <?php if ($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('LeatherCraft')) { 
                                                if (in_array($this->config->item('department_module'), $active_modules))
                                                {?>
                                                <li>
                                                    <a href="<?php echo base_url("department"); ?>">
                                                        <span>Department</span>
                                                    </a>
                                                </li>
                                                <?php }
                                                    if (in_array($this->config->item('sub_department_module'), $active_modules))
                                                    {
                                                ?>
                                                <li>
                                                    <a href="<?php echo base_url("subdepartment"); ?>">
                                                        <span>Subdepartment</span>
                                                    </a>
                                                </li>
                                                <?php
                                            }}
                                            ?>
                                        </ul>
                                        <?php
                                        }?>
                                    </li>
                                </ul>
                            </li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div> 
                <nav class="navbar navbar-static-top">
                    <!--  <div class="col-sm-2"  id="financial_year_view" style="display: <?php
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
                        ?>>FY 
                        <?= $value->financial_year_title ?>
                                                                                                                                                                                                                                                                                                                                                                                                     </option>
                        <?php
                    }
                    ?>
                                 </select>
                             </div>
                         </div>
                     </div> -->

                    <div id="submit_f_y" style="display:<?php
                    if ($this->session->userdata("SESS_F_Y_PASSWORD")) {
                        echo "none";
                    }
                    ?>">                        
                        <button class="finiacial-year"> <!-- data-toggle="modal" data-target="#financial_year_model" -->
                            <?php echo 'FY ' . $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'); ?>
                        </button>
                        <div class="navbar-custom-menu">
                            <ul class="nav navbar-nav">
                                <?php
                                    if (in_array($this->config->item('purchase_module'), $active_modules)){
                                ?>
                                <li class="quick_sales" title="Quick Puchase">
                                    <a href="<?php echo base_url() ?>purchase/add" >
                                       <i class="fa fa-fw fa-shopping-cart"></i>
                                   </a>
                                </li>
                                <?php }
                                    if (in_array($this->config->item('sales_module'), $active_modules)){
                                ?>
                                <li class="quick_sales" title="Quick Sales">
                                    <a href="<?php echo base_url() ?>sales/add" >
                                       <i class="fa fa-fw fa-file-text-o"></i>
                                   </a>
                                </li>
                                <?php } ?>
                                <!-- <?php if ($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('LeatherCraft')) { ?>
                                    <li class="quick_sales">
                                        <a href="<?php echo base_url() ?>sales/add" >
                                            <i class="fa fa-fw fa-shopping-cart" >
                                            </i>Quick Sale</a>
                                    </li>
                                <?php } ?> -->
                                <li class="dropdown notifications-menu">
                                    <a href="javascript:void();" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-bell-o" aria-hidden="true"></i>
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
                                        <li class="divider"></li>
                                        <li class="footer">
                                            <a href="javascript:void();">View all</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown user user-menu">
                                    <a href="javascript:void();"  class="dropdown-toggle" data-toggle="dropdown">
                                        <img src="<?php echo base_url(); ?>assets/dist/img/person2.jpg" class="user-image" alt="User Image">
                                        <span class="hidden-xs">
                                            <?php echo $this->session->userdata('SESS_USERNAME'); ?>
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php
                                            if (in_array($this->config->item('company_setting_module'), $active_modules)){
                                        ?>
                                        <li> 
                                            <a href="<?php echo base_url() ?>company_setting">
                                                <i class="fa fa-fw fa-gear fa-spin" aria-hidden="true">
                                                </i>
                                                Company Settings                                            
                                            </a>
                                        </li>  
                                        <?php } 
                                        if (in_array($this->config->item('module_setting'), $active_modules)){?>                 
                                        <li> 
                                            <a href="<?php echo base_url() ?>module_settings">
                                                <i class="fa fa-fw fa-newspaper-o" aria-hidden="true">
                                                </i>Module Settings
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <li>                                       
                                            <a class="ft_sty" href="https://www.aodry.com/support/" target="_blank"><i class="fa fa-fw fa-support"></i>Support</a>
                                        </li>                                    
                                        <li>
                                            <a href="<?php echo base_url() ?>auth/logout" >
                                                <i class="fa fa-fw fa-sign-out" >
                                                </i>Logout</a>
                                        </li>
                                    </ul>
                                </li>                               
                            </ul>
                        </div>                    
                </nav>
            </header>
        </div>
        <div class="backdrop">
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
        <!-- <aside class="main-sidebar">
            <section class="sidebar <?=($this->session->userdata('SESS_PACKAGE_STATUS') == '0' ? 'disable_menu' : '');?>">
                <ul class="sidebar-menu tree" data-widget="tree">
                    <?php
                        if (in_array($this->config->item('layout_module'), $active_modules))
                        {
                    ?>
                    <li class="treeview">
                        <a href="<?php echo base_url() ?>auth/dashboard">
                            <i class="fa fa-fw fa-dashboard">
                            </i> 
                            <span>Dashboard
                            </span>
                        </a>
                    </li> 
                    <?php }
                    if(in_array($this->config->item('quotation_module'), $active_modules) ||in_array($this->config->item('sales_module'), $active_modules) || in_array($this->config->item('sales_credit_note_module'), $active_modules)||in_array($this->config->item('sales_debit_note_module'), $active_modules)){
                    ?>
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
                            <?php
                                if (in_array($this->config->item('quotation_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?= base_url(); ?>quotation">
                                    Quotation
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('sales_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?= base_url(); ?>sales">
                                    Sales
                                </a>
                            </li> 
                            <?php }
                                if (in_array($this->config->item('sales_credit_note_module'), $active_modules))
                                {
                            ?>                          
                            <li>
                                <a href="<?= base_url(); ?>sales_credit_note">
                                    Sales Credit Note/Sales Return
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('sales_debit_note_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?= base_url(); ?>sales_debit_note">
                                    Sales Debit Note
                                </a>
                            </li> 
                            <?php } ?>                           
                        </ul>
                    </li> 
                    <?php }
                    if(in_array($this->config->item('purchase_order_module'), $active_modules) ||in_array($this->config->item('purchase_module'), $active_modules) ||in_array($this->config->item('purchase_credit_note_module'), $active_modules) || in_array($this->config->item('purchase_debit_note_module'), $active_modules) || in_array($this->config->item('delivery_challan_module'), $active_modules) || in_array($this->config->item('purchase_return_module'), $active_modules) || in_array($this->config->item('BOE_module'), $active_modules)){ 
                        ?>                                                                        
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
                        <?php
                            if (in_array($this->config->item('purchase_order_module'), $active_modules))
                            {
                        ?> 
                            <li>
                                <a href="<?= base_url(); ?>purchase_order">
                                    Purchase Order
                                </a>
                            </li>
                        <?php }
                            if (in_array($this->config->item('purchase_module'), $active_modules))
                            {
                        ?>
                            <li>
                                <a href="<?= base_url(); ?>purchase">
                                    Purchase
                                </a>
                            </li>
                        <?php }
                            if (in_array($this->config->item('purchase_credit_note_module'), $active_modules))
                            {
                        ?>
                            <li>
                                <a href="<?= base_url(); ?>purchase_credit_note">
                                    Purchase Credit Note
                                </a>
                            </li>
                        <?php }
                            if (in_array($this->config->item('purchase_debit_note_module'), $active_modules))
                            {
                        ?>
                            <li>
                                <a href="<?= base_url(); ?>purchase_debit_note">
                                    Purchase Debit Note
                                </a>
                            </li>
                        <?php }
                            if (in_array($this->config->item('delivery_challan_module'), $active_modules))
                            {
                        ?>
                            <li>
                                <a href="<?= base_url(); ?>delivery_challan">
                                    Delivery Challan
                                </a>
                            </li> 
                        <?php }
                            if (in_array($this->config->item('purchase_return_module'), $active_modules))
                            {
                        ?>         
                            <li>
                                <a href="<?= base_url('purchase_return'); ?>">
                                    <i class="" aria-hidden="true">
                                    </i>Purchase Return
                                </a>
                            </li> 
                        <?php }
                            if (in_array($this->config->item('BOE_module'), $active_modules))
                            {
                        ?>
                            <li>
                                <a href="<?= base_url(); ?>boe">
                                    <i class="">
                                    </i>BOE (Bill of Entry)
                                </a>
                            </li> 
                        <?php }
                        ?>                                                      
                        </ul>
                    </li>
                    <?php }
                    if(in_array($this->config->item('expense_module'), $active_modules) || in_array($this->config->item('expense_bill_module'), $active_modules) ||in_array($this->config->item('expense_voucher_module'), $active_modules)){
                    ?>
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
                            <?php
                                if (in_array($this->config->item('expense_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?= base_url('expense'); ?>">
                                    Expense
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('expense_bill_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?= base_url('expense_bill'); ?>">
                                    Expense Bill
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('expense_voucher_module'), $active_modules))
                                {
                            ?>  
                            <li> 
                                <a href="<?php echo base_url('expense_voucher'); ?>">
                                    Expense Voucher 
                                </a>
                            </li>
                        <?php } ?>
                        </ul>
                    </li>
                    <?php }
                    if(in_array($this->config->item('payment_voucher_module'), $active_modules) ||in_array($this->config->item('advance_voucher_module'), $active_modules) || in_array($this->config->item('refund_voucher_module'), $active_modules) || in_array($this->config->item('receipt_voucher_module'), $active_modules) || in_array($this->config->item('general_voucher_module'), $active_modules) || in_array($this->config->item('bank_voucher_module'), $active_modules) || in_array($this->config->item('cash_voucher_module'), $active_modules) || in_array($this->config->item('contra_voucher_module'), $active_modules)){
                    ?>
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
                            <?php
                                if (in_array($this->config->item('payment_voucher_module'), $active_modules))
                                {
                            ?>
                            <li> <a href="<?php echo base_url('payment_voucher'); ?>">
                                    Payment Voucher </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('advance_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?= base_url('advance_voucher'); ?>">
                                 -->    <!-- <i class="fa fa-fw fa-group">
                                    </i>
                                    <span>Advance Voucher
                                    </span>
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('refund_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?= base_url('refund_voucher'); ?>"> -->
<!--                                    <i class="fa fa-fw fa-group">
                                    </i> -->
                                    <!-- <span>Refund Voucher
                                    </span>
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('receipt_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?= base_url('receipt_voucher'); ?>"> -->
<!--                                    <i class="fa fa-fw fa-group">
                                    </i> -->
                                   <!--  <span>Receipt voucher
                                    </span>                                                                                 
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('general_voucher_module'), $active_modules))
                                {
                            ?>                            
                            <li>
                                <a href="<?php echo base_url("general_voucher"); ?>"> -->
<!--                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i>-->
                                    <!-- Journal Voucher
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('bank_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?php echo base_url("bank_voucher"); ?>"> -->
<!--                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i> -->
                                   <!--  Bank Voucher
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('cash_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?php echo base_url("cash_voucher"); ?>"> -->
<!--                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i> -->
                                   <!--  Cash Voucher
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('contra_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?php echo base_url("contra_voucher"); ?>"> -->
<!--                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i>-->
                                   <!--  Contra Voucher
                                </a>
                            </li>
                            <?php }
                            ?>        
                        </ul>
                    </li> 
                    <?php } 
                    if(in_array($this->config->item('sales_voucher_module'), $active_modules) || in_array($this->config->item('sales_credit_note_voucher'), $active_modules) || in_array($this->config->item('sales_debit_note_voucher'), $active_modules) || in_array($this->config->item('purchase_voucher_module'), $active_modules) || in_array($this->config->item('purchase_credit_note_voucher'), $active_modules) || in_array($this->config->item('purchase_debit_note_voucher'), $active_modules)){?>
                    <li class="treeview">
                        <a href="javascript:void();">
                            <i class="fa fa-fw fa-money">
                            </i> 
                            <span>Sales/Purchase Ledger
                            </span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right">
                                </i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                        <?php
                            if (in_array($this->config->item('sales_voucher_module'), $active_modules))
                            {
                        ?>                            
                            <li> 
                                <a href="<?= base_url(); ?>sales_ledger"> -->
<!--                                    <i class="fa fa-fw fa-calendar-plus-o" aria-hidden="true">
                                    </i>-->                              
                                    <!-- Sales Ledger
                                </a>
                            </li>
                        <?php }
                            if (in_array($this->config->item('sales_credit_note_voucher'), $active_modules))
                            {
                        ?>
                            <li> 
                                <a href="<?= base_url(); ?>sales_credit_ledgers"> -->
<!--                                    <i class="fa fa-fw fa-calendar-plus-o" aria-hidden="true">
                                    </i>-->
                                    <!-- Sales Credit Note Ledger
                                </a>
                            </li>
                        <?php }
                            if (in_array($this->config->item('sales_debit_note_voucher'), $active_modules))
                            {
                        ?>
                            <li> 
                                <a href="<?= base_url(); ?>sales_debit_ledgers"> -->
<!--                                    <i class="fa fa-fw fa-calendar-plus-o" aria-hidden="true">
                                    </i>-->
                                   <!--  Sales Debit Note Ledger 
                                </a>
                            </li>
                        <?php }
                            if (in_array($this->config->item('purchase_voucher_module'), $active_modules))
                            {
                        ?>
                            <li> 
                                <a href="<?php echo base_url('purchase_ledger'); ?>"> -->
<!--                                    <i class="fa fa-fw fa-gg" aria-hidden="true">
                                    </i>-->
                                   <!--  Purchase Ledger
                                </a>
                            </li>
                        <?php }
                            if (in_array($this->config->item('purchase_credit_note_voucher'), $active_modules))
                            {
                        ?>
                            <li> 
                                <a href="<?php echo base_url('purchase_credit_ledger'); ?>"> -->
<!--                                    <i class="fa fa-fw fa-gg" aria-hidden="true">
                                    </i>-->
                                   <!--  Purchase Credit Note Ledger 
                                </a>
                            </li>
                        <?php }
                            if (in_array($this->config->item('purchase_debit_note_voucher'), $active_modules))
                            {
                        ?>
                            <li> 
                                <a href="<?php echo base_url('purchase_debit_ledger'); ?>"> -->
<!--                                    <i class="fa fa-fw fa-gg" aria-hidden="true">
                                    </i>-->
                                    <!-- Purchase Debit Note Ledger
                                </a>
                            </li>
                        <?php }
                        ?>
                        </ul>
                    </li>
                    <?php } 
                    if(in_array($this->config->item('advance_voucher_module'), $active_modules) || in_array($this->config->item('payment_voucher_module'), $active_modules) || in_array($this->config->item('general_voucher_module'), $active_modules) || in_array($this->config->item('bank_voucher_module'), $active_modules) || in_array($this->config->item('cash_voucher_module'), $active_modules) || in_array($this->config->item('contra_voucher_module'), $active_modules) || in_array($this->config->item('refund_voucher_module'), $active_modules) || in_array($this->config->item('expense_voucher_module'), $active_modules) || in_array($this->config->item('receipt_voucher_module'), $active_modules)) {?>
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
                            <?php
                                if (in_array($this->config->item('advance_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?php echo base_url("advance_ledger"); ?>"> -->
<!--                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true"></i>-->
                                    <!-- Advance Ledger
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('payment_voucher_module'), $active_modules))
                                {
                            ?>
                            <li> 
                                <a href="<?php echo base_url('payment_ledger'); ?>"> -->
<!--                                    <i class="fa fa-fw fa-gg" aria-hidden="true">
                                    </i>-->
                                    <!-- Payment Ledger
                                </a>
                            </li> 
                            <?php }
                                if (in_array($this->config->item('general_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?php echo base_url("general_ledger"); ?>"> -->
<!--                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i>-->
                                   <!--  Journal Ledger
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('bank_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?php echo base_url("bank_ledger"); ?>"> -->
<!--                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i>-->
                                   <!--  Bank Ledger
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('cash_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?php echo base_url("cash_ledger"); ?>"> -->
<!--                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i>-->
                                   <!--  Cash Ledger
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('contra_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?php echo base_url("contra_ledger"); ?>"> -->
<!--                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i>-->
                                   <!--  Contra Ledger
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('refund_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?php echo base_url("refund_ledger"); ?>"> -->
<!--                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i> -->
                                   <!--  Refund Ledger
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('expense_voucher_module'), $active_modules))
                                {
                            ?>
                            <li>
                                <a href="<?= base_url(); ?>expense_ledgers">                                    
                                    Expense Ledger
                                </a>
                            </li>
                            <?php }
                                if (in_array($this->config->item('receipt_voucher_module'), $active_modules))
                                {
                            ?>
                            <li> 
                                <a href="<?php echo base_url('receipt_ledgers'); ?>"> -->
<!--                                    <i class="fa fa-fw fa-file-text-o" aria-hidden="true">
                                    </i>-->
                                   <!--  Receipt Ledger
                                </a>
                            </li> 
                            <?php } ?>                          
                        </ul>
                    </li>
                    <?php } ?> 
                    </li>  -->

                    <!-- <li class="treeview">
                        <a href="javascript:void();">
                            <i class="fa fa-fw fa-files-o">
                            </i>
                            <span>Sales/Purchase Vouchers
                            </span>
                            <span class="pull-right-container">
                                <i class="fa fa-fw fa-angle-left pull-right">
                                </i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li> 
                                <a href="<?php echo base_url('sales_voucher'); ?>">
                                    Sales Voucher 
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('sales_credit_note_voucher'); ?>">
                                    Sales Credit Note Voucher 
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('sales_debit_note_voucher'); ?>">
                                    Sales Debit Note Voucher
                                </a>
                            </li>

                            <li> 
                                <a href="<?php echo base_url('purchase_voucher'); ?>">
                                    Purchase Voucher 
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('purchase_credit_note_voucher'); ?>">
                                    Purchase Credit Note Voucher 
                                </a>
                            </li>
                            <li> 
                                <a href="<?php echo base_url('purchase_debit_note_voucher'); ?>">
                                    Purchase Debit Note Voucher 
                                </a>
                            </li>                           
                            <li> <a href="<?php echo base_url('boe_voucher'); ?>">
                                    BOE Voucher 
                                </a>
                            </li>
                        </ul>
                    </li> -->
                    <!-- <?php 
                    if(in_array($this->config->item('sales_stock_report'), $active_modules) ||in_array($this->config->item('purchase_stock_report'), $active_modules) || in_array($this->config->item('closing_stock_report'), $active_modules) || in_array($this->config->item('product_stock_report'), $active_modules) || in_array($this->config->item('damaged_stock_module'), $active_modules) || in_array($this->config->item('missing_stock_module'), $active_modules) || in_array($this->config->item('product_module'), $active_modules)){?>
                        <li>
                            <a href="javascript:void();">
                                <i class="fa fa-fw fa-bar-chart">
                                </i> 
                                <span>Inventory
                                </span>
                                <span class="pull-right-container">
                                    <i class="fa fa-fw fa-angle-left pull-right">
                                    </i>
                                </span>
                            </a>
                            <?php if ($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('LeatherCraft')) { 
                                if(in_array($this->config->item('sales_stock_report'), $active_modules) || in_array($this->config->item('purchase_stock_report'), $active_modules) || in_array($this->config->item('closing_stock_report'), $active_modules)){ ?>
                                    <ul class="treeview-menu">
                                        <?php
                                            if (in_array($this->config->item('sales_stock_report'), $active_modules))
                                            {
                                        ?>
                                        <li>
                                            <a href="<?php echo base_url('stock'); ?>">
                                                <i class=""></i>Sales Stock
                                            </a>
                                        </li>
                                        <?php }
                                            if (in_array($this->config->item('purchase_stock_report'), $active_modules))
                                            {
                                        ?>
                                        <li>
                                            <a href="<?php echo base_url('stock/purchase_stock'); ?>">
                                                <i class="">
                                                </i>Purchase Stock
                                            </a>
                                        </li>
                                        <?php }
                                            if (in_array($this->config->item('closing_stock_report'), $active_modules))
                                            {
                                        ?>
                                        <li>
                                            <a href="<?= base_url('stock/closing_stock'); ?>">
                                                <i class="">
                                                </i>Closing stock
                                            </a>
                                        </li> 
                                        <?php } ?>   
                                    </ul>
                                <?php } ?> 
                            <?php } elseif($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('Sanath')){ 
                                if(in_array($this->config->item('sales_stock_report'), $active_modules) || in_array($this->config->item('purchase_stock_report'), $active_modules) || in_array($this->config->item('closing_stock_report'), $active_modules)) { ?>
                                    <ul class="treeview-menu">
                                        <?php if(in_array($this->config->item('sales_stock_report'), $active_modules)){?>
                                            <li>
                                                <a href="<?php echo base_url('stock/brand_sales_stock'); ?>">
                                                    <i class=""></i>Brandwise Sales Stock
                                                </a>
                                            </li>
                                        <?php }
                                        if(in_array($this->config->item('purchase_stock_report'), $active_modules)){?>
                                            <li>
                                                <a href="<?php echo base_url('stock/brand_purchase_stock'); ?>">
                                                    <i class="">
                                                    </i>Brandwise Purchase Stock
                                                </a>
                                            </li>
                                        <?php }
                                        if(in_array($this->config->item('closing_stock_report'), $active_modules)){?>
                                            <li>
                                                <a href="<?= base_url('stock/brand_closing_stock'); ?>">
                                                    <i class="">
                                                    </i>Brandwise Closing stock
                                                </a>
                                            </li>
                                        <?php } ?>    
                                    </ul>
                                <?php }
                            }else { 
                                if(in_array($this->config->item('product_stock_report'), $active_modules) || in_array($this->config->item('sales_stock_report'), $active_modules) || in_array($this->config->item('damaged_stock_module'), $active_modules) || in_array($this->config->item('missing_stock_module'), $active_modules) || in_array($this->config->item('product_module'), $active_modules)) {?>
                                    <ul class="treeview-menu">
                                        <?php
                                        if (in_array($this->config->item('product_stock_report'), $active_modules)) { ?>
                                            <li>
                                                <a href="<?= base_url('product_stock'); ?>">
                                                    <i class="">
                                                    </i>Products Stock
                                                </a>
                                            </li>
                                        <?php }
                                        if (in_array($this->config->item('sales_stock_report'), $active_modules)) {?>
                                            <li>
                                                <a href="<?= base_url('product/sales_product'); ?>">
                                                    <i class="">
                                                    </i>Sales Stock
                                                </a>
                                            </li>
                                        <?php }
                                        if (in_array($this->config->item('damaged_stock_module'), $active_modules)){ ?>
                                            <li>
                                                <a href="<?= base_url('damaged_stock'); ?>">
                                                    <i class="">
                                                    </i>Damaged stock
                                                </a>
                                            </li>
                                        <?php }
                                        if (in_array($this->config->item('missing_stock_module'), $active_modules)){ ?>
                                            <li>
                                                <a href="<?= base_url('missing_stock'); ?>">
                                                    <i class="">
                                                    </i>Missing stock
                                                </a>
                                            </li> 
                                        <?php } 
                                        if (in_array($this->config->item('product_module'), $active_modules)){?>  
                                            <li>
                                                <a href="<?= base_url('product/stock_movement'); ?>">
                                                    <i class="">
                                                    </i>Stock Movement
                                                </a>
                                            </li> 
                                        <?php } ?>        
                                    </ul>
                                <?php } 
                            }?>
                        </li> 
                    <?php } 
                    if(in_array($this->config->item('sales_report_module'), $active_modules) || in_array($this->config->item('sales_credit_note_report_module'), $active_modules) || in_array($this->config->item('sales_debit_note_report_module'), $active_modules) || in_array($this->config->item('purchase_report_module'), $active_modules) || in_array($this->config->item('purchase_credit_note_report_module'), $active_modules) || in_array($this->config->item('purchase_debit_note_report_module'), $active_modules) || in_array($this->config->item('expense_bill_report_module'), $active_modules) || in_array($this->config->item('advance_voucher_report_module'), $active_modules) || in_array($this->config->item('refund_voucher_report_module'), $active_modules) || in_array($this->config->item('receipt_voucher_report_module'), $active_modules) || in_array($this->config->item('payment_voucher_report_module'), $active_modules) || in_array($this->config->item('tds_sales_report_module'), $active_modules) || in_array($this->config->item('tcs_sales_report_module'), $active_modules) || in_array($this->config->item('tcs_purchase_report_module'), $active_modules) || in_array($this->config->item('tds_expense_report_module'), $active_modules) || in_array($this->config->item('gst_report'), $active_modules) || in_array($this->config->item('gst_sales_credit_note_report'), $active_modules) || in_array($this->config->item('gst_sales_debit_note_report'), $active_modules) || in_array($this->config->item('hsn_report_module'), $active_modules) || in_array($this->config->item('gstr1_report_module'), $active_modules)){ ?>
                        <li class="treeview" id="report-menu">
                            <a href="javascript:void();">
                                <i class="fa fa-fw fa-file-text-o"></i> <span>Compliance Register</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-fw fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu" id="report-menu-items">
                                <?php if (in_array($this->config->item('sales_report_module'), $active_modules) || in_array($this->config->item('sales_credit_note_report_module'), $active_modules) || in_array($this->config->item('sales_debit_note_report_module'), $active_modules) || in_array($this->config->item('purchase_report_module'), $active_modules) || in_array($this->config->item('purchase_credit_note_report_module'), $active_modules) || in_array($this->config->item('purchase_debit_note_report_module'), $active_modules) || in_array($this->config->item('expense_bill_report_module'), $active_modules) || in_array($this->config->item('advance_voucher_report_module'), $active_modules) || in_array($this->config->item('refund_voucher_report_module'), $active_modules) || in_array($this->config->item('receipt_voucher_report_module'), $active_modules) || in_array($this->config->item('payment_voucher_report_module'), $active_modules)) { ?>
                                    <li>
                                        <a href="<?= base_url(); ?>report/all_reports"> -->
                                            <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                           <!--  Compliance Register </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('gstr1_report_module'), $active_modules)){ ?>
                                    <li>
                                        <a href="<?php echo base_url('gst_report'); ?>"> -->
                                            <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                           <!--  GSTR1 Register
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('tds_sales_report_module'), $active_modules)){ ?>
                                    <li>
                                        <a href="<?php echo base_url('report/tds_report_sales'); ?>"> -->
                                            <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                           <!--  TDS Sales Register
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('tcs_sales_report_module'), $active_modules)){?>
                                    <li>
                                        <a href="<?php echo base_url('report/tcs_report_sales'); ?>"> -->
                                            <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                            <!-- TCS Sales Register
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('tcs_purchase_report_module'), $active_modules)){?>
                                    <li>
                                        <a href="<?php echo base_url('report/tcs_report_purchase'); ?>"> -->
                                            <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                           <!--  TCS Purchase Register
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('tds_expense_report_module'), $active_modules)){?>
                                    <li>
                                        <a href="<?php echo base_url('report/tds_report_expense'); ?>"> -->
                                            <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                           <!--  TDS Expense Register
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('gst_report'), $active_modules)){?>
                                    <li>
                                        <a href="<?php echo base_url('report/gst_report'); ?>"> -->
                                            <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                           <!--  GST Register
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('gst_sales_credit_note_report'), $active_modules)){?>
                                    <li>
                                        <a href="<?php echo base_url('report/gst_credit_note_report'); ?>"> -->
                                            <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                            <!-- GST Register (Sales CN)
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('gst_sales_debit_note_report'), $active_modules)){?>
                                    <li>
                                        <a href="<?php echo base_url('report/gst_debit_note_report'); ?>"> -->
                                            <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                           <!--  GST Register (Sales DN)
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('hsn_report_module'), $active_modules)){?>
                                    <li>
                                        <a href="<?php echo base_url('report/hsn_report'); ?>"> -->
                                            <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                            <!-- HSN Register
                                        </a>
                                    </li>
                                <?php } ?> 
                            </ul>
                        </li>
                    <?php }
                    if(in_array($this->config->item('closing_balance_module'), $active_modules) || in_array($this->config->item('ledger_view_report_module'), $active_modules) || in_array($this->config->item('trial_balance_report_module'), $active_modules) || in_array($this->config->item('profit_loss_report_module'), $active_modules) || in_array($this->config->item('balance_sheet_report_module'), $active_modules)){ ?>
                        <li class="treeview" id="report-menu">
                            <a href="javascript:void();">
                                <i class="fa fa-fw fa-file-text-o"></i> <span>Financial reports</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-fw fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu" id="report-menu-items">
                                <?php
                                if (in_array($this->config->item('closing_balance_module'), $active_modules)) {?>
                                    <li>
                                        <a href="<?php echo base_url('closing_balance'); ?>"> -->
                                        <!--<i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                           <!--  Closing Balance
                                        </a>
                                    </li> 
                                <?php }
                                if (in_array($this->config->item('ledger_view_report_module'), $active_modules)) {?>                         
                                    <li>
                                        <a href="<?php echo base_url('ledger_view'); ?>"> -->
                                            <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                           <!--  Ledger View
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('trial_balance_report_module'), $active_modules)){?>
                                    <li>
                                        <a href="<?php echo base_url('trial_balance'); ?>"> -->
                                        <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                           <!--  Trial Balance
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('profit_loss_report_module'), $active_modules)) {?>
                                    <li>
                                        <a href="<?php echo base_url('profit_loss'); ?>"> -->
                                        <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                            <!-- Profit & Loss
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('balance_sheet_report_module'), $active_modules)){?>   
                                    <li>
                                        <a href="<?php echo base_url('balance_sheet'); ?>"> -->
                                        <!--                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
                                           <!--  Balance Sheet
                                        </a>
                                    </li>
                                <?php }
                                ?>
                            </ul>
                        </li> 
                    <?php }
                    if (in_array($this->config->item('group_ledgers_module'), $active_modules)){?>
                        <li>
                            <a href="<?= base_url(); ?>groupLedgers">
                                <i class="fa fa-fw fa-group">
                                </i> 
                                <span>Group and Ledgers
                                </span>  
                            </a>
                        </li>
                    <?php } 
                    if(in_array($this->config->item('email_module'), $active_modules) || in_array($this->config->item('notes_module'), $active_modules) || in_array($this->config->item('category_module'), $active_modules) || in_array($this->config->item('subcategory_module'), $active_modules) || in_array($this->config->item('user_module'), $active_modules) || in_array($this->config->item('customer_module'), $active_modules) || in_array($this->config->item('supplier_module'), $active_modules) || in_array($this->config->item('product_module'), $active_modules) || in_array($this->config->item('service_module'), $active_modules) || in_array($this->config->item('financial_year_module'), $active_modules) ||in_array($this->config->item('file_manager_module'), $active_modules) || in_array($this->config->item('groups_module'), $active_modules)){?>
                        <li class="treeview">
                            <a href="javascript:void();">
                                <i class="fa fa-fw fa-cogs">
                                </i>
                                <span>Settings</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-fw fa-angle-left pull-right">
                                    </i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if(in_array($this->config->item('email_module'), $active_modules) || in_array($this->config->item('notes_module'), $active_modules)){ ?>                           
                                    <li class="list_item2">
                                        <a href="javascript:void(0);">                                    
                                            </i>E-mail Setting 
                                            <span class="fa fa-fw fa-arrow-right">
                                            </span>
                                        </a>
                                        <ul class="treeview-menu treeview-sub-menu">    
                                            <?php 
                                            if (in_array($this->config->item('email_module'), $active_modules)){?>                              
                                            <li><a href="<?= base_url(); ?>email_template">E-mail Template<span class="fa fa-fw fa-arrow-left"></span></a></li>
                                            <?php }
                                            if (in_array($this->config->item('notes_module'), $active_modules)){?>
                                            <li><a href="<?= base_url(); ?>note_template">Note Template</a></li>
                                            <?php }
                                            ?>
                                        </ul>
                                    </li>
                                <?php } 
                                if(in_array($this->config->item('category_module'), $active_modules) || in_array($this->config->item('subcategory_module'), $active_modules)) { ?>
                                    <li class="list_item3">
                                        <a href="javascript:void(0);">                                    
                                            </i>Category 
                                            <span class="fa fa-fw fa-arrow-right">
                                            </span>
                                        </a>
                                        <ul class="treeview-menu treeview-sub-menu">
                                            <?php 
                                            if (in_array($this->config->item('category_module'), $active_modules)) {?>
                                                <li> 
                                                    <a href="<?= base_url(); ?>category">Category<span class="fa fa-fw fa-arrow-left"></span>
                                                    </a>
                                                </li>
                                            <?php }
                                            if (in_array($this->config->item('subcategory_module'), $active_modules)){ ?>
                                                <li> 
                                                    <a href="<?= base_url(); ?>subcategory">
                                                        Sub Category
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                <?php } 
                                if(in_array($this->config->item('user_module'), $active_modules) || in_array($this->config->item('customer_module'), $active_modules) || in_array($this->config->item('supplier_module'), $active_modules) || in_array($this->config->item('groups_module'), $active_modules)){ ?>
                                    <li class="list_item4">
                                        <a href="javascript:void(0);">People<span class="fa fa-fw fa-arrow-right"></span>
                                        </a>
                                        <ul class="treeview-menu treeview-sub-menu">
                                            <?php
                                            if (in_array($this->config->item('user_module'), $active_modules)) {?>
                                                <li> 
                                                    <a href="<?= base_url(); ?>auth">Users<span class="fa fa-fw fa-arrow-left"></span>
                                                    </a>
                                                </li>
                                            <?php }
                                            if (in_array($this->config->item('customer_module'), $active_modules))
                                                {
                                            ?>
                                                <li> 
                                                    <a href="<?= base_url(); ?>customer">
                                                        Customers
                                                    </a>
                                                </li>
                                            <?php }
                                            if (in_array($this->config->item('supplier_module'), $active_modules))
                                                {
                                            ?>
                                                <li> 
                                                    <a href="<?= base_url(); ?>supplier">
                                                        Suppliers
                                                    </a>
                                                </li>
                                            <?php }
                                            if (in_array($this->config->item('customer_module'), $active_modules) || in_array($this->config->item('supplier_module'), $active_modules)){
                                            ?>
                                                <li> 
                                                    <a href="<?= base_url(); ?>shipping_address">
                                                        Shipping Address
                                                    </a>
                                                </li>
                                            <?php }
                                            if (in_array($this->config->item('groups_module'), $active_modules))
                                                {
                                            ?>
                                                <li> 
                                                    <a href="<?= base_url(); ?>groups">
                                                        Groups
                                                    </a>
                                                </li>
                                                <li> 
                                                    <a href="<?= base_url(); ?>Group_assign">
                                                        Group Assign
                                                    </a>
                                                </li>
                                            <?php }
                                            ?>
                                        </ul>
                                    </li>
                                <?php } 
                                if(in_array($this->config->item('product_module'), $active_modules) ||in_array($this->config->item('service_module'), $active_modules)){?>
                                    <li class="list_item5">
                                        <a href="javascript:void(0);">
                                            Items & Services
                                            <span class="fa fa-fw fa-arrow-right">
                                            </span>
                                        </a>
                                        <ul class="treeview-menu treeview-sub-menu">
                                            <?php
                                            if (in_array($this->config->item('product_module'), $active_modules)) {?>
                                                <li> 
                                                    <a href="<?= base_url(); ?>product">
                                                        Product <span class="fa fa-fw fa-arrow-left">
                                                        </span>
                                                    </a>
                                                </li>
                                            <?php }
                                            if (in_array($this->config->item('service_module'), $active_modules)){?>
                                                <li> 
                                                    <a href="<?= base_url(); ?>service">
                                                        Services
                                                    </a>
                                                </li>
                                            <?php } ?>
                                            <?php if ($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('Sanath') || $this->session->userdata('SESS_BRANCH_ID') == $this->config->item('LeatherCraft')) { if (in_array($this->config->item('brand_module'), $active_modules)){?>
                                                    <li>
                                                        <a href="<?= base_url(); ?>brand">
                                                            Brand 
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('ILKKA')) { 
                                                if (in_array($this->config->item('brand_module'), $active_modules)){?>
                                                    <li>
                                                        <a href="<?= base_url(); ?>brand">
                                                            Manufacture
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('financial_year_module'), $active_modules)) {?> 
                                    <li class="list_item6">
                                        <a href="<?= base_url(); ?>financial_year">
                                            <span>Financial Year
                                            </span>       
                                        </a>
                                    </li>
                                <?php } 
                                if (in_array($this->config->item('file_manager_module'), $active_modules)) {?> 
                                     <li class="list_item6">
                                        <a href="<?php echo base_url(''); ?>Filemanager">
                                            <span>File Manager</span>
                                        </a>
                                    </li>
                                <?php }?>
                            </ul>
                        </li>
                    <?php } 
                    if(in_array($this->config->item('tax_module'), $active_modules) || in_array($this->config->item('discount_module'), $active_modules)    || in_array($this->config->item('uqc_module'), $active_modules)    || in_array($this->config->item('hsn_module'), $active_modules)    || in_array($this->config->item('varients_module'), $active_modules)   || in_array($this->config->item('barcode_module'), $active_modules)   || in_array($this->config->item('bank_account_module'), $active_modules)) {?>
                        <li class="treeview">
                            <a href="javascript:void();">
                                <i class="fa fa-fw fa-cubes">
                                </i> 
                                <span>Features</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-fw fa-angle-left pull-right">
                                    </i>
                                </span>
                            </a>
                            <ul class="treeview-menu">    
                                <?php
                                if (in_array($this->config->item('tax_module'), $active_modules))
                                { ?>                                                    
                                    <li> 
                                        <a href="<?= base_url(); ?>tax">
                                            Tax
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('discount_module'), $active_modules))
                                    {?>
                                    <li> 
                                        <a href="<?= base_url(); ?>discount">
                                            Discount
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('uqc_module'), $active_modules))
                                { ?>
                                    <li> 
                                        <a href="<?= base_url(); ?>uqc">
                                            UOM
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('hsn_module'), $active_modules))
                                {?>
                                    <li> 
                                        <a href="<?= base_url(); ?>hsn">
                                            HSN
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('varients_module'), $active_modules))
                                { ?>
                                    <li> 
                                        <a href="<?= base_url(); ?>varients">
                                            Variant
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('barcode_module'), $active_modules))
                                {?>
                                    <li>
                                        <a href="<?php echo base_url("barcode"); ?>">
                                            Barcode
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('bank_account_module'), $active_modules)) {?>
                                    <li>
                                        <a href="<?= base_url(); ?>bank_account">Bank Account</a>
                                    </li>
                                <?php } ?>                                                      
                            </ul>
                        </li> 
                    <?php }
                    if(in_array($this->config->item('investments_module'), $active_modules) || in_array($this->config->item('shareholder_module'), $active_modules) || in_array($this->config->item('fixed_assets_module'), $active_modules) || in_array($this->config->item('loan_module'), $active_modules) || in_array($this->config->item('deposit_module'), $active_modules) || in_array($this->config->item('journal_voucher_module'), $active_modules) || in_array($this->config->item('department_module'), $active_modules) || in_array($this->config->item('sub_department_module'), $active_modules))
                        { ?>                     
                        <li class="treeview">
                            <a href="javascript:void();">
                                <i class="fa fa-fw fa-money"></i> <span>General Voucher</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-fw fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php
                                if (in_array($this->config->item('investments_module'), $active_modules))
                                {?>
                                    <li>
                                        <a href="<?php echo base_url("investments"); ?>">
                                            <span>Investments</span>
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('shareholder_module'), $active_modules)) {?> 
                                    <li>
                                        <a href="<?php echo base_url("share_holder"); ?>">
                                            <span>People</span>
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('fixed_assets_module'), $active_modules)) {?> 
                                    <li>
                                        <a href="<?php echo base_url("fixed_assets"); ?>">
                                            <span>Fixed Assets</span>
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('loan_module'), $active_modules))
                                    { ?> 
                                    <li>
                                        <a href="<?php echo base_url("loan"); ?>">
                                            <span>Loan</span>
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('deposit_module'), $active_modules)) {?>
                                    <li>
                                        <a href="<?php echo base_url("deposit"); ?>">
                                            <span>Deposit</span>
                                        </a>
                                    </li>
                                <?php }
                                if (in_array($this->config->item('journal_voucher_module'), $active_modules)) {?>
                                    <li>
                                        <a href="<?php echo base_url("general_voucher/transaction_purpose_list"); ?>">
                                            <span>Transaction Purpose</span>
                                        </a>
                                    </li> 
                                <?php } if ($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('LeatherCraft')) { ?>
                                    <?php 
                                    if (in_array($this->config->item('department_module'), $active_modules)) {?>
                                        <li>
                                            <a href="<?php echo base_url("department"); ?>">
                                                <span>Department</span>
                                            </a>
                                        </li>
                                    <?php }
                                    if (in_array($this->config->item('sub_department_module'), $active_modules)) {?>
                                        <li>
                                            <a href="<?php echo base_url("subdepartment"); ?>">
                                                <span>Subdepartment</span>
                                            </a>
                                        </li>
                                    <?php
                                    }
                                }
                                ?> 
                            </ul>
                        </li>
                    <?php } ?>                                                                          
                </ul>
            </section>
        </aside> -->