<section class="content mt-50">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div id="plus_btn">
                    <div class="box-header with-border">
                        <h3 class="box-title">File Manager</h3><br>
                        <button href="<?php echo $base_url_directorty; ?>" data-toggle="tooltip" title="Click" class="btn-link bread_crumb"><?php echo 'FileManager' . ' > ' ?></button>
                        <?php
                        if ($directory != '') {
                            $directory_array = explode('/', urldecode($directory));
                            $url = '';
                            foreach ($directory_array as $key => $value) {
                                $url .= $value;
                                ?>
                                <button href="<?php echo $base_url_directorty . $url; ?>" data-toggle="tooltip" title="Click" class="btn-link bread_crumb"><?php echo $value . ' > ' ?></button>
                                <?php
                                $url .= '%2f';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div id="filter">
                </div>
                <div class="box-body">
                    <div class="">
                        <div id="msg"></div>
                        <div class="row">
                            <div class="col-sm-5">
                                <?php if ($directory != '') { ?>
                                    <button href="<?php echo $parent; ?>" data-toggle="tooltip" title="<?php echo $button_parent; ?>" id="button-parent" class="btn btn-primary"><i class="fa fa-level-up"></i></button> 
                                <?php } ?>
                                <button href="<?php echo $refresh; ?>" data-toggle="tooltip" title="<?php echo $button_refresh; ?>" id="button-refresh" class="btn btn-primary"><i class="fa fa-refresh"></i></button>
                                <button type="button" data-toggle="tooltip" title="<?php echo $button_upload; ?>" id="button-upload" class="btn btn-primary"><i class="fa fa-upload"></i></button>
                                <button type="button" data-toggle="tooltip" title="<?php echo $button_folder; ?>" id="button-folder" class="btn btn-primary"><i class="fa fa-folder"></i></button>
                                <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" id="button_delete" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
                            </div>
                            <div class="col-sm-3 pull-right">
                                <div class="input-group">
                                    <input type="text" name="search" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_search; ?>" class="form-control">
                                    <span class="input-group-btn">
                                        <button style="border: 1px solid #012b72;" type="button" data-toggle="tooltip" title="<?php echo $button_search; ?>" id="button-search" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <?php foreach (array_chunk($images, 4) as $image) { ?>
                            <div class="row">
                                <?php foreach ($image as $image) { ?>
                                    <div class="col-sm-3 col-xs-6 text-center">
                                        <?php if ($image['type'] == 'directory') { ?>
                                            <div class="text-center"><a href="<?php echo $image['href']; ?>" class="directory" style="vertical-align: middle;"><i class="fa fa-folder fa-5x"></i></a></div>
                                            <!-- <label>
                                        <input type="checkbox" name="path[]" value="<?php echo $image['path']; ?>" />
                                            <?php echo $image['name']; ?></label> -->
                                        <?php } elseif (strpos($image['type'], 'image') !== false) { ?>
                                            <a href="<?php echo $image['href']; ?>" class="thumbnail" target="_blank"><img src="<?php echo $image['thumb']; ?>" alt="<?php echo $image['name']; ?>" title="<?php echo $image['name']; ?>" /></a>
                                        <?php } elseif (strpos($image['type'], 'pdf') !== false) { ?>
                                            <a href="<?php echo $image['href']; ?>" class="thumbnail" title="<?php echo $image['name']; ?>" target="_blank"><img src="<?= base_url(); ?>assets/images/pdf_thumb.jpg" alt="<?php echo $image['name']; ?>" width="100" height="100"/></a>
                                        <?php } elseif (strpos($image['type'], 'msword') !== false || strpos($image['type'], 'wordprocessingml.document') !== false) { ?>
                                            <a href="<?php echo $image['href']; ?>" class="thumbnail" title="<?php echo $image['name']; ?>" target="_blank"><img src="<?= base_url(); ?>assets/images/doc_thumb.jpg" alt="<?php echo $image['name']; ?>" width="100" height="100"/></a>
                                        <?php } elseif (strpos($image['type'], 'plain') !== false || strpos($image['type'], 'vnd.ms-excel') !== false || strpos($image['type'], 'vnd.ms-office') !== false || strpos($image['type'], 'vnd.openxmlformats-') !== false) { ?>
                                            <a href="<?php echo $image['href']; ?>" class="thumbnail" title="<?php echo $image['name']; ?>" target="_blank"><img src="<?= base_url(); ?>assets/images/csv_thumb.jpg" alt="<?php echo $image['name']; ?>" width="100" height="100"/></a>
                                        <?php } elseif (strpos($image['type'], 'xml') !== false) { ?>
                                            <a href="<?php echo $image['href']; ?>" class="thumbnail" title="<?php echo $image['name']; ?>" target="_blank"><img src="<?= base_url(); ?>assets/images/xml_thumb.jpg" alt="<?php echo $image['name']; ?>" width="100" height="100"/></a>
                                        <?php } ?>
                                        <?php
                                        $image_name = preg_replace('!\s+!', '', trim($image['name']));
                                        if (!in_array($image_name, $file_exit)) {
                                            ?>
                                            <label>
                                                <input type="checkbox" name="path[]" value="<?php echo $image['path']; ?>" />
                                            <?php } ?>
                                            <?php echo $image['name']; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <br/>
                        <?php } ?>
                    </div>
                    <div class="modal-footer"><?php echo $pagination; ?></div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    dir = '<?php echo $directory; ?>';
</script>