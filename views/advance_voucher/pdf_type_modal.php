<div class="modal fade" id="pdf_type_modal" role="dialog">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title"> Select Currency for PDF </h4>

            </div>

            <form role="form" id="sales_pdf_form" method="post" action="" target="_blank">

                <div class="modal-body" >

                    <input type="hidden" name="sales_id" id="pdf_type_advance_id" value="">

                    <input type="hidden" name="document_type" id="pdf_type_advance_name" value="">
                    

                    <div class="row currency_div" style="display: none;">

                        <div class="col-sm-12">

                            <div class="radio">

                                

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit" class="btn btn-primary" id="pdf_go">Go</button>

                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>

            </form>

        </div>

    </div>

</div>



<script type="text/javascript">

    $(document).ready(function () {

        $('input[name="pdf_type_check"]').on('change', function () {

            $('input[name="pdf_type_check"]').not(this).prop('checked', false);

        });

    });

    $(document).on("click", '.pdf_button', function (e) {

        var id = $(this).data('id');

        var name = $(this).data('name');

        var b_curr = $(this).attr('b_curr');

        var b_code = $(this).attr('b_code');

        var c_curr = $(this).attr('c_curr');

        var c_code = $(this).attr('c_code');

        if(b_curr != c_curr && b_code != '' && c_code != ''){

            var ht = '<label class="radio-inline"><input type="radio" name="print_currency" checked value="'+b_curr+'">'+b_code+'</label>\
            <label class="radio-inline"><input type="radio" name="print_currency" value="'+c_curr+'">'+c_code+'</label>';

            $('#pdf_type_modal .currency_div .radio').html(ht);

            $('#pdf_type_modal .currency_div').show();

        }else{

            $('#pdf_type_modal .currency_div .radio').html('');

            $('#pdf_type_modal .currency_div').hide();

        }

        $("#pdf_type_advance_id").val(id);

        $("#pdf_type_advance_name").val(name);

        $('#sales_pdf_form').attr('action', $(this).attr('href1'));

        //tinyMCE.get('status_content').setContent(content);

    });

</script>

