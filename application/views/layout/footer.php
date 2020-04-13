<footer class="main-footer">
    <div class="pull-right hidden-xs">
        Version 2.0
    </div>
    Copyright &copy; <?php echo date('Y'); ?> <a href="https://aavana.in/" class="text-white" target="_blank">Aavana Corporate Solutions Pvt Ltd</a>. All rights reserved.
</footer> 
<?php
$financial_year_title_foot = $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE');
$fin_data = explode("-", $financial_year_title_foot);
if (isset($fin_data[0]) && isset($fin_data[1])) {
    ?>
    <input type="hidden" id="financial_year_from_footer" value="<?= trim($fin_data[0]) ?>">
    <input type="hidden" id="financial_year_to_footer" value="<?= trim($fin_data[1]) ?>">
<?php } ?>
<script>
    var get_csrf_token_name = "<?php echo $this->security->get_csrf_token_name(); ?>";
    var get_csrf_hash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var base_url = "<?php echo base_url(); ?>";
</script>
<!-- ./wrapper -->
<!-- Control Sidebar -->
<!-- Cancel Button -->
<script>
    function cancel(path) {
        window.location.href = '<?php echo base_url(); ?>' + path;
    }
</script>

<!-- <script src="<?php echo base_url(); ?>assets/lib/jquery-ui/jquery-ui.min.js"></script> -->
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url(); ?>assets/js/jquery.menu-aim.js"></script> <!-- menu aim -->
<script src="<?php echo base_url(); ?>assets/js/main.js"></script> <!-- Resource jQuery -->
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Morris.js charts -->
<script src="<?php echo base_url(); ?>assets/lib/raphael/raphael-min.js"></script>
<script src="<?php echo base_url('assets/'); ?>plugins/morris/morris.min.js"></script>
<!-- Sparkline -->
<script src="<?php echo base_url('assets/'); ?>plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="<?php echo base_url('assets/'); ?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?php echo base_url('assets/'); ?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo base_url('assets/'); ?>plugins/knob/jquery.knob.js"></script>
<!-- daterangepicker -->
<script src="<?php echo base_url(); ?>assets/lib/moment.js/moment.min.js"></script>
<script src="<?php echo base_url('assets/'); ?>plugins/daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?php echo base_url('assets/'); ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<!-- CK Editor -->
<script src="<?php echo base_url('assets/'); ?>dist/js/bootstrap-datetimepicker.js"></script>
<script src="<?php echo base_url('assets/'); ?>dist/js/moment-2.10.3.js"></script>
<script src="<?php echo base_url('assets/'); ?>plugins/ckeditor/ckeditor.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo base_url('assets/'); ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo base_url('assets/'); ?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url('assets/'); ?>plugins/fastclick/fastclick.js"></script>
<!-- DataTables -->
<script src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/jquery.floatThead.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/responsive.bootstrap.min.js"></script>
<script src="<?php echo base_url('assets/'); ?>graph/loader.js"></script>
<script src="<?php echo base_url('assets/'); ?>js/anime.min.js"></script>

<script type="text/javascript">
    var c = document.querySelector('.composition');
    var l = document.querySelector('#loader');
    if(c != null || l != null){
        var s = document.createElement("script");
        s.type = "text/javascript";
        s.src = "<?php echo base_url('assets/'); ?>js/app3860.js?v=1";
        $("body").append(s);

        var s = document.createElement("script");
        s.type = "text/javascript";
        s.src = "<?php echo base_url('assets/'); ?>js/moving-letters.js";
        $("body").append(s);

        var s = document.createElement("script");
        s.type = "text/javascript";
        s.src = "<?php echo base_url('assets/'); ?>js/loader-letters.js";
        $("body").append(s);
    }
</script>

<script src="<?php echo base_url(); ?>assets/js/modernizr.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('assets/'); ?>dist/js/app.min.js"></script>
<!-- Select2 -->
<script src="<?php echo base_url('assets/'); ?>plugins/select2/select2.full.min.js"></script>
<!-- InputMask -->
<script src="<?php echo base_url('assets/'); ?>plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?php echo base_url('assets/'); ?>plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?php echo base_url('assets/'); ?>plugins/input-mask/jquery.inputmask.extensions.js"></script>
<!-- bootstrap color picker -->
<script src="<?php echo base_url('assets/'); ?>plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<!-- bootstrap time picker -->
<script src="<?php echo base_url('assets/'); ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
<!-- iCheck 1.0.1 -->
<script src="<?php echo base_url('assets/'); ?>plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/pnotify.custom.min.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?php echo base_url('assets/plugins/autocomplite/') ?>jquery.auto-complete.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/tagsinput/bootstrap-tagsinput.js"></script>
<script src="<?php echo base_url(); ?>assets/js/PNotify/PNotify.js"></script>
<script src="<?php echo base_url(); ?>assets/js/PNotify/PNotifyAnimate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/PNotify/PNotifyButtons.js"></script>
<script src="<?php echo base_url(); ?>assets/js/PNotify/PNotifyCallbacks.js"></script>
<script src="<?php echo base_url(); ?>assets/js/PNotify/PNotifyConfirm.js"></script>
<script src="<?php echo base_url(); ?>assets/js/PNotify/PNotifyStyleMaterial.js"></script>
<script src="<?php echo base_url(); ?>assets/js/custom.js"></script>
<script language="javascript" src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.buttons.min.js"></script>
<script language="javascript" src="<?php echo base_url(); ?>assets/plugins/datatables/buttons.flash.min.js"></script>
<script language="javascript" src="<?php echo base_url(); ?>assets/plugins/datatables/jszip.min.js"></script>
<script language="javascript" src="<?php echo base_url(); ?>assets/plugins/datatables/pdfmake.min.js"></script>
<script language="javascript" src="<?php echo base_url(); ?>assets/plugins/datatables/vfs_fonts.js"></script>
<script language="javascript" src="<?php echo base_url(); ?>assets/plugins/datatables/buttons.html5.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

<script>
            $(document).ready(function () {                
                $("#get_password").click(function () {
                    var f_y_password = $("#f_y_password").val();
                    $.ajax({
                        type: "post",
                        url: "<?= base_url('financialyear') ?>",
                        data: {
                            f_y_password: f_y_password},
                        success: function (data) {
                            if (data == "sucess") {
                                $("#financial_year_view").show();
                                $("#submit_f_y").hide();
                                $("#financial_year_model").hide();
                                $(".modal-backdrop ").hide();
                                setTimeout(function () {
                                    location.reload();
                                });
                            } else if (data == "change") {
                                $(".text-danger").text('Please Set your password in Company settings');
                            } else {
                                $(".text-danger").text('Wrong Password');
                            }
                        }
                    });
                });
                var s_count = $('#s_pending').text();
                var p_count = $('#p_pending').text();
                var e_pending = $('#e_pending').text();
                var total = parseInt(s_count, 10) + parseInt(p_count, 10) + parseInt(e_pending, 10);
                $('#n_total').text(total);
            });
        </script>                                                                
        <script>
            $(document).ready(function () {
                $(document).on("click", "#filter .btn-app i", function () {
                    // alert("i ma here");
                    $("[data-toggle='tooltip']").tooltip('hide');
                });
                $(".content").click(function () {
                    $(".treeview-menu, .treeview-sub-menu").css({
                        "display": "none",
                    });
                });

                $(".cd-dropdown-content").mouseleave(function() {
                    $('.cd-dropdown-trigger').trigger('click');
                });

            });
            $(document).on("click", ".date .input-group-addon", function () {
                $(this).parent().find('.datepicker').trigger('focus');
            });

            function mymenu(th) {
                var maxaHeight = th.offset().top;
                var windowHeight = (maxaHeight - $(window).scrollTop());
                var subMenu = th.find('.treeview-menu').height();
                if (windowHeight > 340) {
                    $(".skin-blue .sidebar-menu>li>.treeview-menu").css("margin-top", -subMenu);
                } else {
                    $(".skin-blue .sidebar-menu>li>.treeview-menu").css("margin-top", -41);
                }
            }
            $(".skin-blue .sidebar-menu>li").click(function () {
                mymenu($(this));
            });



            //  function mymenu(ths) {
            //     var maxaHeight = ths.offset().top;
            //     var windowHeight = (maxaHeight - $(window).scrollTop());
            //     var subMenu = th.find('.treeview-menu').height();
            //     if (windowHeight > 340) {
            //         $(".skin-blue .sidebar-menu>li>.treeview-menu").css("margin-top", -subMenu);
            //     } else {
            //         $(".skin-blue .sidebar-menu>li>.treeview-menu").css("margin-top", -41);
            //     }
            // }
            $(".skin-blue .sidebar-menu .treeview-menu .people").click(function () {
                // $('.skin-blue .treeview-menu>li.active>a').css('display','none');
                 $('.treeview-menu .treeview-sub-menu').css('margin-top', -172);
            });     
             $(".skin-blue .sidebar-menu .treeview .treeview-menu .fa-arrow-left").click(function () {
                $('.treeview-menu .treeview-sub-menu').css('margin-top', -41);
                 $(".skin-blue .sidebar-menu>li>.treeview-menu").css('margin-top', -252);
            });  
            $(document).ready(function(){
                $(".cd-dropdown-wrapper .cd-dropdown-trigger").click(function(){                   
                  $(".backdrop").toggleClass("Overlay_menu");
                  //$(".cd-dropdown-wrapper").;
                });
             });
            var list_item_master = $(".list_item1, .list_item2, .list_item3, .list_item4, .list_item5, .list_item6");
            var list_item_master1 = $(".list_item2, .list_item3, .list_item4, .list_item5, .list_item6");
            var list_item_master2 = $(".list_item1, .list_item3, .list_item4, .list_item5, .list_item6");
            var list_item_master3 = $(".list_item1, .list_item2, .list_item4, .list_item5, .list_item6");
            var list_item_master4 = $(".list_item1, .list_item2, .list_item3, .list_item5, .list_item6");
            var list_item_master5 = $(".list_item1, .list_item2, .list_item3, .list_item4, .list_item6");
            var list_item_master6 = $(".list_item1, .list_item2, .list_item3, .list_item4, .list_item5");
            $(".sidebar-menu li>a>.fa-arrow-left").click(function (event) {
                $(".treeview-sub-menu").hide();
                $(list_item_master).show();
                event.preventDefault();
                return false;
            });
            $(".list_item1").click(function () {
                $(this).show();
                $(list_item_master1).hide();
            }
            );
            $(".list_item2").click(function () {
                $(this).show();
                $(list_item_master2).hide();
            }
            );
            $(".list_item3").click(function () {
                $(this).show();
                $(list_item_master3).hide();
            }
            );
            $(".list_item4").click(function () {
                $(this).show();
                $(list_item_master4).hide();
            }
            );
            $(".list_item5").click(function () {
                $(this).show();
                $(list_item_master5).hide();
            }
            );
            $(".list_item6").click(function () {
                $(this).show();
                $(list_item_master6).hide();
            }
            );
        </script>

<script>
    $(function () {
        $("#compose-textarea").wysihtml5();
    });
    $('[data-toggle="modal"]').attr({
        'data-backdrop' : 'static',
        'data-keyboard' : false                       
    });    
    window.stackBottomRight = {
      'dir1': 'up',
      'dir2': 'left',
      'firstpos1': 25,
      'firstpos2': 25
    };
    var alert_d = {
        title : 'Aodry',
        text : '',
        delay : 2500,
       stack: window.stackBottomRight,
         modules: {
            Animate: {
              animate: true,
              inClass: 'zoomInLeft',
              outClass: 'zoomOutRight'
            }
        }
    };

    function getFloatNumber(str){ 
        str = (str != '' ? parseFloat(str.replace(/,/g,'')) : 0);
        return str;
    }
</script>
<!-- datatable plugins for exporting reports -->
<!-- Page script -->
<?php $this->load->view('sales/tds_tcs_modal'); ?>
<script>
    $(document).ready(function () {
        $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
                $(this).closest(".select2-container").siblings('select:enabled').select2('open');
            });
        $('select.select2').on('select2:closing', function (e) {
            $(e.target).data("select2").$selection.one('focus focusin', function (e) {
                    e.stopPropagation();
            });
        });
    });
    $(function () {
        var $select2 = $('.select2').select2({
            containerCssClass: "wrap"
        });
        $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        $("#datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
        $("[data-mask]").inputmask();
        $('#reservation').daterangepicker();
        $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
        $('#daterange-btn').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        },
                function (start, end) {
                    $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                });

        $(document).ready(function () {
            var from_date = $('#financial_year_from_footer').val();
            var to_date = $('#financial_year_to_footer').val();
            $('.datepicker').datepicker({
                autoclose: true,
                /*format: "yyyy-mm-dd",*/
                format: "dd-mm-yyyy",
                todayHighlight: false,
                orientation: "auto",
                todayBtn: false,
                todayHighlight: false,
                // startDate: from_date + '-04-01',
                // endDate: to_date + '-03-31',
            });
        });
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
            checkboxClass: 'icheckbox_minimal-red',
            radioClass: 'iradio_minimal-red'
        });
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });
        $(".my-colorpicker1").colorpicker();
        $(".my-colorpicker2").colorpicker();
    });
    $(document).ready(function () {
        $('#index, #log_datatable').dataTable({
            "aLengthMenu": [
                [-1, 50, 100, 150],
                ["All", 50, 100, 150]
            ]
        });
    });
    $(document).ready(function () {
        $("#get_password").click(function () {
            var f_y_password = $("#f_y_password").val();
            $.ajax({
                type: "post",
                url: "<?= base_url('financialyear') ?>",
                data: {f_y_password: f_y_password},
                success: function (data) {
                    if (data == "sucess")
                    {
                        $("#financial_year_view").show();
                        $("#submit_f_y").hide();
                        $("#financial_year_model").hide();
                        $(".modal-backdrop ").hide();
                        setTimeout(function () {
                            location.reload();
                        });
                    } else if (data == "change")
                    {
                        $(".text-danger").text('Please Set your password in Company settings');
                    } else
                    {
                        $(".text-danger").text('Wrong Password');
                    }
                }
            });
        })
        var s_count = $('#s_pending').text();
        var p_count = $('#p_pending').text();
        var e_pending = $('#e_pending').text();
        var total = parseInt(s_count, 10) + parseInt(p_count, 10) + parseInt(e_pending, 10);
        $('#n_total').text(total);

    })
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".add_style_image").click(function(){
            $(".main-sidebar").addClass("add_style_logo");
            $(".content-wrapper").addClass("add_atyle_body");
        });
    });
</script>

<script src="<?php echo base_url('assets/js/') ?>common.js"></script>
<script src="<?php echo base_url('assets/js/modules/') ?>settings.js"></script>
<script>
    $(document).ready(function () {
        $('.loader').hide();
        do_something();
        function do_something()
        {
            $(".loader").show();
            $('.loader').fadeOut();
        }
        /*setTimeout(function(){
            window.location.href = '<?=base_url();?>auth/logout';
        },300000);*/
    });
    $(document).keyup(function (e) {
        if (e.keyCode === 27) {
            $(".modal").modal("hide");
        }
        });   
        $(document).on('focus', ':input', function () {            
            $(this).attr('autocomplete', 'off');
        });
        $(document).on('click', '.close', function (event) {
            var modal_class = $('.modal').hasClass('in');     
            if (modal_class) {
                $('body').css('position', 'fixed');
            }
            else{
                $('body').css('position', 'relative');
            }
    });
</script>
<script>
    <?php
    if($this->session->flashdata('email_send') == 'success') {
    ?>
        alert_d.text = 'Email has been send with the attachment';
        PNotify.success(alert_d);
    <?php
    } ?> 
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".add_style_image").click(function(){
            $(".main-sidebar").addClass("add_style_logo");
            $(".main_body").addClass("add_atyle_body");
        });         
    });
</script>
</body>
</html>