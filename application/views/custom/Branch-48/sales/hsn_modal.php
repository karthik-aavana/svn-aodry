<div id="hsn_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close s" data-dismiss="modal">&times;</button>
                <h4>HSN Lookup</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="sac_table" width="100%" class="table table-bordered table-striped sac_table ">
                                <thead>
                                <!-- <th>Sl No</th> -->
                                <th>HSN Codes</th>
                                <th>Description</th>
                                <th>Action</th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                   <!--  <div class="col-md-12">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('assets/js/service/') ?>sac_code.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $('#sac_table').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": base_url + "service/hsn_list",
                "dataType": "json",
                "type": "POST",
                "data": {'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'}
            },
            "columns": [
                // { "data": "sl_no" },
                {"data": "hsn_code"},
                {"data": "description"},
                {"data": "action"}
            ],
             'language': {
                'loadingRecords': '&nbsp;',
                'processing': ' <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="30px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>'
                },
            });
             anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});        
            $("#sac_table").on("click", "span.apply", function (event){

                var row = $(this).closest("tr");
                var s_code = +row.find('#hsn_id').text();
                var aaa = $('#sales_table_body tr:last').find('[name=product_hsn_sac_code]');
                //var rows = $('#sales_table_body').closest('[name=product_hsn_sac_code]');
                //var aaa = rows.find('input[name^="product_hsn_sac_code"]');
                console.log(aaa)
                console.log(s_code);
                var id = s_code;
                $.ajax({
                    url: base_url + 'product/get_hsn_code/' + id,
                    dataType: 'JSON',
                    method: 'POST',
                    success: function (result) {
                        //$('[name=product_hsn_sac_code]').val(result[0].hsn_code)
                        //$(aaa).val(result[0].hsn_code)
                        hsn_call(result);
                    }
                }); 

            });
            

            $("#sales_table_body").on(
                "click",
                '.hsn',
                function (event) {
                    $(this).next().addClass('active')
                    //rows.addClass('active')
                    console.log('fkjfj');
                   
            });
            
            function hsn_call(result){
                $("#sales_table_body")
                    .find('.active')
                    .each(function () {
                        $(this).val(result[0].hsn_code);
                        //var rows = $(this).closest("tr");
                        var rows = $(this).closest("tr");
                       
                        console.log('hsn-chng')
                        calculateTable(rows);
                        $(this).removeClass('active');
                    });
            }


            $(document).on('click', '.s', function () {
                console.log('hhhhhhh')
                hsn_close();
            });

            function hsn_close(){
                $("#sales_table_body")
                    .find('.active')
                    .each(function () {
                        $(this).removeClass('active');
                });
            }
            
    });
</script>
