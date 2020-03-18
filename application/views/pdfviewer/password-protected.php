<div class="container">
    <div class="row">
        <form class="password-protected" action="<?= base_url() ?>testxls/passwordprotected" method="post">
            <div class="form-group">
                <label for="pwd">Password:</label>
                <input type="password" class="form-control pwd"  placeholder="Enter password" name="pwd" required>
            </div>
            <input type="hidden" name="fileid" value="<?= $fileid; ?>"
                   </div>
            <button type="submit" class="btn btn-success"> Submit</button>
            <p class="" style="color:red;"><?= isset($errormsg) ? $errormsg : ""; ?></p>
        </form>
    </div>
</div>
