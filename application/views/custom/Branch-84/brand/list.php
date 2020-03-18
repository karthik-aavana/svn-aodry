<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Brand</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                	<div id="plus_btn">
	                    <div class="box-header with-border">
	                        <h3 class="box-title">Brand</h3>
                            <a class="btn btn-sm btn-info pull-right addBrand" >Add Brand</a>
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
                                    <th>Brand Name</th>
                                    <th>Brand<br>First Prefix</th>
                                    <th>Brand<br>Last Prefix</th>
                                    <!-- <th>Brand<br>Separation</th> -->
                                    <th>Brand<br>Type</th>
                                    <th>Brand<br>Creation</th>
                                    <th>Brand<br>Readonly</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
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
$this->load->view('brand/brand_popup');
?>
<script>
    var BrandTable;
    $(document).ready(function () {
        BrandTable = getBrandList();        
    });

    function getBrandList(){
        var table = $('#list_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [[ 0, "desc" ]],
            "ajax": {
                "url": base_url + "brand/getBrandList",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
            	{"data": "action"},
                {"data": "brand_name"},
                {"data": "invoice_first_prefix"},
                {"data": "invoice_last_prefix"},
                /*{"data": "invoice_seperation"},*/
                {"data": "invoice_type"},
                {"data": "invoice_creation"},
                {"data": "invoice_readonly"},        
            ],
            "columnDefs": [
                { "searchable": false, "targets": 0 }
            ]
        });
        return table;
    }
</script>