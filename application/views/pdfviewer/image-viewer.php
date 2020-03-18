<style type="text/css">
    .fill {
        min-height: 100%;
        height: 100%;
        overflow-y: scroll;
    }
    .img-thumbnail{
        margin: 5px 0px;
    }
    .img-responsive{
        margin: 5px 0px 10px 5px;
    }
    .topnav{
        position: fixed;
        top:0;
        height: 50px;
        width: 100%;

        z-index: 10000;
    }
    .fulimageviewer{
        position: relative;
    }
    .imglist{
        padding-top: 60px;
    }
    img.page{
    }
    .imglist > div{
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 100px;
    }
    a.btn {border-radius: .25rem; border: 1px solid transparent; padding: .5rem 1rem; color: #fff; }
    a.btn:hover { border-radius: .25rem; }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2 col-md-2 fill">
            <?php
            if (isset($files) && isset($url))
            {
                foreach ($files as $file)
                {
                    $imgsrc = $url . $file;
                    ?>
                    <img src="<?= $imgsrc; ?>" class="img-thumbnail" />
                    <?php
                }
            }
            ?>
        </div>
        <div class="col-lg-10 col-md-10 fulimageviewer">
            <div class="row topnav">
                <div class="col-lg-7 col-md-7">
                    <a href="<?= base_url() ?>bank_statement/detecttable/<?= $fileid; ?>" role="button" class="btn btn-primary btn-mini autodetect_bank">Auto Detect Table</a>
                    <span class="validation-color" style="color: red;" id="err_statement">Please Select the statements with the header otherwise it will not upload properly.</span>
                </div>
                <div class="col-lg-5 col-md-5 text-center">
                    <a href="<?= base_url() ?>bank_statement/savedata/<?= $fileid; ?>" role="button" class="btn btn-primary btn-mini savedata">Save Data</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 imglist">
                    <?php
                    if (isset($files) && isset($url))
                    {
                        $sno = 1;
                        foreach ($files as $file)
                        {
                            $imgsrc = $url . $file;
                            ?>
                            <img id="page_<?= $sno; ?>" src="<?= $imgsrc; ?>" class="page" data-pg="<?= $sno; ?>" />
                            <?php
                            $sno++;
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var dataviewurl = "<?= base_url() ?>bank_statement/showdata_bank/<?= $fileid; ?>";
</script>
