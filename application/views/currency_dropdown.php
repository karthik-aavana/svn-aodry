<select class="form-control select2" id="currency_id" name="currency_id">
    <?php
    foreach ($currency as $key => $value) {
        if ($value->currency_id == $this->session->userdata('SESS_DEFAULT_CURRENCY')) {
            echo "<option value='" . $value->currency_id . "' selected>" . $value->currency_name.' - ' . $value->country_shortname."</option>";
        } else {
            echo "<option value='" . $value->currency_id . "'>" . $value->currency_name .' - ' . $value->country_shortname. "</option>";
        }
    }
    ?>
</select>