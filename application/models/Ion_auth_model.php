<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ion_auth_model extends CI_Model
{

    public $tables               = array();
    public $activation_code;
    public $forgotten_password_code;
    public $new_password;
    public $identity;
    public $_ion_where           = array();
    public $_ion_select          = array();
    public $_ion_like            = array();
    public $_ion_limit           = NULL;
    public $_ion_offset          = NULL;
    public $_ion_order_by        = NULL;
    public $_ion_order           = NULL;
    protected $_ion_hooks;
    protected $response          = NULL;
    protected $messages;
    protected $errors;
    protected $error_start_delimiter;
    protected $error_end_delimiter;
    public $_cache_user_in_group = array();
    protected $_cache_groups     = array();
    protected $db;

    public function __construct()
    {
        $this->config->load('ion_auth', TRUE);
        $this->load->helper('cookie');
        $this->load->helper('date');
        $this->lang->load('ion_auth');
        $this->db              = $this->load->database($this->config->item('database_group_name', 'ion_auth'), TRUE, TRUE);
        $this->tables          = $this->config->item('tables', 'ion_auth');
        $this->identity_column = $this->config->item('identity', 'ion_auth');
        $this->store_salt      = $this->config->item('store_salt', 'ion_auth');
        $this->salt_length     = $this->config->item('salt_length', 'ion_auth');
        $this->join            = $this->config->item('join', 'ion_auth');
        $this->hash_method     = $this->config->item('hash_method', 'ion_auth');
        $this->default_rounds  = $this->config->item('default_rounds', 'ion_auth');
        $this->random_rounds   = $this->config->item('random_rounds', 'ion_auth');
        $this->min_rounds      = $this->config->item('min_rounds', 'ion_auth');
        $this->max_rounds      = $this->config->item('max_rounds', 'ion_auth');
        $this->messages        = array();
        $this->errors          = array();
        $delimiters_source     = $this->config->item('delimiters_source', 'ion_auth');
        if ($delimiters_source === 'form_validation')
        {
            $this->load->library('form_validation');
            $form_validation_class         = new ReflectionClass("CI_Form_validation");
            $error_prefix                  = $form_validation_class->getProperty("_error_prefix");
            $error_prefix->setAccessible(TRUE);
            $this->error_start_delimiter   = $error_prefix->getValue($this->form_validation);
            $this->message_start_delimiter = $this->error_start_delimiter;
            $error_suffix                  = $form_validation_class->getProperty("_error_suffix");
            $error_suffix->setAccessible(TRUE);
            $this->error_end_delimiter     = $error_suffix->getValue($this->form_validation);
            $this->message_end_delimiter   = $this->error_end_delimiter;
        }
        else
        {
            $this->message_start_delimiter = $this->config->item('message_start_delimiter', 'ion_auth');
            $this->message_end_delimiter   = $this->config->item('message_end_delimiter', 'ion_auth');
            $this->error_start_delimiter   = $this->config->item('error_start_delimiter', 'ion_auth');
            $this->error_end_delimiter     = $this->config->item('error_end_delimiter', 'ion_auth');
        } $this->_ion_hooks = new stdClass;
        if ($this->hash_method == 'bcrypt')
        {
            if ($this->random_rounds)
            {
                $rand   = rand($this->min_rounds, $this->max_rounds);
                $params = array(
                        'rounds' => $rand );
            }
            else
            {
                $params = array(
                        'rounds' => $this->default_rounds );
            } $params['salt_prefix'] = $this->config->item('salt_prefix', 'ion_auth');
            $this->load->library('bcrypt', $params);
        } $this->trigger_events('model_constructor');
    }

    public function hash_password($password, $salt = FALSE, $use_sha1_override = FALSE)
    {
        if (empty($password))
        {
            return FALSE;
        } if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt')
        {
            return $this->bcrypt->hash($password);
        } if ($this->store_salt && $salt)
        {
            return sha1($password . $salt);
        }
        else
        {
            $salt = $this->salt();
            return $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
        }
    }

    public function hash_password_db($id, $password, $use_sha1_override = FALSE)
    {
        if (empty($id) || empty($password))
        {
            return FALSE;
        } $this->trigger_events('extra_where');
        $query            = $this->db->select('password, salt')->where('id', $id)->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
        $hash_password_db = $query->row();
        if ($query->num_rows() !== 1)
        {
            return FALSE;
        } if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt')
        {
            if ($this->bcrypt->verify($password, $hash_password_db->password))
            {
                return TRUE;
            } return FALSE;
        } if ($this->store_salt)
        {
            $db_password = sha1($password . $hash_password_db->salt);
        }
        else
        {
            $salt        = substr($hash_password_db->password, 0, $this->salt_length);
            $db_password = $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
        } if ($db_password == $hash_password_db->password)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function hash_code($password)
    {
        return $this->hash_password($password, FALSE, TRUE);
    }

    public function salt()
    {
        $raw_salt_len = 16;
        $buffer       = '';
        $buffer_valid = FALSE;
        if (function_exists('random_bytes'))
        {
            $buffer = random_bytes($raw_salt_len);
            if ($buffer)
            {
                $buffer_valid = TRUE;
            }
        } if (!$buffer_valid && function_exists('mcrypt_create_iv') && !defined('PHALANGER'))
        {
            $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
            if ($buffer)
            {
                $buffer_valid = TRUE;
            }
        } if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes'))
        {
            $buffer = openssl_random_pseudo_bytes($raw_salt_len);
            if ($buffer)
            {
                $buffer_valid = TRUE;
            }
        } if (!$buffer_valid && @is_readable('/dev/urandom'))
        {
            $f    = fopen('/dev/urandom', 'r');
            $read = strlen($buffer);
            while ($read < $raw_salt_len)
            {
                $buffer .= fread($f, $raw_salt_len - $read);
                $read   = strlen($buffer);
            } fclose($f);
            if ($read >= $raw_salt_len)
            {
                $buffer_valid = TRUE;
            }
        } if (!$buffer_valid || strlen($buffer) < $raw_salt_len)
        {
            $bl = strlen($buffer);
            for ($i = 0; $i < $raw_salt_len; $i++)
            {
                if ($i < $bl)
                {
                    $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                }
                else
                {
                    $buffer .= chr(mt_rand(0, 255));
                }
            }
        } $salt            = $buffer;
        $base64_digits   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
        $bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $base64_string   = base64_encode($salt);
        $salt            = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);
        $salt            = substr($salt, 0, $this->salt_length);
        return $salt;
    }

    public function activate($id, $code = FALSE)
    {
        $this->trigger_events('pre_activate');
        if ($code !== FALSE)
        {
            $query = $this->db->select($this->identity_column)->where('activation_code', $code)->where('id', $id)->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
            $query->row();
            if ($query->num_rows() !== 1)
            {
                $this->trigger_events(array(
                        'post_activate',
                        'post_activate_unsuccessful' ));
                $this->set_error('activate_unsuccessful');
                return FALSE;
            } $data = array(
                    'activation_code' => NULL,
                    'active'          => 1 );
            $this->trigger_events('extra_where');
            $this->db->update($this->tables['users'], $data, array(
                    'id' => $id ));
        }
        else
        {
            $data = array(
                    'activation_code' => NULL,
                    'active'          => 1 );
            $this->trigger_events('extra_where');
            $this->db->update($this->tables['users'], $data, array(
                    'id' => $id ));
        } $return = $this->db->affected_rows() == 1;
        if ($return)
        {
            $this->trigger_events(array(
                    'post_activate',
                    'post_activate_successful' ));
            $this->set_message('activate_successful');
        }
        else
        {
            $this->trigger_events(array(
                    'post_activate',
                    'post_activate_unsuccessful' ));
            $this->set_error('activate_unsuccessful');
        } return $return;
    }

    public function deactivate($id = NULL)
    {

        $this->trigger_events('deactivate');
        if (!isset($id))
        {
            $this->set_error('deactivate_unsuccessful');
            return FALSE;
        }
        else if ($this->ion_auth->logged_in() && $this->user()->row()->id == $id)
        {
            $this->set_error('deactivate_current_user_unsuccessful');
            return FALSE;
        }
       
        $activation_code       = sha1(md5(microtime()));
        $this->activation_code = $activation_code;
        $data                  = array(
                'activation_code' => $activation_code,
                'active'          => 0 );
        $this->trigger_events('extra_where');
        $this->db->update($this->tables['users'], $data, array(
                'id' => $id ));
        /*print_r($this->db->last_query());exit();*/
        $return                = $this->db->affected_rows() == 1;
        if ($return)
        {
            $this->set_message('deactivate_successful');
        }
        else
        {
            $this->set_error('deactivate_unsuccessful');
        } return $return;
    }

    public function clear_forgotten_password_code($code)
    {
        if (empty($code))
        {
            return FALSE;
        } $this->db->where('forgotten_password_code', $code);
        if ($this->db->count_all_results($this->tables['users']) > 0)
        {
            $data = array(
                    'forgotten_password_code' => NULL,
                    'forgotten_password_time' => NULL );
            $this->db->update($this->tables['users'], $data, array(
                    'forgotten_password_code' => $code ));
            return TRUE;
        } return FALSE;
    }

    public function reset_password($identity, $new, $branch_id = "")
    {
        $this->trigger_events('pre_change_password');
        if (!$this->identity_check($identity, $branch_id))
        {
            $this->trigger_events(array(
                    'post_change_password',
                    'post_change_password_unsuccessful' ));
            return FALSE;
        } $this->trigger_events('extra_where');
        $query = $this->db->select('id, password, salt')->where($this->identity_column, $identity)->where('username !=', 'superadmin')->where('branch_id', $branch_id)->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
        if ($query->num_rows() !== 1)
        {
            $this->trigger_events(array(
                    'post_change_password',
                    'post_change_password_unsuccessful' ));
            $this->set_error('password_change_unsuccessful');
            return FALSE;
        } $result = $query->row();
        $new    = $this->hash_password($new, $result->salt);
        $data   = array(
                'password'                => $new,
                'remember_code'           => NULL,
                'forgotten_password_code' => NULL,
                'forgotten_password_time' => NULL, );
        $this->trigger_events('extra_where');
        $this->db->update($this->tables['users'], $data, array(
                $this->identity_column => $identity,
                'branch_id'            => $branch_id ));
        $return = $this->db->affected_rows() == 1;

        if ($return)
        {
            $this->trigger_events(array(
                    'post_change_password',
                    'post_change_password_successful' ));
            $this->set_message('password_change_successful');
        }
        else
        {
            $this->trigger_events(array(
                    'post_change_password',
                    'post_change_password_unsuccessful' ));
            $this->set_error('password_change_unsuccessful');
        } return $return;
    }

    public function change_password($identity, $old, $new)
    {
        $this->trigger_events('pre_change_password');
        $this->trigger_events('extra_where');
        $query = $this->db->select('id, password, salt')->where($this->identity_column, $identity)->where('username !=', 'superadmin')->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
        if ($query->num_rows() !== 1)
        {
            $this->trigger_events(array(
                    'post_change_password',
                    'post_change_password_unsuccessful' ));
            $this->set_error('password_change_unsuccessful');
            return FALSE;
        } $user                 = $query->row();
        $old_password_matches = $this->hash_password_db($user->id, $old);
        if ($old_password_matches === TRUE)
        {
            $hashed_new_password                 = $this->hash_password($new, $user->salt);
            $data                                = array(
                    'password'      => $hashed_new_password,
                    'remember_code' => NULL, );
            $this->trigger_events('extra_where');
            $successfully_changed_password_in_db = $this->db->update($this->tables['users'], $data, array(
                    $this->identity_column => $identity ));
            if ($successfully_changed_password_in_db)
            {
                $this->trigger_events(array(
                        'post_change_password',
                        'post_change_password_successful' ));
                $this->set_message('password_change_successful');
            }
            else
            {
                $this->trigger_events(array(
                        'post_change_password',
                        'post_change_password_unsuccessful' ));
                $this->set_error('password_change_unsuccessful');
            } return $successfully_changed_password_in_db;
        } $this->set_error('password_change_unsuccessful');
        return FALSE;
    }

    public function username_check($username = '')
    {
        $this->trigger_events('username_check');
        if (empty($username))
        {
            return FALSE;
        } $this->trigger_events('extra_where');
        return $this->db->where('username', $username)->where('username !=', 'superadmin')->limit(1)->count_all_results($this->tables['users']) > 0;
    }

    public function email_check($email = '')
    {
        $this->trigger_events('email_check');
        if (empty($email))
        {
            return FALSE;
        } $this->trigger_events('extra_where');
        return $this->db->where('email', $email)->where('username !=', 'superadmin')->limit(1)->count_all_results($this->tables['users']) > 0;
    }

    public function identity_check($identity = '', $branch_id = '')
    {
        $this->trigger_events('identity_check');
        if (empty($identity))
        {
            return FALSE;
        }
        if (!empty($branch_id))
        {
            return $this->db->where($this->identity_column, $identity)->where('username !=', 'superadmin')->where('branch_id', $branch_id)->limit(1)->count_all_results($this->tables['users']) > 0;
        }
        return $this->db->where($this->identity_column, $identity)->where('username !=', 'superadmin')->limit(1)->count_all_results($this->tables['users']) > 0;
    }

    public function forgotten_password($identity, $login_code = "")
    {
        if (empty($identity))
        {
            $this->trigger_events(array(
                    'post_forgotten_password',
                    'post_forgotten_password_unsuccessful' ));
            return FALSE;
        } $activation_code_part = "";
        if (function_exists("openssl_random_pseudo_bytes"))
        {
            $activation_code_part = openssl_random_pseudo_bytes(128);
        } for ($i = 0; $i < 1024; $i++)
        {
            $activation_code_part = sha1($activation_code_part . mt_rand() . microtime());
        } $key = $this->hash_code($activation_code_part . $identity);
        if ($key != '' && $this->config->item('permitted_uri_chars') != '' && $this->config->item('enable_query_strings') == FALSE)
        {
            if (!preg_match("|^[" . str_replace(array(
                                    '\\-',
                                    '\-' ), '-', preg_quote($this->config->item('permitted_uri_chars'), '-')) . "]+$|i", $key))
            {
                $key = preg_replace("/[^" . $this->config->item('permitted_uri_chars') . "]+/i", "-", $key);
            }
        } $this->forgotten_password_code = substr($key, 0, 40);
        $this->trigger_events('extra_where');
        $update                        = array(
                'forgotten_password_code' => $key,
                'forgotten_password_time' => time() );
        $this->db->update($this->tables['users'], $update, array(
                $this->identity_column => $identity,
                'branch_code'          => $login_code ));
        $return                        = $this->db->affected_rows() == 1;
        if ($return)
        {
            $this->trigger_events(array(
                    'post_forgotten_password',
                    'post_forgotten_password_successful' ));
        }
        else
        {
            $this->trigger_events(array(
                    'post_forgotten_password',
                    'post_forgotten_password_unsuccessful' ));
        } return $return;
    }

    public function forgotten_password_complete($code, $salt = FALSE)
    {
        $this->trigger_events('pre_forgotten_password_complete');
        if (empty($code))
        {
            $this->trigger_events(array(
                    'post_forgotten_password_complete',
                    'post_forgotten_password_complete_unsuccessful' ));
            return FALSE;
        } $profile = $this->where('forgotten_password_code', $code)->users()->row();
        if ($profile)
        {
            if ($this->config->item('forgot_password_expiration', 'ion_auth') > 0)
            {
                $expiration = $this->config->item('forgot_password_expiration', 'ion_auth');
                if (time() - $profile->forgotten_password_time > $expiration)
                {
                    $this->set_error('forgot_password_expired');
                    $this->trigger_events(array(
                            'post_forgotten_password_complete',
                            'post_forgotten_password_complete_unsuccessful' ));
                    return FALSE;
                }
            } $password = $this->salt();
            $data     = array(
                    'password'                => $this->hash_password($password, $salt),
                    'forgotten_password_code' => NULL,
                    'active'                  => 1, );
            $this->db->update($this->tables['users'], $data, array(
                    'forgotten_password_code' => $code ));
            $this->trigger_events(array(
                    'post_forgotten_password_complete',
                    'post_forgotten_password_complete_successful' ));
            return $password;
        } $this->trigger_events(array(
                'post_forgotten_password_complete',
                'post_forgotten_password_complete_unsuccessful' ));
        return FALSE;
    }

    public function register($branch_id = "", $identity, $password, $email, $additional_data = array(), $groups = array())
    {
        $this->trigger_events('pre_register');
        $manual_activation = $this->config->item('manual_activation', 'ion_auth');
        if ($this->identity_check($identity, $branch_id))
        {
            $this->set_error('account_creation_duplicate_identity');
            return FALSE;
        }
        else if (!$this->config->item('default_group', 'ion_auth') && empty($groups))
        {
            $this->set_error('account_creation_missing_default_group');
            return FALSE;
        } $query = $this->db->get_where($this->tables['groups'], array(
                        'name' => $this->config->item('default_group', 'ion_auth') ), 1)->row();
        if (!isset($query->id) && empty($groups))
        {
            $this->set_error('account_creation_invalid_default_group');
            return FALSE;
        }

        $default_group = $query;
        $ip_address    = $this->_prepare_ip($this->input->ip_address());
        $salt          = $this->store_salt ? $this->salt() : FALSE;
        $password      = $this->hash_password($password, $salt);
        $branch_id     = $branch_id;
        $this->db->select('branch_code');
        $this->db->from('branch');
        $this->db->where('branch_id', $branch_id);
        $this->db->where('delete_status', 0);
        $branch_code   = $this->db->get()->row()->branch_code;
        $data          = array(
                $this->identity_column => $identity,
                'username'             => $identity,
                'password'             => $password,
                'email'                => $email,
                'ip_address'           => $ip_address,
                'branch_id'            => $branch_id,
                'branch_code'          => $branch_code,
                'created_on'           => time(),
                'active'               => ($manual_activation === FALSE ? 1 : 0) );
        if ($this->store_salt)
        {
            $data['salt'] = $salt;
        } $user_data = array_merge($this->_filter_data($this->tables['users'], $additional_data), $data);
        $this->trigger_events('extra_set');

        $this->db->insert($this->tables['users'], $user_data);
        $id = $this->db->insert_id($this->tables['users'] . '_id_seq');
        $this->trigger_events('post_register');
        return (isset($id)) ? $id : FALSE;
    }

    public function login($branch_code, $identity, $password, $remember = FALSE)
    {
        $this->trigger_events('pre_login');
        $password_text = $password;

        if (empty($identity) || empty($password))
        {
            $this->set_error('login_unsuccessful');
            return FALSE;
        }
        $this->trigger_events('extra_where');
        
        $query = $this->db->select($this->identity_column . ', email,first_name,last_name, id,branch_id,branch_code, password, active, last_login')->where([
                        $this->identity_column => $identity,
                        'branch_code'          => $branch_code ])->where('username !=', 'superadmin')->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
        /*print_r($this->db->last_query());
        exit;*/
        if ($this->is_max_login_attempts_exceeded($identity))
        {
            $this->hash_password($password);
            $this->trigger_events('post_login_unsuccessful');
            $this->set_error('login_timeout');
            return FALSE;
        }
        if ($query->num_rows() === 1){
            $user     = $query->row();
            
            $password = $this->hash_password_db($user->id, $password);
            if ($password === TRUE){
                if ($user->active == 0){
                    $this->trigger_events('post_login_unsuccessful');
                    $this->set_error('login_unsuccessful_not_active');
                    return FALSE;
                }

                ########
                /*$this->db->select('*');
                    
                $this->db->from('login_auth');

                $this->db->where(array('status'=> '0' , 'user_id' => $user->id));

                $query = $this->db->get();//echo $this->db->last_query();

                
                if ( $query->num_rows() > 0 )
                {
                    $this->session->set_flashdata('message', 'Someone is already logged in with this credentials.');

                    redirect('auth/login', 'refresh');
                }*/

                ########

                //echo "<pre>"; print_r($user);exit();
                $this->set_session($user);
                $this->update_last_login($user->id);
                $this->clear_login_attempts($identity);
                if ($remember && $this->config->item('remember_users', 'ion_auth'))
                {
                    $this->remember_user($user->id,$branch_code, $password_text);
                } $this->_regenerate_session();
                $this->trigger_events(array(
                        'post_login',
                        'post_login_successful' ));
                $this->set_message('login_successful');
                return TRUE;
            }
        } 
        $this->hash_password($password);
        $this->increase_login_attempts($identity);
        $this->trigger_events('post_login_unsuccessful');
        $this->set_error('login_unsuccessful');
        return FALSE;
    }

    public function super_admin_login($branch_code, $identity, $password, $remember = FALSE)
    {
        $this->trigger_events('pre_login');
        if (empty($identity) || empty($password))
        {
            $this->set_error('login_unsuccessful');
            return FALSE;
        } $this->trigger_events('extra_where');
        $query = $this->db->select($this->identity_column . ', email,first_name,last_name, id,branch_id,branch_code, password, active, last_login,username')->where([
                        $this->identity_column => $identity,
                        'branch_code'          => $branch_code ])->where('username =', 'superadmin')->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
        if ($this->is_max_login_attempts_exceeded($identity))
        {
            $this->hash_password($password);
            $this->trigger_events('post_login_unsuccessful');
            $this->set_error('login_timeout');
            return FALSE;
        } if ($query->num_rows() === 1)
        {
            $user     = $query->row();
            $password = $this->hash_password_db($user->id, $password);
            if ($password === TRUE)
            {
                if ($user->active == 0)
                {
                    $this->trigger_events('post_login_unsuccessful');
                    $this->set_error('login_unsuccessful_not_active');
                    return FALSE;
                } if ($user->username != "superadmin")
                {
                    $this->trigger_events('post_login_unsuccessful');
                    $this->set_error('login_unsuccessful_not_active');
                    return FALSE;
                } $this->set_sa_session($user);
                $this->update_last_login($user->id);
                $this->clear_login_attempts($identity);
                if ($remember && $this->config->item('remember_users', 'ion_auth'))
                {
                    $this->remember_user($user->id);
                } $this->_regenerate_session();
                $this->trigger_events(array(
                        'post_login',
                        'post_login_successful' ));
                $this->set_message('login_successful');
                return TRUE;
            }
        } $this->hash_password($password);
        $this->increase_login_attempts($identity);
        $this->trigger_events('post_login_unsuccessful');
        $this->set_error('login_unsuccessful');
        return FALSE;
    }

    public function recheck_session()
    {
        $recheck = (NULL !== $this->config->item('recheck_timer', 'ion_auth')) ? $this->config->item('recheck_timer', 'ion_auth') : 0;
        if ($recheck !== 0)
        {
            $last_login = $this->session->userdata('SESS_LAST_CHECK');
            if ($last_login + $recheck < time())
            {
                $query = $this->db->select('id')->where(array(
                                $this->identity_column => $this->session->userdata('SESS_IDENTITY'),
                                'active'               => '1',
                                'username !='          => 'superadmin' ))->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
                if ($query->num_rows() === 1)
                {
                    $this->session->set_userdata('SESS_LAST_CHECK', time());
                }
                else
                {
                    $this->trigger_events('logout');
                    $identity = $this->config->item('identity', 'ion_auth');
                    if (substr(CI_VERSION, 0, 1) == '2')
                    {
                        $this->session->unset_userdata(array(
                                $identity      => '',
                                'id'           => '',
                                'SESS_USER_ID' => '' ));
                    }
                    else
                    {
                        $this->session->unset_userdata(array(
                                $identity,
                                'id',
                                'SESS_USER_ID' ));
                    } return FALSE;
                }
            }
        } return (bool) $this->session->userdata('SESS_IDENTITY');
    }

    public function is_max_login_attempts_exceeded($identity, $ip_address = NULL)
    {
        if ($this->config->item('track_login_attempts', 'ion_auth'))
        {
            $max_attempts = $this->config->item('maximum_login_attempts', 'ion_auth');
            if ($max_attempts > 0)
            {
                $attempts = $this->get_attempts_num($identity, $ip_address);
                return $attempts >= $max_attempts;
            }
        } return FALSE;
    }

    public function get_attempts_num($identity, $ip_address = NULL)
    {
        if ($this->config->item('track_login_attempts', 'ion_auth'))
        {
            $this->db->select('1', FALSE);
            $this->db->where('login', $identity);
            if ($this->config->item('track_login_ip_address', 'ion_auth'))
            {
                if (!isset($ip_address))
                {
                    $ip_address = $this->_prepare_ip($this->input->ip_address());
                } $this->db->where('ip_address', $ip_address);
            } $this->db->where('time >', time() - $this->config->item('lockout_time', 'ion_auth'), FALSE);
            $qres = $this->db->get($this->tables['login_attempts']);
            return $qres->num_rows();
        } return 0;
    }

    public function is_time_locked_out($identity, $ip_address = NULL)
    {
        return $this->is_max_login_attempts_exceeded($identity, $ip_address);
    }

    public function get_last_attempt_time($identity, $ip_address = NULL)
    {
        if ($this->config->item('track_login_attempts', 'ion_auth'))
        {
            $this->db->select('time');
            $this->db->where('login', $identity);
            if ($this->config->item('track_login_ip_address', 'ion_auth'))
            {
                if (!isset($ip_address))
                {
                    $ip_address = $this->_prepare_ip($this->input->ip_address());
                } $this->db->where('ip_address', $ip_address);
            } $this->db->order_by('id', 'desc');
            $qres = $this->db->get($this->tables['login_attempts'], 1);
            if ($qres->num_rows() > 0)
            {
                return $qres->row()->time;
            }
        } return 0;
    }

    public function get_last_attempt_ip($identity)
    {
        if ($this->config->item('track_login_attempts', 'ion_auth') && $this->config->item('track_login_ip_address', 'ion_auth'))
        {
            $this->db->select('ip_address');
            $this->db->where('login', $identity);
            $this->db->order_by('id', 'desc');
            $qres = $this->db->get($this->tables['login_attempts'], 1);
            if ($qres->num_rows() > 0)
            {
                return $qres->row()->ip_address;
            }
        } return '';
    }

    public function increase_login_attempts($identity)
    {
        if ($this->config->item('track_login_attempts', 'ion_auth'))
        {
            $data = array(
                    'ip_address' => '',
                    'login'      => $identity,
                    'time'       => time() );
            if ($this->config->item('track_login_ip_address', 'ion_auth'))
            {
                $data['ip_address'] = $this->_prepare_ip($this->input->ip_address());
            } return $this->db->insert($this->tables['login_attempts'], $data);
        } return FALSE;
    }

    public function clear_login_attempts($identity, $old_attempts_expire_period = 86400, $ip_address = NULL)
    {
        if ($this->config->item('track_login_attempts', 'ion_auth'))
        {
            $old_attempts_expire_period = max($old_attempts_expire_period, $this->config->item('lockout_time', 'ion_auth'));
            $this->db->where('login', $identity);
            if ($this->config->item('track_login_ip_address', 'ion_auth'))
            {
                if (!isset($ip_address))
                {
                    $ip_address = $this->_prepare_ip($this->input->ip_address());
                } $this->db->where('ip_address', $ip_address);
            } $this->db->or_where('time <', time() - $old_attempts_expire_period, FALSE);
            return $this->db->delete($this->tables['login_attempts']);
        } return FALSE;
    }

    public function limit($limit)
    {
        $this->trigger_events('limit');
        $this->_ion_limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->trigger_events('offset');
        $this->_ion_offset = $offset;
        return $this;
    }

    public function where($where, $value = NULL)
    {
        $this->trigger_events('where');
        if (!is_array($where))
        {
            $where = array(
                    $where => $value );
        } array_push($this->_ion_where, $where);
        return $this;
    }

    public function like($like, $value = NULL, $position = 'both')
    {
        $this->trigger_events('like');
        array_push($this->_ion_like, array(
                'like'     => $like,
                'value'    => $value,
                'position' => $position ));
        return $this;
    }

    public function select($select)
    {
        $this->trigger_events('select');
        $this->_ion_select[] = $select;
        return $this;
    }

    public function order_by($by, $order = 'desc')
    {
        $this->trigger_events('order_by');
        $this->_ion_order_by = $by;
        $this->_ion_order    = $order;
        return $this;
    }

    public function row()
    {
        $this->trigger_events('row');
        $row = $this->response->row();
        return $row;
    }

    public function row_array()
    {
        $this->trigger_events(array(
                'row',
                'row_array' ));
        $row = $this->response->row_array();
        return $row;
    }

    public function result()
    {
        $this->trigger_events('result');
        $result = $this->response->result();
        return $result;
    }

    public function result_array()
    {
        $this->trigger_events(array(
                'result',
                'result_array' ));
        $result = $this->response->result_array();
        return $result;
    }

    public function num_rows()
    {
        $this->trigger_events(array(
                'num_rows' ));
        $result = $this->response->num_rows();
        return $result;
    }

    public function users($groups = NULL)
    {
        $this->trigger_events('users');
        if (isset($this->_ion_select) && !empty($this->_ion_select))
        {
            foreach ($this->_ion_select as $select)
            {
                $this->db->select($select);
            } $this->_ion_select = array();
        }
        else
        {
            $this->db->select(array(
                    $this->tables['users'] . '.*',
                    $this->tables['users'] . '.id as id',
                    $this->tables['users'] . '.id as user_id' ));
        } if (isset($groups))
        {
            if (!is_array($groups))
            {
                $groups = Array(
                        $groups );
            } if (isset($groups) && !empty($groups))
            {
                $this->db->distinct();
                $this->db->join($this->tables['users_groups'], $this->tables['users_groups'] . '.' . $this->join['users'] . '=' . $this->tables['users'] . '.id', 'inner');
            } $group_ids   = array();
            $group_names = array();
            foreach ($groups as $group)
            {
                if (is_numeric($group))
                    $group_ids[]   = $group;
                else
                    $group_names[] = $group;
            } $or_where_in = (!empty($group_ids) && !empty($group_names)) ? 'or_where_in' : 'where_in';
            if (!empty($group_names))
            {
                $this->db->join($this->tables['groups'], $this->tables['users_groups'] . '.' . $this->join['groups'] . ' = ' . $this->tables['groups'] . '.id', 'inner');
                $this->db->where_in($this->tables['groups'] . '.name', $group_names);
            } if (!empty($group_ids))
            {
                $this->db->{$or_where_in}($this->tables['users_groups'] . '.' . $this->join['groups'], $group_ids);
            }
        } $this->trigger_events('extra_where');
        if (isset($this->_ion_where) && !empty($this->_ion_where))
        {
            foreach ($this->_ion_where as $where)
            {
                $this->db->where($where);
            } $this->_ion_where = array();
        } if ($this->session->userdata('SESS_BRANCH_ID') != NULL)
        {
            $this->db->where('branch_id', $this->session->userdata('SESS_BRANCH_ID'));
        } $this->db->where('delete_status', 0);
        if (isset($this->_ion_like) && !empty($this->_ion_like))
        {
            foreach ($this->_ion_like as $like)
            {
                $this->db->or_like($like['like'], $like['value'], $like['position']);
            } $this->_ion_like = array();
        } if (isset($this->_ion_limit) && isset($this->_ion_offset))
        {
            $this->db->limit($this->_ion_limit, $this->_ion_offset);
            $this->_ion_limit  = NULL;
            $this->_ion_offset = NULL;
        }
        else if (isset($this->_ion_limit))
        {
            $this->db->limit($this->_ion_limit);
            $this->_ion_limit = NULL;
        } if (isset($this->_ion_order_by) && isset($this->_ion_order))
        {
            $this->db->order_by($this->_ion_order_by, $this->_ion_order);
            $this->_ion_order    = NULL;
            $this->_ion_order_by = NULL;
        } $this->response = $this->db->get($this->tables['users']);
        return $this;
    }

    public function user($id = NULL)
    {
        $this->trigger_events('user');
        $id = isset($id) ? $id : $this->session->userdata('SESS_USER_ID');
        $this->limit(1);
        $this->order_by($this->tables['users'] . '.id', 'desc');
        $this->where($this->tables['users'] . '.id', $id);
        $this->users();
        return $this;
    }

    public function get_users_groups($id = FALSE)
    {
        $this->trigger_events('get_users_group');
        $id || $id = $this->session->userdata('SESS_USER_ID');
        return $this->db->select($this->tables['users_groups'] . '.' . $this->join['groups'] . ' as id, ' . $this->tables['groups'] . '.name, ' . $this->tables['groups'] . '.description')->where($this->tables['users_groups'] . '.' . $this->join['users'], $id)->join($this->tables['groups'], $this->tables['users_groups'] . '.' . $this->join['groups'] . '=' . $this->tables['groups'] . '.id')->get($this->tables['users_groups']);
    }

    public function add_to_group($group_ids, $user_id = FALSE)
    {
        $this->trigger_events('add_to_group');
        $user_id || $user_id = $this->session->userdata('SESS_USER_ID');
        if (!is_array($group_ids))
        {
            $group_ids = array(
                    $group_ids );
        } $return = 0;
        foreach ($group_ids as $group_id)
        {
            if ($this->db->insert($this->tables['users_groups'], array(
                            $this->join['groups'] => (float) $group_id,
                            $this->join['users']  => (float) $user_id )))
            {
                if (isset($this->_cache_groups[$group_id]))
                {
                    $group_name = $this->_cache_groups[$group_id];
                }
                else
                {
                    $group                          = $this->group($group_id)->result();
                    $group_name                     = $group[0]->name;
                    $this->_cache_groups[$group_id] = $group_name;
                } $this->_cache_user_in_group[$user_id][$group_id] = $group_name;
                $return++;
            }
        } return $return;
    }

    public function remove_from_group($group_ids = FALSE, $user_id = FALSE)
    {
        $this->trigger_events('remove_from_group');
        if (empty($user_id))
        {
            return FALSE;
        } if (!empty($group_ids))
        {
            if (!is_array($group_ids))
            {
                $group_ids = array(
                        $group_ids );
            } foreach ($group_ids as $group_id)
            {
                $this->db->delete($this->tables['users_groups'], array(
                        $this->join['groups'] => (float) $group_id,
                        $this->join['users']  => (float) $user_id ));
                if (isset($this->_cache_user_in_group[$user_id]) && isset($this->_cache_user_in_group[$user_id][$group_id]))
                {
                    unset($this->_cache_user_in_group[$user_id][$group_id]);
                }
            } $return = TRUE;
        }
        else
        {
            if ($return = $this->db->delete($this->tables['users_groups'], array(
                    $this->join['users'] => (float) $user_id )))
            {
                $this->_cache_user_in_group[$user_id] = array();
            }
        } return $return;
    }

    public function groups()
    {
        $this->trigger_events('groups');
        if (isset($this->_ion_where) && !empty($this->_ion_where))
        {
            foreach ($this->_ion_where as $where)
            {
                $this->db->where($where);
            } $this->_ion_where = array();
        } if (isset($this->_ion_limit) && isset($this->_ion_offset))
        {
            $this->db->limit($this->_ion_limit, $this->_ion_offset);
            $this->_ion_limit  = NULL;
            $this->_ion_offset = NULL;
        }
        else if (isset($this->_ion_limit))
        {
            $this->db->limit($this->_ion_limit);
            $this->_ion_limit = NULL;
        } if (isset($this->_ion_order_by) && isset($this->_ion_order))
        {
            $this->db->order_by($this->_ion_order_by, $this->_ion_order);
        } $this->response = $this->db->get($this->tables['groups']);
        return $this;
    }

    public function group($id = NULL)
    {
        $this->trigger_events('group');
        if (isset($id))
        {
            $this->where($this->tables['groups'] . '.id', $id);
        } $this->limit(1);
        $this->order_by('id', 'desc');
        return $this->groups();
    }

    public function update($id, array $data)
    {
        $this->trigger_events('pre_update_user');
        $user = $this->user($id)->row();
        $this->db->trans_begin();
        if (array_key_exists($this->identity_column, $data) && $this->identity_check($data[$this->identity_column]) && $user->{$this->identity_column} !== $data[$this->identity_column])
        {
            $this->db->trans_rollback();
            $this->set_error('account_creation_duplicate_identity');
            $this->trigger_events(array(
                    'post_update_user',
                    'post_update_user_unsuccessful' ));
            $this->set_error('update_unsuccessful');
            return FALSE;
        } $data = $this->_filter_data($this->tables['users'], $data);
        if (array_key_exists($this->identity_column, $data) || array_key_exists('password', $data) || array_key_exists('email', $data))
        {
            if (array_key_exists('password', $data))
            {
                if (!empty($data['password']))
                {
                    $data['password'] = $this->hash_password($data['password'], $user->salt);
                }
                else
                {
                    unset($data['password']);
                }
            }
        } $this->trigger_events('extra_where');
        $this->db->update($this->tables['users'], $data, array(
                'id' => $user->id ));
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $this->trigger_events(array(
                    'post_update_user',
                    'post_update_user_unsuccessful' ));
            $this->set_error('update_unsuccessful');
            return FALSE;
        } $this->db->trans_commit();
        $this->trigger_events(array(
                'post_update_user',
                'post_update_user_successful' ));
        $this->set_message('update_successful');
        return TRUE;
    }

    public function delete_user($id)
    {
        $this->trigger_events('pre_delete_user');
        $this->db->trans_begin();
        $this->remove_from_group(NULL, $id);
        $this->db->delete($this->tables['users'], array(
                'id' => $id ));
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $this->trigger_events(array(
                    'post_delete_user',
                    'post_delete_user_unsuccessful' ));
            $this->set_error('delete_unsuccessful');
            return FALSE;
        } $this->db->trans_commit();
        $this->trigger_events(array(
                'post_delete_user',
                'post_delete_user_successful' ));
        $this->set_message('delete_successful');
        return TRUE;
    }

    public function update_last_login($id)
    {
        $this->trigger_events('update_last_login');
        $this->load->helper('date');
        $this->trigger_events('extra_where');
        $this->db->update($this->tables['users'], array(
                'last_login' => time() ), array(
                'id' => $id ));
        return $this->db->affected_rows() == 1;
    }

    public function set_lang($lang = 'en')
    {
        $this->trigger_events('set_lang');
        if ($this->config->item('user_expire', 'ion_auth') === 0)
        {
            $expire = (60 * 60 * 24 * 365 * 2);
        }
        else
        {
            $expire = $this->config->item('user_expire', 'ion_auth');
        } set_cookie(array(
                'name'   => 'lang_code',
                'value'  => $lang,
                'expire' => $expire ));
        return TRUE;
    }

    public function set_session($user)
    {
        $this->trigger_events('pre_set_session');
        $session_data                                 = array(
                'SESS_IDENTITY'        => $user->{$this->identity_column},
                $this->identity_column => $user->{$this->identity_column},
                'SESS_EMAIL'           => $user->email,
                'SESS_USER_ID'         => $user->id,
                'SESS_OLD_LAST_LOGIN'  => $user->last_login,
                'SESS_BRANCH_ID'       => $user->branch_id,
                'SESS_USERNAME'        => $user->first_name . ' ' . $user->last_name,
                'SESS_LAST_CHECK'      => time(), );
        $this->db->select('groups.name')->from('users')->join('users_groups', 'users.id = users_groups.user_id')->join('groups', 'users_groups.group_id = groups.id')->where('username !=', 'superadmin')->where('users.id', $user->id);
        $session_data['SESS_USER_TYPE']               = $this->db->get()->result_array();
        $branch_data                                  = $this->db->select('branch.financial_year_id,branch.firm_id,is_updated,concat(YEAR(tbl_financial_year.from_date),"-",YEAR(tbl_financial_year.to_date)) as financial_year_title,concat(DATE_FORMAT(tbl_financial_year.from_date, "%m"),"/",YEAR(tbl_financial_year.from_date),"-",DATE_FORMAT(tbl_financial_year.to_date, "%m"),"/",YEAR(tbl_financial_year.to_date)) as financial_year_title_with_month,branch.branch_default_currency,currency.currency_symbol,currency.currency_code,currency.currency_text')->from('users')->join('branch', 'users.branch_id = branch.branch_id')->join('currency', 'currency.currency_id = branch.branch_default_currency')->join('tbl_financial_year', 'tbl_financial_year.year_id = branch.financial_year_id')->join('firm', 'firm.firm_id = branch.firm_id')->where('users.id', $user->id)->where('username !=', 'superadmin')->get()->row();

        $current_date = date('Y-m-d H:i:s');

        $this->db->select('package,activation_date');
        $this->db->from('tbl_billing_info');
        $this->db->where('firm_id',$branch_data->firm_id);
        $pack = $this->db->get();
        if($pack->num_rows() > 0){
            $this->db->select('package,activation_date');
            $this->db->from('tbl_billing_info');
            $this->db->where('payment_status','1');
            $this->db->where('activation_date <=',$current_date);
            $this->db->where('end_date >=',$current_date);
            $this->db->where('package_status','1');
            $this->db->where('firm_id',$branch_data->firm_id);
            $this->db->order_by('bill_id','ASC');
            $q = $this->db->get();
            $package_result = $q->result();
            if(!empty($package_result)){
                $session_data['SESS_PACKAGE_STATUS'] = 1;
                $session_data['SESS_PACKAGE_ID'] = $package_result[0]->package;
                $session_data['SESS_PACKAGE_ACTIVATE'] = $package_result[0]->activation_date;
            }else{
                $session_data['SESS_PACKAGE_STATUS'] = 0;
            }
        }else{
            $session_data['SESS_PACKAGE_STATUS'] = 1;
        }

        $session_data['SESS_FIRM_ID']        = $branch_data->firm_id;
        $session_data['SESS_DEFAULT_CURRENCY']        = $branch_data->branch_default_currency;
        $session_data['SESS_DEFAULT_CURRENCY_SYMBOL'] = $branch_data->currency_symbol;
        $session_data['SESS_DEFAULT_CURRENCY_CODE']   = $branch_data->currency_code;
        $session_data['SESS_DEFAULT_CURRENCY_TEXT']   = $branch_data->currency_text;
        $session_data['SESS_FINANCIAL_YEAR_ID']       = $branch_data->financial_year_id;
        $session_data['SESS_DETAILS_UPDATED']         = $branch_data->is_updated;
        $session_data['SESS_FINANCIAL_YEAR_TITLE']    = trim($branch_data->financial_year_title);
        $session_data['SESS_FINANCIAL_YEAR_TITLE_WITH_MONTH']    = trim($branch_data->financial_year_title_with_month);
        $this->session->set_userdata($session_data);
        $this->trigger_events('post_set_session');
        
        return TRUE;
    }

    public function set_sa_session($user)
    {
        $this->trigger_events('pre_set_session');
        $session_data                      = array(
                'SESS_SA_IDENTITY'       => $user->{$this->identity_column},
                $this->identity_column   => $user->{$this->identity_column},
                'SESS_SA_EMAIL'          => $user->email,
                'SESS_SA_USER_ID'        => $user->id,
                'SESS_SA_OLD_LAST_LOGIN' => $user->last_login,
                'SESS_SA_USERNAME'       => $user->first_name . ' ' . $user->last_name,
                'SESS_SA_LAST_CHECK'     => time(), );
        $user_type                         = array(
                'superadmin' );
        $session_data['SESS_SA_USER_TYPE'] = $user_type;
        $this->session->set_userdata($session_data);
        $this->trigger_events('post_set_session');
        return TRUE;
    }

    public function remember_user($id,$branch_code, $password)
    {
        $this->trigger_events('pre_remember_user');
        if (!$id)
        {
            return FALSE;
        } $user = $this->user($id)->row();
        $salt = $this->salt();
        $this->db->update($this->tables['users'], array(
                'remember_code' => $salt ), array(
                'id' => $id ));
        if ($this->db->affected_rows() > -1)
        {
            if ($this->config->item('user_expire', 'ion_auth') === 0)
            {
                $expire = (60 * 60 * 24 * 365 * 2);
            }
            else
            {
                $expire = $this->config->item('user_expire', 'ion_auth');
            } set_cookie(array(
                    'name'   => $this->config->item('identity_cookie_name', 'ion_auth'),
                    'value'  => $user->{$this->identity_column},
                    'expire' => $expire ));
            set_cookie(array(
                    'name'   => $this->config->item('remember_cookie_name', 'ion_auth'),
                    'value'  => $salt,
                    'expire' => $expire ));
            set_cookie(array(
                    'name'   => 'password',
                    'value'  => $password,
                    'expire' => $expire ));
            set_cookie(array(
                    'name'   => 'branch_code',
                    'value'  => $branch_code,
                    'expire' => $expire ));
            $this->trigger_events(array(
                    'post_remember_user',
                    'remember_user_successful' ));
            return TRUE;
        } $this->trigger_events(array(
                'post_remember_user',
                'remember_user_unsuccessful' ));
        return FALSE;
    }

    public function login_remembered_user()
    {
        $this->trigger_events('pre_login_remembered_user');
        if (!get_cookie($this->config->item('identity_cookie_name', 'ion_auth')) || !get_cookie($this->config->item('remember_cookie_name', 'ion_auth')) || !$this->identity_check(get_cookie($this->config->item('identity_cookie_name', 'ion_auth'))))
        {
            $this->trigger_events(array(
                    'post_login_remembered_user',
                    'post_login_remembered_user_unsuccessful' ));
            return FALSE;
        } $this->trigger_events('extra_where');
        $query = $this->db->select($this->identity_column . ', id,branch_code,branch_id,first_name,last_name, email, last_login')->where($this->identity_column, urldecode(get_cookie($this->config->item('identity_cookie_name', 'ion_auth'))))->where('remember_code', get_cookie($this->config->item('remember_cookie_name', 'ion_auth')))->where('active', 1)->where('username !=', 'superadmin')->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
        if ($query->num_rows() == 1)
        {
            $user = $query->row();
            $this->update_last_login($user->id);
            $this->set_session($user);
            if ($this->config->item('user_extend_on_login', 'ion_auth'))
            {
               // $this->remember_user($user->id);
            } $this->_regenerate_session();
            $this->trigger_events(array(
                    'post_login_remembered_user',
                    'post_login_remembered_user_successful' ));
            return TRUE;
        } $this->trigger_events(array(
                'post_login_remembered_user',
                'post_login_remembered_user_unsuccessful' ));
        return FALSE;
    }

    public function create_group($group_name = FALSE, $group_description = '', $additional_data = array())
    {
        if (!$group_name)
        {
            $this->set_error('group_name_required');
            return FALSE;
        } $existing_group = $this->db->get_where($this->tables['groups'], array(
                        'name' => $group_name ))->num_rows();
        if ($existing_group !== 0)
        {
            $this->set_error('group_already_exists');
            return FALSE;
        } $data     = array(
                'name'        => $group_name,
                'description' => $group_description );
        if (!empty($additional_data))
            $data     = array_merge($this->_filter_data($this->tables['groups'], $additional_data), $data);
        $this->trigger_events('extra_group_set');
        $this->db->insert($this->tables['groups'], $data);
        $group_id = $this->db->insert_id($this->tables['groups'] . '_id_seq');
        $this->set_message('group_creation_successful');
        return $group_id;
    }

    public function update_group($group_id = FALSE, $group_name = FALSE, $additional_data = array())
    {
        if (empty($group_id))
        {
            return FALSE;
        } $data = array();
        if (!empty($group_name))
        {
            $existing_group = $this->db->get_where($this->tables['groups'], array(
                            'name' => $group_name ))->row();
            if (isset($existing_group->id) && $existing_group->id != $group_id)
            {
                $this->set_error('group_already_exists');
                return FALSE;
            } $data['name'] = $group_name;
        } $group = $this->db->get_where($this->tables['groups'], array(
                        'id' => $group_id ))->row();
        if ($this->config->item('admin_group', 'ion_auth') === $group->name && $group_name !== $group->name)
        {
            $this->set_error('group_name_admin_not_alter');
            return FALSE;
        } if (is_string($additional_data))
        {
            $additional_data = array(
                    'description' => $additional_data );
        } if (!empty($additional_data))
        {
            $data = array_merge($this->_filter_data($this->tables['groups'], $additional_data), $data);
        } $this->db->update($this->tables['groups'], $data, array(
                'id' => $group_id ));
        $this->set_message('group_update_successful');
        return TRUE;
    }

    public function delete_group($group_id = FALSE)
    {
        if (!$group_id || empty($group_id))
        {
            return FALSE;
        } $group = $this->group($group_id)->row();
        if ($group->name == $this->config->item('admin_group', 'ion_auth'))
        {
            $this->trigger_events(array(
                    'post_delete_group',
                    'post_delete_group_notallowed' ));
            $this->set_error('group_delete_notallowed');
            return FALSE;
        } $this->trigger_events('pre_delete_group');
        $this->db->trans_begin();
        $this->db->delete($this->tables['users_groups'], array(
                $this->join['groups'] => $group_id ));
        $this->db->delete($this->tables['groups'], array(
                'id' => $group_id ));
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $this->trigger_events(array(
                    'post_delete_group',
                    'post_delete_group_unsuccessful' ));
            $this->set_error('group_delete_unsuccessful');
            return FALSE;
        } $this->db->trans_commit();
        $this->trigger_events(array(
                'post_delete_group',
                'post_delete_group_successful' ));
        $this->set_message('group_delete_successful');
        return TRUE;
    }

    public function set_hook($event, $name, $class, $method, $arguments)
    {
        $this->_ion_hooks->{$event}[$name]            = new stdClass;
        $this->_ion_hooks->{$event}[$name]->class     = $class;
        $this->_ion_hooks->{$event}[$name]->method    = $method;
        $this->_ion_hooks->{$event}[$name]->arguments = $arguments;
    }

    public function remove_hook($event, $name)
    {
        if (isset($this->_ion_hooks->{$event}[$name]))
        {
            unset($this->_ion_hooks->{$event}[$name]);
        }
    }

    public function remove_hooks($event)
    {
        if (isset($this->_ion_hooks->$event))
        {
            unset($this->_ion_hooks->$event);
        }
    }

    protected function _call_hook($event, $name)
    {
        if (isset($this->_ion_hooks->{$event}[$name]) && method_exists($this->_ion_hooks->{$event}[$name]->class, $this->_ion_hooks->{$event}[$name]->method))
        {
            $hook = $this->_ion_hooks->{$event}[$name];
            return call_user_func_array(array(
                    $hook->class,
                    $hook->method ), $hook->arguments);
        } return FALSE;
    }

    public function trigger_events($events)
    {
        if (is_array($events) && !empty($events))
        {
            foreach ($events as $event)
            {
                $this->trigger_events($event);
            }
        }
        else
        {
            if (isset($this->_ion_hooks->$events) && !empty($this->_ion_hooks->$events))
            {
                foreach ($this->_ion_hooks->$events as $name => $hook)
                {
                    $this->_call_hook($events, $name);
                }
            }
        }
    }

    public function set_message_delimiters($start_delimiter, $end_delimiter)
    {
        $this->message_start_delimiter = $start_delimiter;
        $this->message_end_delimiter   = $end_delimiter;
        return TRUE;
    }

    public function set_error_delimiters($start_delimiter, $end_delimiter)
    {
        $this->error_start_delimiter = $start_delimiter;
        $this->error_end_delimiter   = $end_delimiter;
        return TRUE;
    }

    public function set_message($message)
    {
        $this->messages[] = $message;
        return $message;
    }

    public function messages()
    {
        $_output = '';
        foreach ($this->messages as $message)
        {
            $messageLang = $this->lang->line($message) ? $this->lang->line($message) : '##' . $message . '##';
            $_output     .= $this->message_start_delimiter . $messageLang . $this->message_end_delimiter;
        } return $_output;
    }

    public function messages_array($langify = TRUE)
    {
        if ($langify)
        {
            $_output = array();
            foreach ($this->messages as $message)
            {
                $messageLang = $this->lang->line($message) ? $this->lang->line($message) : '##' . $message . '##';
                $_output[]   = $this->message_start_delimiter . $messageLang . $this->message_end_delimiter;
            } return $_output;
        }
        else
        {
            return $this->messages;
        }
    }

    public function clear_messages()
    {
        $this->messages = array();
        return TRUE;
    }

    public function set_error($error)
    {
        $this->errors[] = $error;
        return $error;
    }

    public function errors()
    {
        $_output = '';
        foreach ($this->errors as $error)
        {
            $errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
            $_output   .= $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
        } return $_output;
    }

    public function errors_array($langify = TRUE)
    {
        if ($langify)
        {
            $_output = array();
            foreach ($this->errors as $error)
            {
                $errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
                $_output[] = $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
            } return $_output;
        }
        else
        {
            return $this->errors;
        }
    }

    public function clear_errors()
    {
        $this->errors = array();
        return TRUE;
    }

    protected function _filter_data($table, $data)
    {
        $filtered_data = array();
        $columns       = $this->db->list_fields($table);
        if (is_array($data))
        {
            foreach ($columns as $column)
            {
                if (array_key_exists($column, $data))
                    $filtered_data[$column] = $data[$column];
            }
        } return $filtered_data;
    }

    protected function _prepare_ip($ip_address)
    {
        return $ip_address;
    }

    protected function _regenerate_session()
    {
        if (substr(CI_VERSION, 0, 1) == '2')
        {
            $old_sess_time_to_update            = $this->session->sess_time_to_update;
            $this->session->sess_time_to_update = 0;
            $this->session->sess_update();
            $this->session->sess_time_to_update = $old_sess_time_to_update;
        }
        else
        {
            $this->session->sess_regenerate(FALSE);
        }
    }

    public function allReports()
    {
        $branch_id             = $this->session->userdata('SESS_BRANCH_ID');
        $week                  = date('W');
        $year                  = date("Y");
        $from                  = date("Y-m-d", strtotime("{$year}-W{$week}-1"));
        $to                    = date("Y-m-d", strtotime("{$year}-W{$week}-7"));
        $data['todayProduct']  = $this->db->select('count(*) as item')->where('added_date', date("Y-m-d"))->where('delete_status', 0)->where('branch_id', $branch_id)->get('products')->result();
        $data['weekProduct']   = $this->db->select('count(*) as item')->where('added_date BETWEEN "' . $from . '" AND "' . $to . '"')->where('delete_status', 0)->where('branch_id', $branch_id)->get('products')->result();
        $data['monthProduct']  = $this->db->select('count(*) as item')->where('delete_status', 0)->like('added_date', date('Y-m'))->where('branch_id', $branch_id)->get('products')->result();
        $data['yearProduct']   = $this->db->select('count(*) as item')->where('delete_status', 0)->like('added_date', date('Y'))->where('branch_id', $branch_id)->get('products')->result();
        $data['allProduct']    = $this->db->select('count(*) as item')->where('branch_id', $branch_id)->where('delete_status', 0)->get('products')->result();
        $data['todayPurchase'] = $this->db->select('sum(pi.purchase_item_quantity) as item, sum(pi.purchase_item_grand_total) as value')->from('purchase p')->join('purchase_item pi', 'p.purchase_id = pi.purchase_id')->where('p.purchase_date', date("Y-m-d"))->where('p.branch_id', $branch_id)->where('p.delete_status', 0)->get()->result();
        $data['weekPurchase']  = $this->db->select('sum(pi.purchase_item_quantity) as item, sum(pi.purchase_item_grand_total) as value')->from('purchase p')->join('purchase_item pi', 'p.purchase_id = pi.purchase_id')->where('p.purchase_date BETWEEN "' . $from . '" AND "' . $to . '"')->where('p.branch_id', $branch_id)->where('p.delete_status', 0)->get()->result();
        $data['monthPurchase'] = $this->db->select('sum(pi.purchase_item_quantity) as item, sum(pi.purchase_item_grand_total) as value')->from('purchase p')->join('purchase_item pi', 'p.purchase_id = pi.purchase_id')->where('p.delete_status', 0)->where('p.branch_id', $branch_id)->like('p.purchase_date', date('Y-m'))->get()->result();
        $data['yearPurchase']  = $this->db->select('sum(pi.purchase_item_quantity) as item, sum(pi.purchase_item_grand_total) as value')->from('purchase p')->join('purchase_item pi', 'p.purchase_id = pi.purchase_id')->where('p.delete_status', 0)->where('p.branch_id', $branch_id)->like('p.purchase_date', date('Y'))->get()->result();
        $data['allPurchase']   = $this->db->select('sum(pi.purchase_item_quantity) as item, sum(pi.purchase_item_grand_total) as value')->from('purchase p')->join('purchase_item pi', 'p.purchase_id = pi.purchase_id')->where('p.delete_status', 0)->where('p.branch_id', $branch_id)->get()->result();
        $data['todaySales']    = $this->db->select('sum(si.sales_item_quantity) as item,sum(si.sales_item_grand_total) as value')->from('sales s')->join('sales_item si', 's.sales_id = si.sales_id')->where('s.delete_status', 0)->where('s.branch_id', $branch_id)->where('s.sales_date', date("Y-m-d"))->get()->result();
        $data['weekSales']     = $this->db->select('sum(si.sales_item_quantity) as item,sum(si.sales_item_grand_total) as value')->from('sales s')->join('sales_item si', 's.sales_id = si.sales_id')->where('s.delete_status', 0)->where('s.branch_id', $branch_id)->where('s.sales_date BETWEEN "' . $from . '" AND "' . $to . '"')->get()->result();
        $data['monthSales']    = $this->db->select('sum(si.sales_item_quantity) as item,sum(si.sales_item_grand_total) as value')->from('sales s')->join('sales_item si', 's.sales_id = si.sales_id')->where('s.delete_status', 0)->where('s.branch_id', $branch_id)->like('s.sales_date', date('Y-m'))->get()->result();
        $data['yearSales']     = $this->db->select('sum(si.sales_item_quantity) as item,sum(si.sales_item_grand_total) as value')->from('sales s')->join('sales_item si', 's.sales_id = si.sales_id')->where('s.delete_status', 0)->where('s.branch_id', $branch_id)->like('s.sales_date', date('Y'))->get()->result();
        $data['allSales']      = $this->db->select('sum(si.sales_item_quantity) as item,sum(si.sales_item_grand_total) as value')->from('sales s')->join('sales_item si', 's.sales_id = si.sales_id')->where('s.delete_status', 0)->where('s.branch_id', $branch_id)->get()->result();
        $data['total_sales']   = $this->db->select('sum(si.sales_item_grand_total) as total_sales')->from('sales s')->join('sales_item si', 'si.sales_id = s.sales_id')->where('s.delete_status', 0)->where('s.branch_id', $branch_id)->get()->result();
        return $data;
    }

}
