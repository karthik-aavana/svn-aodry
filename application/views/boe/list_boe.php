<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div id="plus_btn">
                        <div class="box-header with-border">
                            <h3 class="box-title">BOE(Bill of Entry)</h3>
                            <a href="<?php echo base_url('boe/add'); ?>" class="btn btn-info btn-sm pull-right">Add BOE</a>
                        </div>
                    </div>
                    <div id="filter">
                        <div class="box-header with-border box-body filter_body">
                            <div class="btn-group">
                                <span><a href="#" class="btn btn-app view" data-toggle="tooltip" data-placement="bottom" data-original-title="View BOE"> <i class="fa fa-eye"></i> </a></span>
                                <span><a href="#" target="_blank" class="btn btn-app pdf" data-toggle="tooltip" data-placement="bottom" title="Download PDF"><i class="fa fa-file-pdf-o"></i></a></span>                                
                                <span><a href="javascript:void(0);" class="btn btn-app edit" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit BOE" > <i class="fa fa-pencil"></i> </a></span>
                                <span class="delete_button" data-backdrop="static" data-keyboard="false"  data-toggle="modal" data-target="#delete_modal" data-id="" data-path="boe/delete_boe" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?">
                                    <a href="#" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="Delete BOE"> <i class="fa fa-trash-o"></i> </a>
                                </span>
                            </div>
                        </div>
                    </div>           
                    <div class="box-body">
                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th width="9px">#</th>
                                    <th>BOE voucher Number</th>
                                    <th>BOE Date</th>
                                    <th>BOE Reference Number</th>
                                    <th>Net Duties</th>
                                    <th>BCD</th>
                                    <th>IGST</th>
                                    <th>Other Duties</th>
                                    <th>Purchase Invoice</th>
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
$this->load->view('advance_voucher/connect_to_sales');
$this->load->view('general/delete_modal');
?>
<script>
    $(document).ready(function () {
        $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "boe",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            'language': {
            'loadingRecords': '&nbsp;',
            'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
            },
            "columns": [
                {"data": "check"},
                {"data": "voucher_number"},
                {"data": "date"},
                {"data": "reference_number"},
                {"data": "net_duties"},
                {"data": "bcd_amount"},
                {"data": "igst_amount"},
                {"data": "other_duties"},
                {"data": "purchase_invoice"},
            ]
        });

        anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});

        $(document).on('click', 'input[name="check_boe"]', function () {
            if ($(this).is(":checked")) {
                $(document).find('[name=check_boe]').prop('checked', false);
                $(this).prop('checked', true);
                $('#filter .edit').attr('href', $(this).parent().find('[name=edit]').val());
                $('#filter .pdf').attr('href', $(this).parent().find('[name=pdf]').val());
                $('#filter .view').attr('href', $(this).parent().find('[name=view]').val());
                $('#filter .delete_button').attr('data-id', $(this).parent().find('[name=delete]').val());
                $('#plus_btn').hide();
                $('#filter').show();
            } else {
                $('#plus_btn').show();
                $('#filter').hide();
            }
        });
        <?php 
        $boe_success = $this->session->flashdata('boe_success');
        $boe_error = $this->session->flashdata('boe_error');
        ?>
        var alert_success = '<?= $boe_success; ?>';
        var alert_failure = '<?= $boe_error; ?>';
        if(alert_success != ''){
            alert_d.text = alert_success;
            PNotify.success(alert_d);
        }else if(alert_failure != ''){
            alert_d.text = alert_failure;
            PNotify.error(alert_d);
        }
    });
</script>