<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                <div class="box-header with-border">
                    <div id="plus_btn">                       
                            <h3 class="box-title">inlet</h3>
                            <?php
                            if (in_array($inlet_module_id, $active_add)) {
                            ?>
                            <a class="btn btn-sm btn-default pull-right" href="<?php echo base_url('inlet/add'); ?>">Add inlet </a>
                            <?php } ?>
                        </div>
                        <div id="filter">
                            <div class="box-header box-body filter_body"></div>
                        </div>
                    </div>                    
                    <div class="box-body">
                        <table id="list_datatable" class="table custom_datatable table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th width="1%"> # </th>
                                    <th width="9%">Invoice Date</th>
                                    <th>Invoice Number</th>
                                    <th>From Branch</th>
                                    <th>Total Items</th>
                                    <th>Transfer Status</th>
                                </tr>
                            </thead>                           
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('general/delete_modal');
?>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script>
    $(document).ready(function () {
        var inletTable = GetAllinlet();

        function GetAllinlet() {
            var inlet_table = $('#list_datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": false,
                "ajax": {
                    "url": base_url + "inlet",
                    "dataType": "json",
                    "type": "POST",
                    "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
                },
                "columns": [
                    {"data": "action"},
                    {"data": "date"},
                    {"data": "invoice" },
                    {"data": "branch_name"},
                    {"data": "total_items"},
                    {"data": "transfer_status"},
                ],  
                "order": [[ 0, "desc" ]],              
                "columnDefs": [ { 'orderable': false, 'targets': [4,5] }],
                 'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                    },
                });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
            $(document).find('[name=check_item]').prop('checked', false);
            return inlet_table;
        }
    });

    $('body').on('change', 'input[type="checkbox"][name="check_item"]', function () {
        var i = 0;
        $.each($("input[name='check_item']:checked"), function () {
            i++;
        });
        if (i == 1) {
            var row = $("input[name='check_item']:checked").closest("tr");
            var action_button = row.find('.action_button').html();

            $('#plus_btn').hide();
            $('.filter_body').html(action_button);
            $('#filter').show();
        } else {
            $('#plus_btn').show();
            $('#filter').hide();
            $('.filter_body').html('');
        }
    });
    
    <?php 
        $inlet_success = $this->session->flashdata('inlet_success');
        $inlet_receipt_voucher_success = $this->session->flashdata('receipt_voucher_inlet');
        $inlet_error = $this->session->flashdata('inlet_error');
    ?>
    var alert_success = '<?= $inlet_success; ?>';
    var alert_receipt_success = '<?= $inlet_receipt_voucher_success; ?>';
    var alert_failure = '<?= $inlet_error; ?>';
    if(alert_receipt_success != ''){
        alert_d.text = alert_receipt_success;
        PNotify.success(alert_d);
    }else if(alert_success != ''){
        alert_d.text = alert_success;
        PNotify.success(alert_d);
    }else if(alert_failure != ''){
        alert_d.text = alert_failure;
        PNotify.error(alert_d);
    }
    
</script>