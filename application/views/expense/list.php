<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Expense</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">           
            <div class="col-md-12">
                <div class="box">
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <h3 class="box-title">Expense</h3>
                            <?php
                            if (in_array($expense_module_id, $active_add)) {
                                ?>
                                <a data-toggle="modal" data-target="#expense_modal" class="btn btn-sm btn-info pull-right expense_btn" href="">Add Expense</a>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body">
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th width="9px">#</th>
                                    <!-- <th>Date</th> -->
                                    <th>Expense Title</th>
                                    <th>Expense Ledger</th>
                                    <th>Expense HSN/SAC</th>
                                    <th>Description</th>
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
<script>
    var dTable = '';
    $(document).ready(function () {
        dTable = GetAllExpences();

        $('.expense_btn').click(function(){
            $('#expense_modal #product_hsn_sac_code').val('');
        });
        <?php 
        $expence_success = $this->session->flashdata('expence_success');
        $expence_error = $this->session->flashdata('expence_error');
        ?>
        var alert_success = '<?= $expence_success; ?>';
        var alert_failure = '<?= $expence_error; ?>';
        if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });

    function GetAllExpences() {
        var table = $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "iDisplayLength": 15,
            "lengthMenu": [ [15, 25, 50,100, -1], [15, 25, 50,100, "All"] ],
            "ajax": {
                "url": base_url + "expense",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "expense_title"},
                {"data": "ledger"},
                {"data": "hsn"},
                {"data": "expense_description"},
                /*{"data": "added_user"},    */            
            ],
            "order": [[ 0, "desc" ]],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });

             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
        return table;
    }
</script>
<?php
$this->load->view('product/hsn_modal');
$this->load->view('layout/footer');
$this->load->view('general/delete_modal');
$this->load->view('expense/expense_add_modal');
$this->load->view('expense/expense_edit_modal');
?>