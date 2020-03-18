<?php
defined('BASEPATH') OR exit('No direct script access allowed');
function in_array_results($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_results($needle, $item, $strict))) {
            return true;
        }
    }
    return false;
}
$b = $this->session->userdata('type');
if (!in_array_results('admin', $b) || in_array_results('manager', $b)) {
    redirect('auth');
}
$this->load->view('layout/header');
?>
<script type="text/javascript">
    function delete_id(id)
    {
        if (confirm('Sure To Remove This Template ?'))
        {
            window.location.href = '<?php echo base_url('template/delete/'); ?>' + id;
        }
    }
</script>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> <!-- Dashboard --> <?php echo $this->lang->line('header_dashboard'); ?></a></li>
                <li class="active">Templates <!-- <?php echo $this->lang->line('branch_label'); ?> --></li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">List Templates</h3>
                        <a class="btn btn-sm btn-info pull-right" href="<?php echo base_url('template/add'); ?>" title="Add Template" onclick="">Add Template <!-- <?php echo $this->lang->line('branch_label_newbranch'); ?> --></a>
                    </div>
                    <div class="box-body">
                        <table id="log_datatable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Hash Tag</th>
                                    <th>Title</th>
                                    <th>Content</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($data as $row) {
                                    $id = $row->id;
                                    $id = $this->encryption_url->encode($id);
                                    ?>
                                    <tr>
                                        <td><?php echo $row->hash_tag; ?></td>
                                        <td><?php echo $row->title; ?></td>
                                        <td><?php
                                            $row->content = str_replace(array(
                                                "\r\n",
                                                "\\r\\n"), " <br>", $row->content);
                                            echo $row->content;
                                            ?></td>
                                        <td>
                                            <!-- <a href="" title="View Details" class="btn btn-xs btn-warning"><span class="fa fa-eye"></span></a> -->
                                            <a href="<?php echo base_url('template/edit/'); ?><?php echo $id; ?>" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>
                                            <a href="javascript:delete_id(<?php echo $id; ?>)" title="Delete" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>
                                        </td>
                                        <?php
                                    }
                                    ?>
                                </tr>
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
?>
