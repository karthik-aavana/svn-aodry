<style type="text/css">
    .input {
        position: relative;
        z-index: 1;
        display: inline-block;
    }

    .input__field {
        position: relative;
        display: block;
        float: right;
        padding: 0.8em;
        width: 60%;
        border: none;
        border-radius: 0;
        background: #f0f0f0;
        color: #aaa;
        font-weight: bold;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        -webkit-appearance: none; /* for box shadows to show on iOS */
    }

    .input__field:focus {
        outline: none;
    }

    .input__label {
        display: inline-block;
        float: right;
        padding: 0 1em;
        width: 40%;
        color: #6a7989;
        font-weight: bold;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .input__label-content {
        position: relative;
        display: block;
        padding: 1.6em 0;
        width: 100%;
    }
    .input--hoshi {
        overflow: hidden;
    }

    .input__field--hoshi {
        margin-top: 1em;
        padding: 1.2em 0.15em;
        width: 100%;
        background: transparent;
        color: #595F6E;
    }

    .input__label--hoshi {
        position: absolute;
        bottom: 0;
        left: 0;
        padding: 0 0.25em;
        width: 100%;
        height: calc(100% - 1em);
        text-align: left;
        pointer-events: none;
    }

    .input__label-content--hoshi {
        position: absolute;
    }

    .input__label--hoshi::before,
    .input__label--hoshi::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: calc(100% - 10px);
        border-bottom: 1px solid #333;
    }

    .input__label--hoshi::after {
        margin-top: 2px;
        border-bottom: 4px solid red;
        -webkit-transform: translate3d(-100%, 0, 0);
        transform: translate3d(-100%, 0, 0);
        -webkit-transition: -webkit-transform 0.3s;
        transition: transform 0.3s;
    }

    .input__label--hoshi-color-1::after {
        border-color: hsl(200, 100%, 50%);
    }

    .input__label--hoshi-color-2::after {
        border-color: hsl(160, 100%, 50%);
    }

    .input__label--hoshi-color-3::after {
        border-color: hsl(20, 100%, 50%);
    }

    .input__field--hoshi:focus + .input__label--hoshi::after,
    .input--filled .input__label--hoshi::after {
        -webkit-transform: translate3d(0, 0, 0);
        transform: translate3d(0, 0, 0);
    }

    .input__field--hoshi:focus + .input__label--hoshi .input__label-content--hoshi,
    .input--filled .input__label-content--hoshi {
        -webkit-animation: anim-1 0.3s forwards;
        animation: anim-1 0.3s forwards;
    }

    @-webkit-keyframes anim-1 {
        50% {
            opacity: 0;
            -webkit-transform: translate3d(1em, 0, 0);
            transform: translate3d(1em, 0, 0);
        }
        51% {
            opacity: 0;
            -webkit-transform: translate3d(-1em, -40%, 0);
            transform: translate3d(-1em, -40%, 0);
        }
        100% {
            opacity: 1;
            -webkit-transform: translate3d(0, -40%, 0);
            transform: translate3d(0, -40%, 0);
        }
    }

    @keyframes anim-1 {
        50% {
            opacity: 0;
            -webkit-transform: translate3d(1em, 0, 0);
            transform: translate3d(1em, 0, 0);
        }
        51% {
            opacity: 0;
            -webkit-transform: translate3d(-1em, -40%, 0);
            transform: translate3d(-1em, -40%, 0);
        }
        100% {
            opacity: 1;
            -webkit-transform: translate3d(0, -40%, 0);
            transform: translate3d(0, -40%, 0);
        }
    }
</style>

<div class="modal fade" id="move_to_categorized" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 52%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 id="cat" class="modal-title">Categories</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form role="form" method="post" class="f1">
                            <div class="f1-steps">
                                <div class="f1-progress">
                                    <div class="f1-progress-line" data-now-value="12.5" data-number-of-steps="4" style="width: 12.5%;"></div>
                                </div>
                                <div class="f1-step active">
                                    <div class="f1-step-icon">
                                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                                    </div>
                                    <p>
                                        Step-1
                                    </p>
                                </div>
                                <div class="f1-step">
                                    <div class="f1-step-icon">
                                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                                    </div>
                                    <p>
                                        Step-2
                                    </p>
                                </div>
                                <!--   <div class="f1-step">
                                      <div class="f1-step-icon">
                                          <i class="fa fa-angle-right" aria-hidden="true"></i>
                                      </div>
                                      <p>
                                          Step-3
                                      </p>
                                  </div>
                                  <div class="f1-step">
                                      <div class="f1-step-icon">
                                          <i class="fa fa-angle-right" aria-hidden="true"></i>
                                      </div>
                                      <p>
                                          Step-4
                                      </p>
                                  </div> -->
                            </div>
                            <fieldset>
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Please Select the Category :</label>
                                            <div class="input-group margin-bottom-sm">

                                                <input class="form-control" id="search" name="search" type="text" placeholder="Search">
                                                <input type="hidden" id="statement_id" name="statement_id" value="">
                                                <input type="hidden" id="id" name="id" value="">
                                                <input type="hidden" id="cat_amount" name="cat_amount" value="">
                                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">

                                            <?php
                                            $myFile   = base_url() . "assets/json/type.json";
                                            $arr_data = array(); // create empty array

                                            try
                                            {
                                                //Get data from existing json file
                                                $jsondata = file_get_contents($myFile);

                                                // converts json data into array
                                                $arr_data = json_decode($jsondata, true);
                                            } catch (Exception $e)
                                            {
                                                echo 'Caught exception: ', $e->getMessage(), "\n";
                                            }

                                            for ($i = 0; $i < sizeof($arr_data); $i++)
                                            {
                                                if ($i < 5)
                                                {
                                                    $cid = $arr_data[$i]['cid'];
                                                    ?>
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input class="checkbox1" type="checkbox" name="checkbox1" value="<?php echo $arr_data[$i]['name']; ?>"> <?php echo $arr_data[$i]['name']; ?>
                                                        </label>
                                                    </div>
                                                <?php }
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">

                                            <?php
                                            for ($i = 0; $i < sizeof($arr_data); $i++)
                                            {
                                                if ($i >= 5 && $i < 10)
                                                {
                                                    $cid = $arr_data[$i]['cid'];
                                                    ?>
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input class="checkbox1" type="checkbox" name="checkbox1" value="<?php echo $arr_data[$i]['name']; ?>"> <?php echo $arr_data[$i]['name']; ?>
                                                        </label>
                                                    </div>
                                                <?php }
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">

                                            <?php
                                            for ($i = 0; $i < sizeof($arr_data); $i++)
                                            {
                                                if ($i >= 10 && $i < 15)
                                                {
                                                    $cid = $arr_data[$i]['cid'];
                                                    ?>
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input class="checkbox1" type="checkbox" name="checkbox1" value="<?php echo $arr_data[$i]['name']; ?>"> <?php echo $arr_data[$i]['name']; ?>
                                                        </label>
                                                    </div>
    <?php }
}
?>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="f1-buttons">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                                Close
                                            </button>
                                            <button type="button" id="next" class="btn btn-next btn-default">
                                                Next
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <h4 id="question" style="float: left;"></h4>
                                <div style="float: right;">
                                    <a href="<?php echo base_url(); ?>bank_statement/add_voucher_invoice"><button id="add_voucher_invoice" type="button" class="btn btn-default">
                                            Add Voucher
                                        </button></a>
                                </div>
                                <div id="answer">

                                </div>
                                <div id="invoice">

                                </div>
                                <div><span class="validation-color" id="err_mismatch_amount" hidden="true">The voucher amount and statement amount should be same.</span></div>
                                <div class="col-md-12">
                                    <div class="f1-buttons">

                                        <button type="button" class="btn btn-previous btn-default">
                                            Previous
                                        </button>
                                        <button id="next2" type="button" class="btn btn-default">
                                            Next
                                        </button>

                                        <input type="hidden" id="sales_purchase_id" name="sales_purchase_id" value="">
                                        <!-- <button id="add_voucher" type="submit" class="btn btn-default">
                                            Add Voucher
                                        </button> -->

                                    </div>
                                </div>
                            </fieldset>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<input type="hidden" id="open_myModal" name="open_myModal" data-toggle="modal" data-target="#myModal">
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <!-- <button type="button" class="close" data-dismiss="modal">x</button> -->
                <h4 class="modal-title" style="float: left;">Success</h4>
            </div>
            <div class="modal-body">
                <p style="float: left;">Statement categorized successfully.</p>
            </div>
            <div class="modal-footer">
                <button id="categorized_success" type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $('#next').hide();
    $('#next2').hide();

    $("input:checkbox").on('click', function () {
        // in the handler, 'this' refers to the box clicked on
        var $box = $(this);
        if ($box.is(":checked")) {
            // the name of the box is retrieved using the .attr() method
            // as it is assumed and expected to be immutable
            var group = "input:checkbox[name='" + $box.attr("name") + "']";
            // the checked state of the group/box on the other hand will change
            // and the current value is retrieved using .prop() method
            $(group).prop("checked", false);
            $box.prop("checked", true);
        } else {
            $box.prop("checked", false);
        }
    });

    $(function () {
        $(".open-rawdata").click(function () {
            // $('#statement_id').val($(this).data('id'));
            document.getElementById("statement_id").value = $(this).data('sid');
            document.getElementById("id").value = $(this).data('id');
            document.getElementById("cat_amount").value = $(this).data('amount');

        });
    });

    $('.checkbox1').on('change', function () {
        $('.checkbox1').not(this).prop('checked', false);
        $('.checkbox').prop('checked', false);
        $('#search2').attr('readonly', false);
        $('#search2').val('');
        var val1 = $('input:checkbox[name=checkbox1]:checked').val();

        $('#search').val(val1);

        if (val1 == undefined)
        {
            //document.getElementById('cat').innerHTML='Categories';
            $('#cat').html('Categories');
            $('#next').hide();
            $('#add_voucher').hide();
            $('#search').attr('readonly', false);
        } else
        {
            //document.getElementById('cat').innerHTML='Category : '+val1;
            $('#cat').html('Category : ' + val1);
            $('#next').show();
            $('#add_voucher').hide();
            $('#search').attr('readonly', true);
        }

    });

    $(document).ready(function () {
        $(function () {
            $('#search').autoComplete({
                minChars: 1,
                cache: false,
                source: function (term, suggest) {
                    term = term.toLowerCase();
                    var exp = $('#search').val();
                    //var warehouse_id = $('#warehouse').val();
                    $('#next').hide();
                    $.ajax({
                        url: "<?php echo base_url('bank_statement/getCategory') ?>/" + term,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            var result = [];
                            var result2 = [];

                            for (var i = 0; i < data.length; i++)
                            {
                                result2.push(data[i]);
                            }

                            for (var i = 0; i < data.length; i++)
                            {
                                data[i] = data[i].toLowerCase();
                                if (data[i].match(term))
                                {
                                    result.push(result2[i]);
                                }

                            }
                            suggest(result);
                        }

                    });
                },
                onSelect: function (event, ui) {
                    $('#cat').html('Category : ' + ui);
                    $('#next').show();
                }
            });
        });
    });

    $(document).ready(function () {
        $(function () {
            $('#search2').autoComplete({
                minChars: 1,
                cache: false,
                source: function (term, suggest) {
                    var text = $('#cat').text();
                    var text2 = [];
                    text2 = text.split(":");
                    $('#cat').html(text2[0] + " : " + text2[1]);

                    term = term.toLowerCase();
                    var exp = $('#search2').val();
                    //var warehouse_id = $('#warehouse').val();
                    $('#next2').hide();
                    $.ajax({
                        url: "<?php echo base_url('bank_statement/getSubCategory') ?>/" + term,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            var result = [];
                            var result2 = [];

                            for (var i = 0; i < data.length; i++)
                            {
                                result2.push(data[i]);
                            }

                            for (var i = 0; i < data.length; i++)
                            {
                                data[i] = data[i].toLowerCase();
                                if (data[i].match(term))
                                {
                                    result.push(result2[i]);
                                }

                            }
                            suggest(result);
                        }

                    });
                },
                onSelect: function (event, ui) {
                    //document.getElementById('cat').innerHTML=text+' : '+ui;
                    var text = $('#cat').text();
                    var text2 = [];
                    text2 = text.split(":");
                    $('#cat').html(text2[0] + " : " + text2[1] + " : " + ui);

                    $('#next2').show();
                }
            });
        });
    });

    $(document).ready(function () {
        $('#next').on('click', function (event) {
            document.getElementById('invoice').innerHTML = '';
            $("#next2").hide();

            $.ajax({

                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('bank_statement/get_question_answer') ?>",
                data: {name: $('#search').val(), sid: $('#statement_id').val(), id: $('#id').val(), amount: $('#cat_amount').val()},
                success: function (data) {
                    document.getElementById('question').innerHTML = data[0];
                    document.getElementById('answer').innerHTML = data[1];
                    document.getElementById('invoice').innerHTML = data[2];
                    $('.checkbox').on('change', function () {
                        $('.checkbox').not(this).prop('checked', false);
                        $('.checkbox_v').prop('checked', false);
                        var val1 = $('input:checkbox[name=checkbox]:checked').val();
                        if (val1 == undefined)
                        {
                            $("#next2").hide();
                        } else
                        {
                            $("#next2").show();
                            $("#add_voucher").hide();
                        }
                    });

                    $('.checkbox_v').on('change', function () {
                        $('.checkbox_v').not(this).prop('checked', false);
                        $('.checkbox').prop('checked', false);
                        var val1 = $('input:checkbox[name=checkbox_v]:checked').val();

                        if (val1 == undefined)
                        {
                            $("#add_voucher").hide();
                        } else
                        {
                            var val2 = val1.split(':');
                            $('#sales_purchase_id').val(val2[1]);
                            $("#add_voucher").show();
                            $("#next2").hide();
                        }
                    });

                    $('.checkbox').on('change', function () {
                        $('.checkbox').not(this).prop('checked', false);
                        $('.checkbox_v').prop('checked', false);
                        var val1 = $('input:checkbox[name=checkbox]:checked').val();
                        if (val1 == undefined)
                        {
                            $("#next2").hide();
                        } else
                        {
                            $("#next2").show();
                            $("#add_voucher").hide();
                        }
                    });

                    $('.checkbox_g').on('change', function () {
                        $('.checkbox_g').not(this).prop('checked', false);
                        var val1 = $('input:checkbox[name=checkbox_g]:checked').val();
                        if (val1 == undefined)
                        {
                            $("#next2").hide();
                        } else
                        {
                            $("#next2").show();
                            $("#add_voucher").hide();
                        }
                    });

                    $('#customer').on('change', function (event) {

                        $.ajax({

                            type: "POST",
                            dataType: 'json',
                            url: "<?php echo base_url('bank_statement/get_customer_invoices') ?>",
                            data: {id: $('#customer').val()},
                            success: function (data) {
                                document.getElementById('invoice').innerHTML = data;
                                $('.checkbox').on('change', function () {
                                    $('.checkbox').not(this).prop('checked', false);
                                    $('.checkbox_v').prop('checked', false);
                                    var val1 = $('input:checkbox[name=checkbox]:checked').val();
                                    if (val1 == undefined)
                                    {
                                        $("#next2").hide();
                                    } else
                                    {
                                        $("#next2").show();
                                        $("#add_voucher").hide();
                                    }
                                });

                                $('.checkbox_v').on('change', function () {
                                    $('.checkbox_v').not(this).prop('checked', false);
                                    $('.checkbox').prop('checked', false);
                                    var val1 = $('input:checkbox[name=checkbox_v]:checked').val();

                                    if (val1 == undefined)
                                    {
                                        $("#add_voucher").hide();
                                    } else
                                    {
                                        var val2 = val1.split(':');
                                        $('#sales_purchase_id').val(val2[1]);
                                        $("#add_voucher").show();
                                        $("#next2").hide();
                                    }
                                });
                            }
                        });
                        // if($('#customer').val()!='')
                        // {
                        //     $("#next2").show();
                        // }
                        // else
                        // {
                        //     document.getElementById('invoice').innerHTML='';
                        //     $("#next2").hide();
                        // }
                        if ($('#customer').val() == '')
                        {
                            document.getElementById('invoice').innerHTML = '';
                        }
                    });

                    $('#suppliers').on('change', function (event) {

                        $.ajax({
                            type: "POST",
                            dataType: 'json',
                            url: "<?php echo base_url('bank_statement/get_suppliers_expense_invoices') ?>",
                            data: {id: $('#suppliers').val(), category_type: $('#category_type').val()},
                            success: function (data) {
                                document.getElementById('invoice').innerHTML = data;
                                $('.checkbox').on('change', function () {
                                    $('.checkbox').not(this).prop('checked', false);
                                    $('.checkbox_v').prop('checked', false);
                                    var val1 = $('input:checkbox[name=checkbox]:checked').val();
                                    if (val1 == undefined)
                                    {
                                        $("#next2").hide();
                                    } else
                                    {
                                        $("#next2").show();
                                        $("#add_voucher").hide();
                                    }
                                });

                                $('.checkbox_v').on('change', function () {
                                    $('.checkbox_v').not(this).prop('checked', false);
                                    $('.checkbox').prop('checked', false);
                                    var val1 = $('input:checkbox[name=checkbox_v]:checked').val();

                                    if (val1 == undefined)
                                    {
                                        $("#add_voucher").hide();
                                    } else
                                    {
                                        var val2 = val1.split(':');
                                        $('#sales_purchase_id').val(val2[1]);
                                        $("#add_voucher").show();
                                        $("#next2").hide();
                                    }
                                });
                            }
                        });
                        if ($('#suppliers').val() == '')
                        {
                            document.getElementById('invoice').innerHTML = '';
                        }
                    });

                    $('#general_type').on('change', function (event) {

                        $.ajax({
                            type: "POST",
                            dataType: 'json',
                            url: "<?php echo base_url('bank_statement/get_general_voucher') ?>",
                            data: {general_type: $('#general_type').val(), category_type: $('#category_type').val()},
                            success: function (data) {
                                document.getElementById('invoice').innerHTML = data;
                                $('.checkbox').on('change', function () {
                                    $('.checkbox').not(this).prop('checked', false);
                                    var val1 = $('input:checkbox[name=checkbox]:checked').val();
                                    if (val1 == undefined)
                                    {
                                        $("#next2").hide();
                                    } else
                                    {
                                        $("#next2").show();
                                        $("#add_voucher").hide();
                                    }
                                });
                            }
                        });
                        if ($('#general_type').val() == '')
                        {
                            document.getElementById('invoice').innerHTML = '';
                        }
                    });
                }
            });
        });
    });


    $(document).ready(function () {
        $('#next2').on('click', function (event) {
            var val1 = $('input:checkbox[name=checkbox]:checked').val();

            var val2 = $('input:checkbox[name=checkbox_g]:checked').val();

            if (val2 != undefined)
            {
                val1 = val2;
            }

            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('bank_statement/store_statement') ?>",
                data: {tid: val1},
                success: function (data)
                {
                    document.getElementById('tbody_rawdata').innerHTML = data[0];
                    document.getElementById('tbody_categorized').innerHTML = data[1];
                    document.getElementById('tbody_suspense').innerHTML = data[2];
                    if (data[3] == 0)
                    {
                        $('#err_mismatch_amount').show();
                    } else
                    {
                        $('#open_myModal').click();
                    }
                    $(function () {
                        $(".open-rawdata").click(function () {
                            // $('#statement_id').val($(this).data('id'));
                            document.getElementById("statement_id").value = $(this).data('sid');
                            document.getElementById("id").value = $(this).data('id');

                        });
                    });
                }
            });

        });
    });

    $(document).ready(function () {
        $('#categorized_success').on('click', function (event) {
            setTimeout(function () {// wait for 5 secs(2)
                location.reload(); // then reload the page.(3)
            });

        });
    });


</script>

<script src="<?php echo base_url('assets/plugins/autocomplite/') ?>jquery.auto-complete.js"></script>
