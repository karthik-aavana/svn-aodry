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
                            <h3 class="box-title">Company Settings</h3>
                            <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('company_setting/add'); ?>">Add Company</a>
                        </div>
                    </div>
                    <div id="filter">
                        <div class="box-header with-border box-body">
                            <div class="btn-group">
                                <span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#"> <a href="#" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Edit"> <i class="fa fa-pencil"></i> </a></span>
                                <span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#" data-path="sales/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="list_datatable" class="table table-bordered table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th>Company Code</th>
                                    <th>Company Name</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
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
                "url": base_url + "company_setting",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>
                    '}
                },
                "columns": [
                    {"data": "branch_code"},
                    {"data": "branch_name"},
                    {"data": "branch_address"},
                    {"data": "action"}
                ],
                 'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                    },
                });

                 anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});

        });
</script>
