<?php
class Common_api{
    public function __construct(){
        //  $this->CI = get_instance();
        $this->ci = &get_instance();
    }

    public function branch_field($branch_id){
        $string = "f.*,br.*,con.country_name as branch_country_name,sta.state_name as branch_state_name,sta.state_code as branch_state_code,cit.city_name as branch_city_name";
        $table  = "branch br";
        $where  = array(
            'br.delete_status' => 0);
        $join = [
            "common_settings com" => "com.branch_id = br.branch_id" . "#" . "left",
            "firm f"              => "f.firm_id = br.firm_id",
            "countries con"       => "br.branch_country_id = con.country_id",
            "states sta"          => "br.branch_state_id = sta.state_id" . "#" . "left",
            "cities cit"          => "br.branch_city_id = cit.city_id" . "#" . "left"];
        
        $where['br.branch_id'] = $branch_id;
        
        $order = [
            "br.branch_id" => "asc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
    }

    public function country_field($country_id = ""){
        $string = "*";
        $table  = "countries";
        $where  = array();
        if ($country_id != "")
        {
            $where = array(
                'country_id' => $country_id);
        }
        $where['delete_status'] = 0;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }

    public function state_field($country_id = "", $state_id = ""){
        $string = "*";
        $table  = "states";
        $where  = array();
        if ($country_id != "")
        {
            $where = array(
                'country_id' => $country_id);
        }
        if ($state_id != "")
        {
            $where['state_id'] = $state_id;
        }
        $where['delete_status'] = 0;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }

    public function city_field($state_id = "", $city_id = ""){
        $string = "*";
        $table  = "cities";
        $where  = array();
        if ($state_id != "") $where['state_id'] = $state_id;
        
        if ($city_id != "") $where['city_id'] = $city_id;
        
        $where['delete_status'] = 0;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }

    public function module_field($branch_id)
    {
        $string = "am.*,ua.add_privilege,ua.edit_privilege,ua.view_privilege,
                   ua.delete_privilege";
        $table = "active_modules am";
        $join  = [
            'user_accessibility ua' => 'ua.module_id = am.module_id and ua.user_id =' . $this->ci->session->userdata('SESS_USER_ID') . '#' . 'left'];
        $where = array(
            'am.delete_status' => 0,
            'am.branch_id'     => $branch_id,
            'ua.delete_status' => 0);
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }

    public function sub_module_field($module_id = "", $branch_id = "")
    {
        $string = "active_sub_modules.*";
        $table  = "active_sub_modules";
        $where  = array(
            'active_sub_modules.delete_status' => 0
        );
        if ($module_id != "")
        {
            $where['active_sub_modules.module_id'] = $module_id;
        }
        if ($branch_id != "")
        {
            $where['active_sub_modules.branch_id'] = $branch_id;
        }
        else
        {
            $where['active_sub_modules.branch_id'] = $branch_id;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }

    public function settings_field($module_id, $branch_id = "")
    {
        $string = "s.*";
        $table  = "settings s";
        $where  = array(
            's.delete_status' => 0,
            's.module_id'     => $module_id
        );
        if ($branch_id != "")
        {
            $where['s.branch_id'] = $branch_id;
        }
        else
        {
            $where['s.branch_id'] = $branch_id;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }

    public function common_settings_field($branch_id = "")
    {
        $string = "common_settings.*";
        $table  = "common_settings";
        $where  = array(
            'common_settings.delete_status' => 0);
        if ($branch_id != "")
        {
            $where['common_settings.branch_id'] = $branch_id;
        }
        else
        {
            $where['common_settings.branch_id'] = $branch_id;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }

    public function product_field($branch_id='',$product_id = ""){
        $string = "p.product_id,p.product_code,p.product_hsn_sac_code,p.product_name,p.product_price,p.product_quantity,p.product_damaged_quantity,p.product_gst_id as product_tax_id,p.product_gst_value as product_tax_value,p.product_tds_id,p.product_tds_value,p.product_details,p.ledger_id,td.tax_name as module_type,p.product_batch,p. product_quantity";
        $table = "products p";
        $join  = [
            'tax td' => 'td.tax_id = p.product_tds_id' . '#' . 'left'];
        $where = array(
            'p.delete_status' => 0,
            'p.branch_id'     => $branch_id);
        if ($product_id != "")
        {
            $where['p.product_id'] = $product_id;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'join'   => $join,
            'where'  => $where,
            'order'  => ""
        );
        return $data;
    }

    public function GetBranchDetails($post){
       
        $resp = array();
        if(@$post['Method']){
            $method = $post['Method'];
            if(@$post['branch']){
                $branch = $post['branch'];
                if(@$branch['User'] && @$branch['Password'] && @$branch['Code']){
                    $branch_code = $branch['Code'];
                    if ($this->ci->ion_auth->login($branch['Code'], $branch['User'], base64_decode($branch['Password']),'0')) {

                        $query = $this->ci->db->select('email,first_name,last_name, id,branch_id,branch_code, password, active, last_login')->where([
                            'email' => $branch['User'],
                            'branch_code' => $branch['Code'] ])->where('username !=', 'superadmin')->limit(1)->order_by('id', 'desc')->get('users');
                        if($query->num_rows() > 0){
                            $branch_detail = $query->result();
                            $this->branch_id = $branch_detail[0]->branch_id;
                            $data = $this->get_default_country_state($this->branch_id);
                            $branch_data = $this->ci->db->select('users.id as user_id,branch.branch_id,branch.financial_year_id,concat(YEAR(tbl_financial_year.from_date),"-",YEAR(tbl_financial_year.to_date)) as financial_year_title,branch.branch_default_currency,currency.currency_symbol,currency.currency_code,currency.currency_text')->from('users')->join('branch', 'users.branch_id = branch.branch_id')->join('currency', 'currency.currency_id = branch.branch_default_currency')->join('tbl_financial_year', 'tbl_financial_year.year_id = branch.financial_year_id')->where('users.id', $branch_detail[0]->id)->where('username !=', 'superadmin')->get()->row();
                                
                            $this->user_id = $branch_data->user_id;
                            
                            $this->ci->session->set_userdata('SESS_BRANCH_ID',trim($branch_detail[0]->branch_id));
                            $this->ci->session->set_userdata('SESS_USER_ID',trim($branch_data->user_id));
                            $this->ci->session->set_userdata('SESS_FINANCIAL_YEAR_TITLE',trim($branch_data->financial_year_title));
                            $this->ci->session->set_userdata('SESS_FINANCIAL_YEAR_ID',trim($branch_data->financial_year_id));
                            $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY_TEXT',trim($branch_data->currency_text));
                            $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY_CODE',trim($branch_data->currency_code));
                            $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY',trim($branch_data->branch_default_currency));
                            $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY_SYMBOL',trim($branch_data->currency_symbol));

                            $this->SESS_FINANCIAL_YEAR_TITLE = trim($branch_data->financial_year_title);
                            $this->SESS_FINANCIAL_YEAR_ID = $branch_data->financial_year_id;
                            $this->SESS_DEFAULT_CURRENCY_TEXT = $branch_data->currency_text;
                            $this->SESS_DEFAULT_CURRENCY_CODE = $branch_data->currency_code;
                            $this->SESS_DEFAULT_CURRENCY = $branch_data->branch_default_currency;
                            $this->SESS_DEFAULT_CURRENCY_SYMBOL = $branch_data->currency_symbol;
                            $resp['modules'] = $this->get_modules($branch_data);
                        }else{
                            $resp['status'] = 404;
                            $resp['message'] = 'Invalid branch detail.';
                        }
                    }else{
                        $resp['status'] = 404;
                        $resp['message'] = 'Invalid branch detail.';
                    }
                }else{
                    $resp['status'] = 404;
                    $resp['message'] = 'User details required!';
                }
            }else{
                $resp['status'] = 404;
                $resp['message'] = 'Branch details required';
            }
        }else{
            $resp['status'] = 404;
            $resp['message'] = 'Method not defined!';
        }
        return $resp;
    }

    public function get_default_country_state() {
        $branch_data  = $this->branch_field($this->ci->branch_id);
        
        $branch       = $this->ci->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data = $this->country_field();
        $country      = $this->ci->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data   = $this->state_field($branch[0]->branch_country_id);
        $state        = $this->ci->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data    = $this->city_field($branch[0]->branch_state_id);
        $city         = $this->ci->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $data         = array('branch' => $branch, 'country' => $country, 'state' => $state,
            'city'                         => $city);
        return $data;
    }

    public function get_modules($branch_data){
        $sess_branch_id         = $branch_data->branch_id;
        $sess_financial_year_id = $branch_data->financial_year_id;
        $sess_user_id           = $branch_data->user_id;
        $sess_default_currency  = $branch_data->branch_default_currency;
        
        if (empty($sess_branch_id) || empty($sess_financial_year_id) || empty($sess_user_id) || empty($sess_default_currency)){
            return false;
        }

        $module_data = $this->module_field($sess_branch_id);
        $module      = $this->ci->general_model->getJoinRecords($module_data['string'], $module_data['table'], $module_data['where'], $module_data['join']);
        $settings = $active_modules = $sub_module= $active_view = $active_edit = $active_delete = $active_add = array();
        foreach ($module as $key => $value)
        {

            if (isset($value->module_id) && $value->module_id != "")
            {
                $active_modules[$key] = $value->module_id;

                if ($value->view_privilege == "yes")
                {
                    $active_view[$key] = $value->module_id;
                }

                if ($value->edit_privilege == "yes")
                {
                    $active_edit[$key] = $value->module_id;
                }

                if ($value->delete_privilege == "yes")
                {
                    $active_delete[$key] = $value->module_id;
                }

                if ($value->add_privilege == "yes")
                {
                    $active_add[$key] = $value->module_id;
                }

                $sub_module_data               = $this->sub_module_field($value->module_id,$sess_branch_id);
                $sub_module[$value->module_id] = $this->ci->general_model->getRecords($sub_module_data['string'], $sub_module_data['table'], $sub_module_data['where']);
                $settings_data                 = $this->settings_field($value->module_id,$sess_branch_id);
                $settings[$value->module_id]   = $this->ci->general_model->getRecords($settings_data['string'], $settings_data['table'], $settings_data['where']);
            }
        }

        $common_settings_data = $this->common_settings_field($sess_branch_id);
        $common_settings      = $this->ci->general_model->getRecords($common_settings_data['string'], $common_settings_data['table'], $common_settings_data['where']);
        $data                 = array(       
            'user_active_modules' => $active_modules,
            'user_active_add'     => $active_add,
            'user_active_edit'    => $active_edit,
            'user_active_view'    => $active_view,
            'user_active_delete'  => $active_delete,
            'modules'             => $module,
            'sub_modules'         => $sub_module,
            'common_settings'     => $common_settings,
            'settings'            => $settings
        );
        return $data;
    }

    public function get_section_modules($module_id, $modules, $privilege,$redirect =''){

        if (in_array($module_id, $modules['user_active_modules'])){
            $check_value = "";

            if ($privilege == "add_privilege")
            {

                if (in_array($module_id, $modules['user_active_add']))
                {
                    $check_value = "yes";
                }

            }
            elseif ($privilege == "edit_privilege")
            {

                if (in_array($module_id, $modules['user_active_edit']))
                {
                    $check_value = "yes";
                }

            }
            elseif ($privilege == "delete_privilege")
            {

                if (in_array($module_id, $modules['user_active_delete']))
                {
                    $check_value = "yes";
                }

            }
            elseif ($privilege == "view_privilege")
            {

                if (in_array($module_id, $modules['user_active_view']))
                {
                    $check_value = "yes";
                }

            }
           
            if ($check_value != "yes" && $redirect != 'unauthorized')
            {
                return false;
            }

            $check_sub_modules = $this->check_sub_modules($module_id, $modules['sub_modules'],$this->ci->session->userdata('SESS_BRANCH_ID'));
            $check_settings    = $this->check_settings($module_id, $modules['settings'],$this->ci->session->userdata('SESS_BRANCH_ID'));

            // $data = array(

            //         'modules'     => $modules,

            //         'sub_modules' => $check_sub_modules,

            //         'settings'    => $check_settings,
            // );
            $data                           = array();
            $data['access_sub_modules']     = $check_sub_modules;
            $data['access_settings']        = $check_settings;
            $data['access_common_settings'] = $modules['common_settings'];

            // echo "<pre>";

            // print_r($check_sub_modules);

            // exit;
            //      $data['access_modules']     = $modules;
            $data['active_modules'] = $modules['user_active_modules'];
            $data['active_add']     = $modules['user_active_add'];
            $data['active_edit']    = $modules['user_active_edit'];
            $data['active_view']    = $modules['user_active_view'];
            $data['active_delete']  = $modules['user_active_delete'];
            
            return $data;
        }else{
            redirect('auth/unauthorized', 'refresh');
        }
    }

    public function check_sub_modules($module_id, $sub_modules){
        $data = array();

        foreach ($sub_modules[$module_id] as $key => $value)
        {

            if ($module_id == $value->module_id && $value->branch_id == $this->ci->session->userdata('SESS_BRANCH_ID'))
            {
                $data[] = $value->sub_module_id;
            }

        }
        return $data;
    }

    public function check_settings($module_id, $sub_modules ){
        $data = array();

        foreach ($sub_modules[$module_id] as $key => $value)
        {

            if ($module_id == $value->module_id && $value->branch_id == $this->ci->session->userdata('SESS_BRANCH_ID'))
            {
                $data[] = $value;
            }

        }
        return $data;
    }

    public function generate_invoice_number($th,$access_settings, $primary_id, $table_name, $date_field_name, $current_date, $option = ""){
       
        $financial_year = explode('-', $this->ci->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));
        if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1]))
        {

            if ($option == "1")
            {
                $current_date = $current_date;
            }
            else
            {
                $current_date = $financial_year[0] . '-04-01';
            }

        }
        else
        {
            $current_date = $current_date;
        }

        $first_prefix       = $access_settings[0]->settings_invoice_first_prefix;
        $last_prefix        = $access_settings[0]->settings_invoice_last_prefix;
        $invoice_type       = $access_settings[0]->invoice_type;
        $invoice_creation   = $access_settings[0]->invoice_creation;
        $invoice_seperation = $access_settings[0]->invoice_seperation;
        $count_condition    = "";

        if ($invoice_creation == "automatic")
        {

            if ($invoice_type == "monthly")
            {
                $mont            = date('m', strtotime($current_date));
                $count_condition = 'month(' . $date_field_name . ') => ' . $mont;
            }
            else
            if ($invoice_type == "yearly")
            {
                $yea             = date('Y', strtotime($current_date));
                $count_condition = 'year(' . $date_field_name . ') => ' . $yea;
            }
        }

        $invoice_count_data = $this->ci->common->invoice_count_field($primary_id, $table_name, $count_condition, $invoice_type);
        $invoice_count = $this->ci->general_model->getRecords($invoice_count_data['string'], $invoice_count_data['table'], $invoice_count_data['where'], $invoice_count_data['order']);
        
        $count         = $invoice_count[0]->invoice_count;
        $invoice_count = sprintf('%03d', intval($count) + 1);
        $month         = explode("-", trim($current_date));
        
        if ($invoice_creation == "automatic")
        {
            $year_prefix = $month[0];

            if ($last_prefix == "month_with_number")
            {
                $last_prefix = $invoice_seperation . $month[1] . $invoice_seperation;
            }
            else
            if ($last_prefix == "year_with_month")
            {
                $last_prefix = $invoice_seperation . $month[0] . $invoice_seperation . $month[1] . $invoice_seperation;
            }
            else
            {
                $last_prefix = $invoice_seperation;
            }

            if ($invoice_type == "regular")
            {
                $reference_number = $first_prefix . $invoice_seperation . $invoice_count;
            }
            else
            {
                $reference_number = $first_prefix . $last_prefix . $invoice_count;
            }

        }
        else
        {
            $reference_number = $first_prefix . $invoice_count;
        }

        return $reference_number;
    }

    public function generate_reference_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date, $option = "")
    {
        $financial_year = explode('-', $this->SESS_FINANCIAL_YEAR_TITLE);

        if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1]))
        {

            if ($option == "1")
            {
                $current_date = $current_date;
            }
            else
            {
                $current_date = $financial_year[0] . '-04-01';
            }

        }
        else
        {
            $current_date = $current_date;
        }

        $first_prefix       = $access_settings[0]->settings_reference_first_prefix;
        $last_prefix        = $access_settings[0]->settings_invoice_last_prefix;
        $invoice_type       = $access_settings[0]->invoice_type;
        $invoice_creation   = $access_settings[0]->invoice_creation;
        $invoice_seperation = $access_settings[0]->invoice_seperation;
        $count_condition    = "";

        if ($invoice_creation == "automatic")
        {

            if ($invoice_type == "monthly")
            {
                $mont            = date('m', strtotime($current_date));
                $count_condition = 'month(' . $date_field_name . ') => ' . $mont;
            }
            else
            if ($invoice_type == "yearly")
            {
                $yea             = date('Y', strtotime($current_date));
                $count_condition = 'year(' . $date_field_name . ') => ' . $yea;
            }

        }

        $invoice_count_data = $this->ci->common->reference_count_field($primary_id, $table_name, $count_condition, $invoice_type);
        $invoice_count      = $this->ci->general_model->getRecords($invoice_count_data['string'], $invoice_count_data['table'], $invoice_count_data['where'], $invoice_count_data['order']);
        $count              = $invoice_count[0]->invoice_count;
        $invoice_count      = sprintf('%03d', intval($count) + 1);
        $month              = explode("-", trim($current_date));

        if ($invoice_creation == "automatic")
        {
            $year_prefix = $month[0];

            if ($last_prefix == "month_with_number")
            {
                $last_prefix = $invoice_seperation . $month[1] . $invoice_seperation;
            }
            else
            if ($last_prefix == "year_with_month")
            {
                $last_prefix = $invoice_seperation . $month[0] . $invoice_seperation . $month[1] . $invoice_seperation;
            }
            else
            {
                $last_prefix = $invoice_seperation;
            }

            if ($invoice_type == "regular")
            {
                $reference_number = $first_prefix . $invoice_seperation . $invoice_count;
            }
            else
            {
                $reference_number = $first_prefix . $last_prefix . $invoice_count;
            }

        }
        else
        {
            $reference_number = $first_prefix . $invoice_count;
        }

        return $reference_number;
    }
}