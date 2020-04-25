<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">General Voucher</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">      
         <div class="row">          
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <h3 class="box-title">General Voucher</h3>
                            <?php
                            if (in_array($module_id, $active_add)) {
                            ?>
                                <a class="btn btn-sm btn-default pull-right" href="<?php echo base_url('general_voucher/add'); ?>">Add General Voucher </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body">
                        </div>
                    </div>                   
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >
                            <thead>
                                 <tr>
                                    <th width="9px">#</th>
                                    <th>Voucher Date</th>
                                    <th>Voucher Number</th>
                                    <th>Transaction</th>
                                    <th>Amount</th>
                                    <th>Others Charges / Interest </th>
                                   <!-- <th>From Account</th>
                                    <th>To Account</th>-->
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php
$this->load->view('layout/footer');
$this->load->view('general/delete_modal');
?>
<script>
    var getVoucher;
    $(document).ready(function(){
        getVoucher = GetAllVoucher();
    });
    $(document).ready(function(){
        <?php 
        $general_voucher_success = $this->session->flashdata('partner_success');
        $general_voucher_error = $this->session->flashdata('partner_error');
        ?>
        var alert_success = '<?= $general_voucher_success; ?>';
        var alert_failure = '<?= $general_voucher_error; ?>';
        if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });
      

    function GetAllVoucher(){
        var tbl = $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "general_voucher/general_voucher_list",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "voucher_date"},
                {"data": "voucher_number"},
                {"data": "option"},
                {"data": "amount"},
                {"data": "expense_amount"},
               /* {"data": "from_account"},
                {"data": "to_account"},*/                
            ],
            "order": [[ 0, "desc" ]],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });
             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
       // $('.edit_voucher').hide();
        //$('.delete_voucher').hide();
        return tbl;
    }

    $('body').on('change', 'input[type="checkbox"][name="check_item"]', function () {
        var i = 0;
        $.each($("input[name='check_item']:checked"), function () {
            i++;
        });
        if (i == 1)
        {
            var row = $("input[name='check_item']:checked").closest("tr");
            var action_button = row.find('.action_button').html();

            $('#plus_btn').hide();
            $('.filter_body').html(action_button);
            $('#filter').show();
        } else
        {
            $('#plus_btn').show();
            $('#filter').hide();
            $('.filter_body').html('');
        }
    });
</script>
