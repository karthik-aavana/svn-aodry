<div id="delete_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Records</h4>
            </div>
            <form role="form" id="delete_form" method="post" action="">
                <div class="modal-body" >
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input type="hidden" name="delete_id" id="delete_id" >
                                <label for="category_name">Are You Sure To Remove This Record ?</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" id="delete_button">Yes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                </div>
            </form>


        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).on("click", '.delete_button', function (e) {
        var id = $(this).data('id');
        var path = $(this).data('path');
        if (id != "" && path != "")
        {

            $("#delete_id").val(id);
            $('#delete_form').attr('action', base_url + path);
        } else
        {
            location.reload();
        }

    });
</script>
