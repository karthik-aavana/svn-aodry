<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">	
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li>
                <a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                Deposits
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <?php
            if (!@$bulk_error) {
                if ($this->session->flashdata('email_send') == 'success') {
                    ?>
                    <div class="col-sm-12">
                        <div class="alert alert-danger">
                            <button class="close" data-dismiss="alert" type="button">×</button>
                            Email has been send with the attachment.
                            <div class="alerts-con"></div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="col-md-12">
                <div class="box">
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <h3 class="box-title">Deposits</h3>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('deposit/add'); ?>">Add Deposits</a>
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
                                    <th>Type</th>
                                    <th>Date of Deposit</th>
                                    <th>Bank</th>
                                    <th>Name</th>
                                    <th>Comments</th>
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
<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url + "deposit",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                {"data": "action"},
                {"data": "deposit_type"},
                {"data": "deposit_date"},
                {"data": "deposit_bank"},
                {"data": "others_name"},
                {"data": "comments"}
            ],
            "columnDefs": [{'orderable': false, 'targets': [5]}],
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
            },
        });
        anime.timeline({loop: !0}).add({targets: ".ml8 .circle-white", scale: [0, 3], opacity: [1, 0], easing: "easeInOutExpo", rotateZ: 360, duration: 8e3}), anime({targets: ".ml8 .circle-dark-dashed", rotateZ: 360, duration: 8e3, easing: "linear", loop: !0});
<?php
$partner_success = $this->session->flashdata('partner_success');
$partner_error = $this->session->flashdata('partner_error');
?>
        var alert_success = '<?= $partner_success; ?>';
        var alert_failure = '<?= $partner_error; ?>';
        if (alert_success != '') {
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        } else if (alert_failure != '') {
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
        $(document).on('change', 'input[name="check_item"]', function () {
            $("input[name='check_item']").not(this).prop('checked', false);
        });
        $('.upload_customer_popup').click(function () {
            $('#upload_customer_doc').modal({show: true, backdrop: 'static', keyboard: false});
        });
    });
</script>