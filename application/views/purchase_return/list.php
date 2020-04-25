<?phpdefined('BASEPATH') OR exit('No direct script access allowed');$this->load->view('layout/header');?><div class="content-wrapper">       <div class="fixed-breadcrumb">        <ol class="breadcrumb abs-ol">            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>            <li class="active">Purchase Return</li>        </ol>    </div>    <section class="content mt-50">        <div class="row">            <!-- <?php            if ($this->session->flashdata('email_send') == 'success') {                ?>                <div class="col-sm-12">                    <div class="alert alert-success">                        <button class="close" data-dismiss="alert" type="button">×</button>                        Email has been send with the attachment.                        <div class="alerts-con"></div>                    </div>                </div>                <?php            }            ?> -->            <div class="col-md-12">                <div class="box">                    <div id="plus_btn">                    <div class="box-header with-border">                        <h3 class="box-title">Purchase Return</h3>                          <?php                  if (in_array($purchase_return_module_id , $active_add))                  {                      ?>                      <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('purchase_return/add'); ?>">Add Purchase Return </a>                  <?php } ?>                    </div>                    </div>                    <div id="filter">                        <div class="box-header with-border box-body filter_body"></div>                    </div>                                        <div class="box-body">                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive" >                            <thead>                                <tr>                                    <th width="9px">#</th>                                    <th>Voucher Number</th>                                    <th>Purchase Voucher Number</th>                                    <th>Supplier Name</th>                                    <th>Amount</th>                                </tr>                            </thead>                            <tbody>                                                            </tbody>                        </table>                    </div>                </div>            </div>        </div>    </section></div><?php$this->load->view('layout/footer');$this->load->view('general/delete_modal');$this->load->view('purchase_return/compose_mail');?><script>   // $('#list_datatable').DataTable();        $('#select_all').click(function(event) {	if (this.checked) {		// Iterate each checkbox		$(':checkbox').each(function() {			this.checked = true;			check_all();		});	} else {		$(':checkbox').each(function() {			this.checked = false;			check_all();		});	}});/*$(document).on('change', 'input[type="checkbox"][name="check_item"]', function() {	check_all();});function check_all() {	var i = 0;	$.each($('input[type="checkbox"][name="check_item"]:checked'), function() {		i++;	});	if (i == 1) {		$('#plus_btn').hide();		$('#filter').show();		$('#filter span:first-child').show();	} else if (i > 1) {		$('#filter span:first-child').hide();	} else {		$('#plus_btn').show();		$('#filter').hide();	}}*/</script><script>    $(document).ready(function () {        $('#list_datatable').DataTable({            "processing": true,            "serverSide": true,            "responsive": true,            "ajax": {                "url": base_url + "purchase_return",                "dataType": "json",                "type": "POST",                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}            },            "columns": [                {"data": "action"},                {"data": "voucher_no"},                {"data": "purchase_voucher_no"},                {"data": "supplier"},                {"data": "grand_total"},            ],             'language': {                'loadingRecords': '&nbsp;',                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'                },            });             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});        <?php         $purchase_return_success = $this->session->flashdata('purchase_return_success');        $purchase_return_error = $this->session->flashdata('purchase_return_error');        ?>        var alert_success = '<?= $purchase_return_success; ?>';        var alert_failure = '<?= $purchase_return_error; ?>';        if(alert_success != ''){            alert_d.text = alert_success;            PNotify.success(alert_d);        }else if(alert_failure != ''){            alert_d.text = alert_failure;            PNotify.error(alert_d);        }    });</script>