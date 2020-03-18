<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . "vendor/autoload.php";

use Webit\PHPgs\ExecutorBuilder;
use Webit\PHPgs\Input;
use Webit\PHPgs\Output;
use Webit\PHPgs\Options\Options;
use Webit\PHPgs\Options\Device;

class Bank_statement extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'bank_statement_model' ]);
        $this->modules = $this->get_modules();
    }

    public function index()
    {
        $bank_reconciliation_module_id = $this->config->item('bank_reconciliation_module');
        $data['module_id']             = $bank_reconciliation_module_id;
        $modules                       = $this->modules;
        $privilege                     = "view_privilege";
        $data['privilege']             = "view_privilege";
        $section_modules               = $this->get_section_modules($bank_reconciliation_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $string               = 'b.*,l.*';
        $table                = 'bank_account b';
        $join['ledgers l']    = 'l.ledger_id=b.ledger_id';
        $where                = array(
                'b.delete_status' => 0,
                'b.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') );
        $data['bank']         = $this->general_model->getJoinRecords($string, $table, $where, $join, $order                = "");
        $string               = 'f.*,l.ledger_title';
        $table                = 'file_details f';
        $join['ledgers l']    = 'l.ledger_id=f.ledger_id';
        $where                = array(
                'f.delete_status'     => 0,
                'f.branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                'f.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data['file_details'] = $this->general_model->getJoinRecords($string, $table, $where, $join, $order                = "");
        $this->load->view('bank_statement/view_bank_statement', $data);
    }

    public function get_bank_statement()
    {
        $bank_reconciliation_module_id = $this->config->item('bank_reconciliation_module');
        $data['module_id']             = $bank_reconciliation_module_id;
        $modules                       = $this->modules;
        $privilege                     = "view_privilege";
        $data['privilege']             = "view_privilege";
        $section_modules               = $this->get_section_modules($bank_reconciliation_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $string            = 'b.*,l.*';
        $table             = 'bank_account b';
        $join['ledgers l'] = 'l.ledger_id=b.ledger_id';
        $where             = array(
                'b.delete_status' => 0,
                'b.branch_id'     => $this->session->userdata('SESS_BRANCH_ID')
        );
        $data['bank']      = $this->general_model->getJoinRecords($string, $table, $where, $join, $order             = "");
        $data['modal']     = 'close';
        $this->load->view('bank_statement/get_bank_statement', $data);
    }

    public function add_bank_statement()
    {
        if (isset($_FILES['uploadfilename']))
        {
            $filename = $_FILES['uploadfilename']['name'];

            $config['file_name']     = $filename;
            $config['upload_path']   = './upload/original/';
            $config['allowed_types'] = 'xls|xlsx|csv|pdf';
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('uploadfilename'))
            {
                $error = array(
                        'error' => $this->upload->display_errors() );
                echo $error['error'];
                exit;
                $this->load->view('pdfform/upload_form', $error);
            }
            else
            {
                $data           = $this->upload->data();
                $xls_filename   = explode('.', $data['file_name']);
                $data_IDBI      = $data;
                $file_path      = $data['full_path'];
                $this->session->set_userdata('file_path', $file_path);
                $dt             = new DateTime("now");
                $formatdate     = $dt->format("Y-m-d H:i:s");
                $bank_ledger_id = $this->input->post('bank_ledger_id');
                $month          = $this->input->post('month');

                $file_details_data = $this->general_model->getRecords('file_id', 'file_details', array(
                        'ledger_id'         => $bank_ledger_id,
                        'month'             => $month,
                        "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        'branch_id'         => $this->session->userdata('SESS_BRANCH_ID') ));


                $contain_files = 0;
                if (isset($file_details_data[0]->file_id))
                {
                    $fileid     = $file_details_data[0]->file_id;
                    $statements = $this->general_model->getRecords('*', 'file_data', array(
                            'file_id'           => $fileid,
                            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                            'branch_id'         => $this->session->userdata('SESS_BRANCH_ID') ), array(
                            'contain_files' => 'asc' ), 'contain_files');
                    foreach ($statements as $row)
                    {
                        $contain_files = $row->contain_files;
                    }
                    $contain_files++;
                    $data2 = array(
                            'categorized_status' => '0' );
                    $this->general_model->updateData('file_details', $data2, array(
                            'file_id' => $fileid ));
                }
                else
                {
                    $contain_files = 1;
                    $fileid        = $this->general_model->insertData('file_details', array(
                            "file_name"         => $data['file_name'],
                            "added_date"        => $formatdate,
                            "added_user_id"     => $this->session->userdata('SESS_USER_ID'),
                            "ledger_id"         => $this->input->post('bank_ledger_id'),
                            "month"             => $this->input->post('month'),
                            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                            'branch_id'         => $this->session->userdata('SESS_BRANCH_ID')
                    ));
                }



                $ext    = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                $reader = FALSE;

                switch ($ext)
                {
                    case 'xls':
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                        break;
                    case 'xlsx':
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                        break;
                    case 'csv':
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                        $fh     = fopen($file_path, "r");
                        $reader->setDelimiter($this->DetectDelimiter($fh));
                        fclose($fh);
                        break;
                    case 'pdf':
                        $_view  = '';
                        if (is_array($data) && $fileid)
                        {
                            extract(config_item('filelocationdetails'));
                            $srcfile          = $data['full_path'];
                            $filename         = $data['file_name'];
                            $converted_folder = $converted_folder . $fileid;
                            if (!file_exists($converted_folder))
                            {
                                mkdir($converted_folder);
                            }
                            $convertedfile = $converted_folder . "\\" . $filename;

                            $err = "";
                            try
                            {
                                $executor = ExecutorBuilder::create()->setGhostScriptBinary('gswin64c')->build();
                                $input    = Input::singleFile($srcfile);
                                $output   = Output::create($convertedfile);
                                $options  = Options::create(Device::pdfWrite())
                                        ->withOption('-q', NULL)
                                        ->withOption('-dSAFER', NULL)
                                        ->withOption('-dNOPAUSE', NULL)
                                        ->withOption('-dBATCH', NULL);
                                $executor->execute($input, $output, $options);
                            } catch (Webit\PHPgs\GhostScriptExecutionException $e)
                            {
                                $output = strtolower($e->output());
                                if (preg_match('/\bfile requires a password for access\b/', $output))
                                {
                                    $err = 'password required';
                                }
                            }
                            if (!empty($err))
                            {


                                $this->session->set_userdata('contain_files', $contain_files);
                                redirect('bank_statement/password_required/' . $fileid . '/' . $bank_ledger_id . '/' . $month);
                            }
                            else
                            {

                                $license_code = "786F2A7D-3838-44E5-9809-D31E9E0B8149";
                                $username     = "KARTHIK456";
                                $url          = 'http://www.ocrwebservice.com/restservices/processDocument?gettext=true&outputformat=xls';

                                $fp      = fopen($file_path, 'r');
                                $session = curl_init();
                                curl_setopt($session, CURLOPT_URL, $url);
                                curl_setopt($session, CURLOPT_USERPWD, "$username:$license_code");
                                curl_setopt($session, CURLOPT_UPLOAD, true);
                                curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'POST');
                                curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($session, CURLOPT_TIMEOUT, 200);
                                curl_setopt($session, CURLOPT_HEADER, false);

                                curl_setopt($session, CURLOPT_HTTPHEADER, array(
                                        'Content-Type: application/json' ));

                                curl_setopt($session, CURLOPT_INFILE, $fp);
                                curl_setopt($session, CURLOPT_INFILESIZE, filesize($file_path));
                                $result   = curl_exec($session);
                                $httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);
                                curl_close($session);
                                fclose($fp);

                                if ($httpCode == 401)
                                {

                                    die('Unauthorized request');
                                }
                                $data2 = json_decode($result);

                                if ($httpCode != 200)
                                {








                                    die($data2->ErrorMessage);
                                }


                                $url     = $data2->OutputFileUrl;
                                $content = file_get_contents($url);

                                file_put_contents('upload/original/' . $xls_filename[0] . '.xls', $content);



                                $file_path_details = explode('/', $file_path);
                                $file_path         = '';
                                for ($i = 0; $i < sizeof($file_path_details) - 1; $i++)
                                {
                                    $file_path .= $file_path_details[$i] . '/';
                                }
                                $file_path               .= $xls_filename[0] . '.xls';
                                $config['file_name']     = $xls_filename[0] . '.xls';
                                $config['upload_path']   = base_url() . 'upload/original/';
                                $config['allowed_types'] = 'xls|xlsx|csv|pdf';
                                $this->load->library('upload', $config);
                                $this->upload->initialize($config);
                                $data                    = $this->upload->data();
                                $dt                      = new DateTime("now");
                                $formatdate              = $dt->format("Y-m-d H:i:s");
                                $this->general_model->updateData('file_details', array(
                                        "file_name" => $data['file_name'] ), array(
                                        'file_id' => $fileid ));
                                $ext                     = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                                $reader                  = FALSE;
                                $reader                  = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                            }
                        }
                        break;
                }

                $sheetData = array();
                if (is_object($reader))
                {
                    $spreadsheet = $reader->load($file_path);
                    $sheetData   = $spreadsheet->getActiveSheet()->toArray();
                    $rowemptyarr = array();
                    foreach ($sheetData as $row => $cols)
                    {
                        if (!array_key_exists($row, $rowemptyarr))
                        {
                            $rowemptyarr[$row] = 0;
                        }
                        foreach ($cols as $colkey => $value)
                        {
                            if (!empty($colkey) && !empty($value))
                            {
                                $rowemptyarr[$row] += 1;
                            }
                        }
                    }
                    $maxIndex     = array_search(max($rowemptyarr), $rowemptyarr);
                    $metakeys     = FALSE;
                    $account_no   = $this->general_model->getRecords('account_no', 'bank_account', array(
                            'ledger_id' => $bank_ledger_id ));
                    $account_no   = $account_no[0]->account_no;
                    $match_acc_no = 0;
                    for ($i = 0; $i < count($sheetData); $i++)
                    {
                        $cols = $sheetData[$i];
                        foreach ($cols as $j => $meta_key)
                        {
                            if (preg_match("/" . $account_no . "/", $cols[$j]))
                            {
                                $match_acc_no = 1;
                            }
                        }
                    }
                    $num_rows = 0;
                    for ($i = $maxIndex; $i < count($sheetData); $i++)
                    {
                        $cols = $sheetData[$i];
                        if (!is_array($metakeys))
                        {
                            $metakeys  = $cols;
                            $after_bal = 0;
                            foreach ($metakeys as $k => $meta_key)
                            {
                                if (empty($meta_key) || $after_bal == 1)
                                {
                                    unset($metakeys[$k]);
                                }
                                if (preg_match("/[Bb][Aa][Ll]/", $meta_key))
                                {
                                    $after_bal = 1;
                                }
                            } $metakeys = array_values($metakeys);
                            continue;
                        } $insertarr = array();
                        foreach ($metakeys as $j => $meta_key)
                        {
                            $insertarr[] = array(
                                    "meta_key"          => $meta_key,
                                    "meta_value"        => $cols[$j],
                                    "file_id"           => $fileid,
                                    "added_date"        => $formatdate,
                                    "added_user_id"     => $this->session->userdata('SESS_USER_ID'),
                                    "delete_status"     => 1,
                                    "contain_files"     => $contain_files,
                                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID')
                            );
                        }
                        foreach ($metakeys as $j => $meta_key)
                        {
                            $num_rows++;
                            break;
                        }
                        $status = 0;
                        foreach ($insertarr as $val)
                        {
                            foreach ($val as $key => $value)
                            {
                                if ($key == 'meta_value')
                                {
                                    if (preg_match("/[Dd][Aa][Tt][Ee]/", $value))
                                    {
                                        if ($status == 1)
                                            $status = 2;
                                        else
                                            $status = 1;
                                    }
                                    if (preg_match("/[Bb][Aa][Ll]/", $value))
                                    {
                                        if ($status == 1)
                                            $status = 2;
                                        else
                                            $status = 1;
                                    }
                                }
                            }
                        }
                        if ($status == 2)
                        {
                            $num_rows = 0;
                            $metakeys = FALSE;
                            $i--;
                            continue;
                        }
                    }


                    if ($match_acc_no == 1)
                    {
                        if ($num_rows < 5)
                        {
                            for ($i = $maxIndex; $i < count($sheetData); $i++)
                            {
                                $cols = $sheetData[$i];
                                if (!is_array($metakeys))
                                {
                                    $metakeys  = $cols;
                                    $after_bal = 0;
                                    foreach ($metakeys as $k => $meta_key)
                                    {
                                        if (empty($meta_key) || $after_bal == 1)
                                        {
                                            unset($metakeys[$k]);
                                        }
                                        if (preg_match("/[Bb][Aa][Ll]/", $meta_key))
                                        {
                                            $after_bal = 1;
                                        }
                                    } $metakeys = array_values($metakeys);
                                    continue;
                                } $insertarr = array();
                                foreach ($metakeys as $j => $meta_key)
                                {
                                    $insertarr[] = array(
                                            "meta_key"          => $meta_key,
                                            "meta_value"        => $cols[$j],
                                            "file_id"           => $fileid,
                                            "added_date"        => $formatdate,
                                            "added_user_id"     => $this->session->userdata('SESS_USER_ID'),
                                            "delete_status"     => 1,
                                            "contain_files"     => $contain_files,
                                            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                            'branch_id'         => $this->session->userdata('SESS_BRANCH_ID')
                                    );
                                }
                                $status = 0;
                                foreach ($insertarr as $val)
                                {
                                    foreach ($val as $key => $value)
                                    {
                                        if ($key == 'meta_value')
                                        {
                                            if (preg_match("/[Dd][Aa][Tt][Ee]/", $value))
                                            {
                                                $datelen = strlen($value);
                                                $dateval = $value;
                                                if ($status == 1)
                                                    $status  = 2;
                                                else
                                                    $status  = 1;
                                            }
                                            if (preg_match("/[Bb][Aa][Ll]/", $value))
                                            {
                                                if ($status == 1)
                                                    $status = 2;
                                                else
                                                    $status = 1;
                                            }
                                        }
                                    }
                                }
                                if ($status == 2)
                                {
                                    $metakeys = FALSE;
                                    $i--;
                                    continue;
                                }
                            }
                            if ($datelen > 50)
                            {


                                $this->fetchpdf($data_IDBI, $fileid);
                            }
                            else
                            {

                                redirect('bank_statement/minimum_statement/' . $fileid . '/' . $bank_ledger_id . '/' . $month);
                            }
                        }
                        else
                        {

                            for ($i = $maxIndex; $i < count($sheetData); $i++)
                            {
                                $cols = $sheetData[$i];
                                if (!is_array($metakeys))
                                {
                                    $metakeys  = $cols;
                                    $after_bal = 0;
                                    foreach ($metakeys as $k => $meta_key)
                                    {
                                        if (empty($meta_key) || $after_bal == 1)
                                        {
                                            unset($metakeys[$k]);
                                        }
                                        if (preg_match("/[Bb][Aa][Ll]/", $meta_key))
                                        {
                                            $after_bal = 1;
                                        }
                                    } $metakeys = array_values($metakeys);
                                    continue;
                                } $insertarr = array();
                                foreach ($metakeys as $j => $meta_key)
                                {
                                    $insertarr[] = array(
                                            "meta_key"          => $meta_key,
                                            "meta_value"        => $cols[$j],
                                            "file_id"           => $fileid,
                                            "added_date"        => $formatdate,
                                            "added_user_id"     => $this->session->userdata('SESS_USER_ID'),
                                            "delete_status"     => 1,
                                            "contain_files"     => $contain_files,
                                            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                            'branch_id'         => $this->session->userdata('SESS_BRANCH_ID')
                                    );
                                }
                                $status = 0;
                                foreach ($insertarr as $val)
                                {
                                    foreach ($val as $key => $value)
                                    {
                                        if ($key == 'meta_value')
                                        {
                                            if (preg_match("/[Dd][Aa][Tt][Ee]/", $value))
                                            {
                                                if ($status == 1)
                                                    $status = 2;
                                                else
                                                    $status = 1;
                                            }
                                            if (preg_match("/[Bb][Aa][Ll]/", $value))
                                            {
                                                if ($status == 1)
                                                    $status = 2;
                                                else
                                                    $status = 1;
                                            }
                                        }
                                    }
                                }
                                if ($status == 2)
                                {
                                    $metakeys = FALSE;
                                    $i--;
                                    $this->general_model->deleteData('file_data', array(
                                            'file_id'       => $fileid,
                                            'contain_files' => $contain_files ));
                                    continue;
                                }
                                $this->general_model->insertBatchData('file_data', $insertarr);
                            }


                            redirect(base_url() . "bank_statement");
                        }
                    }
                    else
                    {

                        redirect('bank_statement/mismatch_account/' . $fileid . '/' . $bank_ledger_id . '/' . $month);
                    }
                }
                else
                {
                    $this->fetchpdf($data, $fileid);
                }
            }
        }
        else
        {
            $this->load->view('test/upload-view-xls');
        }
    }

    public function mismatch_account($fileid, $bank_ledger_id, $month)
    {
        // $modules = $this->modules;
        // foreach ($modules['modules'] as $key => $value)
        // {
        //     $data['active_modules'][$key] = $value->module_id;
        //     if ($value->view_privilege == "yes")
        //     {
        //         $data['active_view'][$key] = $value->module_id;
        //     }
        //     if ($value->edit_privilege == "yes")
        //     {
        //         $data['active_edit'][$key] = $value->module_id;
        //     }
        //     if ($value->delete_privilege == "yes")
        //     {
        //         $data['active_delete'][$key] = $value->module_id;
        //     }
        //     if ($value->add_privilege == "yes")
        //     {
        //         $data['active_add'][$key] = $value->module_id;
        //     }
        // }

        $this->general_model->deleteData('file_details', array(
                'file_id' => $fileid ));
        $data['bank_ledger']    = $bank_ledger_id;
        $data['file_month']     = $month;
        $data['fileid']         = $fileid;
        $data['mismatch_modal'] = 'open';

        $string            = 'b.*,l.*';
        $table             = 'bank_account b';
        $join['ledgers l'] = 'l.ledger_id=b.ledger_id';
        $where             = array(
                'b.delete_status' => 0,
                'b.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') );
        $data['bank']      = $this->general_model->getJoinRecords($string, $table, $where, $join, $order             = "");
        $this->load->view('bank_statement/get_bank_statement', $data);
    }

    public function minimum_statement($fileid, $bank_ledger_id, $month)
    {
        // $modules = $this->modules;
        // foreach ($modules['modules'] as $key => $value)
        // {
        //     $data['active_modules'][$key] = $value->module_id;
        //     if ($value->view_privilege == "yes")
        //     {
        //         $data['active_view'][$key] = $value->module_id;
        //     }
        //     if ($value->edit_privilege == "yes")
        //     {
        //         $data['active_edit'][$key] = $value->module_id;
        //     }
        //     if ($value->delete_privilege == "yes")
        //     {
        //         $data['active_delete'][$key] = $value->module_id;
        //     }
        //     if ($value->add_privilege == "yes")
        //     {
        //         $data['active_add'][$key] = $value->module_id;
        //     }
        // }

        $this->general_model->deleteData('file_details', array(
                'file_id' => $fileid ));
        $data['bank_ledger']  = $bank_ledger_id;
        $data['file_month']   = $month;
        $data['fileid']       = $fileid;
        $data['minimum_stat'] = 'open';

        $string            = 'b.*,l.*';
        $table             = 'bank_account b';
        $join['ledgers l'] = 'l.ledger_id=b.ledger_id';
        $where             = array(
                'b.delete_status' => 0,
                'b.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') );
        $data['bank']      = $this->general_model->getJoinRecords($string, $table, $where, $join, $order             = "");
        $this->load->view('bank_statement/get_bank_statement', $data);
    }

    public function statement_history()
    {
        $file_id    = $this->input->post('file_id');
        $statements = $this->general_model->getRecords('*', 'file_data', array(
                'file_id' => $file_id ), array(
                'contain_files' => 'asc' ), 'contain_files');
        $output     = '';
        $i          = 1;
        if ($statements)
        {
            $output .= '<div class="form-group">
                         <table border="1" class="table table-bordered table-striped dataTable">
                         <tr><th>Uploaded Date & Time</th>
                             <th>Statements</th>
                             <th>Action</th>
                         </tr>';
            foreach ($statements as $row)
            {

                $output .= '<tr>
                            <td>' . $row->added_date . '</td>
                            <td>Statement ' . $i . '</td>
                            <td>
                                <a href="' . base_url() . 'bank_statement/showdata_file/' . $row->file_id . '/' . $row->contain_files . '" title="View" class="btn btn-xs btn-warning"><i class="fa fa-eye"></i></a>

                                <a href="" data-file_id="' . $row->file_id . '" data-contain_files="' . $row->contain_files . '" data-target="#delete_file_data" data-toggle="modal" title="Delete" class="btn btn-xs btn-danger file_delete"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>';
                $i++;
            }

            $output .= '</table></div>';
        }
        else
        {
            $output = 'Statement is not exist. Please upload some statements for this account.';
        }
        echo json_encode($output);
    }

    public function password_required($fileid, $bank_ledger_id, $month)
    {
        // $modules = $this->modules;
        // foreach ($modules['modules'] as $key => $value)
        // {
        //     $data['active_modules'][$key] = $value->module_id;
        //     if ($value->view_privilege == "yes")
        //     {
        //         $data['active_view'][$key] = $value->module_id;
        //     }
        //     if ($value->edit_privilege == "yes")
        //     {
        //         $data['active_edit'][$key] = $value->module_id;
        //     }
        //     if ($value->delete_privilege == "yes")
        //     {
        //         $data['active_delete'][$key] = $value->module_id;
        //     }
        //     if ($value->add_privilege == "yes")
        //     {
        //         $data['active_add'][$key] = $value->module_id;
        //     }
        // }


        if ($this->session->flashdata('errormsg'))
        {
            $data['errormsg'] = "Wrong password";
        } $data['bank_ledger'] = $bank_ledger_id;
        $data['file_month']  = $month;
        $data['fileid']      = $fileid;
        $data['modal']       = 'open';

        $string            = 'b.*,l.*';
        $table             = 'bank_account b';
        $join['ledgers l'] = 'l.ledger_id=b.ledger_id';
        $where             = array(
                'b.delete_status' => 0,
                'b.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') );
        $data['bank']      = $this->general_model->getJoinRecords($string, $table, $where, $join, $order             = "");
        $this->load->view('bank_statement/get_bank_statement', $data);
    }

    public function password_protected()
    {
        $_view = "";
        extract($this->input->post());
        if (isset($pwd) && isset($fileid))
        {
            $filename     = $this->general_model->getRecords('*', 'file_details', array(
                    'file_id' => $fileid ));
            $file_name    = $filename[0]->file_name;
            $xls_filename = explode('.', $file_name);
            $file_name    = $xls_filename[0] . '.pdf';
            extract(config_item('filelocationdetails'));
            try
            {


                if (isset($file_name))
                {
                    $srcfile       = $original_folder . $file_name;
                    $convertedfile = $converted_folder . "$fileid\\$file_name";
                    $executor      = ExecutorBuilder::create()->setGhostScriptBinary('gswin64c')->build();
                    $input         = Input::singleFile($srcfile);
                    $output        = Output::create($convertedfile);
                    $options       = Options::create(Device::pdfWrite())
                            ->withOption('-q', NULL)
                            ->withOption('-dSAFER', NULL)
                            ->withOption('-dNOPAUSE', NULL)
                            ->withOption('-dBATCH', NULL)
                            ->withOption('-sPDFPassword', $pwd);
                    $executor->execute($input, $output, $options);
                }
            } catch (Webit\PHPgs\GhostScriptExecutionException $e)
            {
                $output = strtolower($e->output());

                if (preg_match('/\bfile requires a password for access\b/', $output) || preg_match('/\bpassword did not work\b/', $output))
                {

                    $this->session->set_flashdata('errormsg', true);

                    redirect('bank_statement/password_required/' . $fileid . '/' . $bank_ledger . '/' . $file_month);
                }
            } $file_path    = $convertedfile;
            $license_code = "786F2A7D-3838-44E5-9809-D31E9E0B8149";
            $username     = "KARTHIK456";
            $url          = 'http://www.ocrwebservice.com/restservices/processDocument?gettext=true&outputformat=xls';

            $fp      = fopen($file_path, 'r');
            $session = curl_init();
            curl_setopt($session, CURLOPT_URL, $url);
            curl_setopt($session, CURLOPT_USERPWD, "$username:$license_code");
            curl_setopt($session, CURLOPT_UPLOAD, true);
            curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($session, CURLOPT_TIMEOUT, 200);
            curl_setopt($session, CURLOPT_HEADER, false);

            curl_setopt($session, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json' ));

            curl_setopt($session, CURLOPT_INFILE, $fp);
            curl_setopt($session, CURLOPT_INFILESIZE, filesize($file_path));
            $result   = curl_exec($session);
            $httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);
            curl_close($session);
            fclose($fp);

            if ($httpCode == 401)
            {

                die('Unauthorized request');
            }
            $data2 = json_decode($result);

            if ($httpCode != 200)
            {


                die($data2->ErrorMessage);
            }


            $url     = $data2->OutputFileUrl;
            $content = file_get_contents($url);

            file_put_contents('upload/original/' . $xls_filename[0] . '.xls', $content);
            $file_path_details = explode('\\', $file_path);

            $file_path = '';
            for ($i = 0; $i < sizeof($file_path_details) - 3; $i++)
            {
                $file_path .= $file_path_details[$i] . '/';
            }
            $file_path .= 'original/' . $xls_filename[0] . '.xls';

            $config['file_name']     = $xls_filename[0] . '.xls';
            $config['upload_path']   = base_url() . 'upload/original/';
            $config['allowed_types'] = 'xls|xlsx|csv|pdf';
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            $data                    = $this->upload->data();
            $dt                      = new DateTime("now");
            $formatdate              = $dt->format("Y-m-d H:i:s");
            $contain_files           = $this->session->userdata('contain_files');
            $this->session->unset_userdata('contain_files');
            $this->general_model->updateData('file_details', array(
                    "file_name" => $data['file_name'] ), array(
                    'file_id' => $fileid ));





























            $ext         = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            $reader      = FALSE;
            $reader      = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            $spreadsheet = $reader->load($file_path);
            $sheetData   = $spreadsheet->getActiveSheet()->toArray();
            $rowemptyarr = array();
            foreach ($sheetData as $row => $cols)
            {
                if (!array_key_exists($row, $rowemptyarr))
                {
                    $rowemptyarr[$row] = 0;
                }
                foreach ($cols as $colkey => $value)
                {
                    if (!empty($colkey) && !empty($value))
                    {
                        $rowemptyarr[$row] += 1;
                    }
                }
            } $maxIndex = array_search(max($rowemptyarr), $rowemptyarr);
            $metakeys = FALSE;
            for ($i = $maxIndex; $i < count($sheetData); $i++)
            {
                $cols = $sheetData[$i];
                if (!is_array($metakeys))
                {
                    $metakeys  = $cols;
                    $after_bal = 0;
                    foreach ($metakeys as $k => $meta_key)
                    {
                        if (empty($meta_key) || $after_bal == 1)
                        {
                            unset($metakeys[$k]);
                        }
                        if (preg_match("/[Bb][Aa][Ll]/", $meta_key))
                        {
                            $after_bal = 1;
                        }
                    } $metakeys = array_values($metakeys);
                    continue;
                } $insertarr = array();
                foreach ($metakeys as $j => $meta_key)
                {
                    $insertarr[] = array(
                            "meta_key"          => $meta_key,
                            "meta_value"        => $cols[$j],
                            "file_id"           => $fileid,
                            "added_date"        => $formatdate,
                            "added_user_id"     => $this->session->userdata('SESS_USER_ID'),
                            "delete_status"     => 1,
                            "contain_files"     => $contain_files,
                            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                            'branch_id'         => $this->session->userdata('SESS_BRANCH_ID')
                    );
                }
                $status = 0;
                foreach ($insertarr as $val)
                {
                    foreach ($val as $key => $value)
                    {
                        if ($key == 'meta_value')
                        {
                            if (preg_match("/[Dd][Aa][Tt][Ee]/", $value))
                            {
                                if ($status == 1)
                                    $status = 2;
                                else
                                    $status = 1;
                            }
                            if (preg_match("/[Bb][Aa][Ll]/", $value))
                            {
                                if ($status == 1)
                                    $status = 2;
                                else
                                    $status = 1;
                            }
                        }
                    }
                }
                if ($status == 2)
                {
                    $metakeys = FALSE;
                    $i--;
                    $this->general_model->deleteData('file_data', array(
                            'file_id'       => $fileid,
                            'contain_files' => $contain_files ));
                    continue;
                }
                $this->general_model->insertBatchData('file_data', $insertarr);
            }




            redirect(base_url() . "bank_statement");
        }
        redirect(base_url() . "bank_statement");
    }

    public function showdata($fileid = FALSE)
    {
        // $modules = $this->modules;
        // foreach ($modules['modules'] as $key => $value)
        // {
        //     $modules_data['active_modules'][$key] = $value->module_id;
        //     if ($value->view_privilege == "yes")
        //     {
        //         $modules_data['active_view'][$key] = $value->module_id;
        //     }
        //     if ($value->edit_privilege == "yes")
        //     {
        //         $modules_data['active_edit'][$key] = $value->module_id;
        //     }
        //     if ($value->delete_privilege == "yes")
        //     {
        //         $modules_data['active_delete'][$key] = $value->module_id;
        //     }
        //     if ($value->add_privilege == "yes")
        //     {
        //         $modules_data['active_add'][$key] = $value->module_id;
        //     }
        // }

        if ($fileid)
        {

            $status = $this->general_model->getRecords('*', 'file_details', array(
                    'file_id' => $fileid ));
            if ($status[0]->categorized_status == 0)
            {
                $file_data = $this->general_model->getRecords('meta_key,meta_value', 'file_data', array(
                        'file_id' => $fileid ));
                $meta_keys = array();
                $test      = 0;

                foreach ($file_data as $data)
                {
                    if ($test == 0)
                    {
                        $test2 = $data->meta_key;
                    } if (!in_array($data->meta_key, $meta_keys))
                    {

                        $meta_keys[] = $data->meta_key;
                    }
                    else
                    {
                        if ($test2 != $data->meta_key)
                        {
                            $meta_keys[] = $data->meta_key;
                        }
                        else
                        {
                            break;
                        }
                    }
                    $test++;
                }
                $dataarr        = array();
                $count_meta_key = count($meta_keys);
                $sample_array   = array();
                $i              = 0;
                $k              = 0;
                foreach ($file_data as $data)
                {

                    $sample_array[$k][] = $data->meta_value;
                    $i++;
                    if ($i >= $count_meta_key)
                    {
                        $i = 0;
                        $k++;
                    }
                }
                $content = array();
                for ($i = 0; $i < $k; $i++)
                {
                    for ($j = 0; $j < $test; $j++)
                    {
                        if ($sample_array[$i][$test - 1] != '')
                        {
                            $content[$i][$j] = $sample_array[$i][$j];
                            $count           = 0;
                            for ($l = $i + 1; $l < $k; $l++)
                            {
                                if ($sample_array[$l][0] == '' && $sample_array[$l][$test - 1] == '')
                                {
                                    $count++;
                                    $content[$i][$j] .= ' ' . $sample_array[$l][$j];
                                }
                                else
                                {
                                    break;
                                }
                            }
                        }
                    }
                    if ($count != 0)
                    {
                        $i += $count;
                    }
                }
                $content = array_values($content);



                $records = array();
                for ($i = 0; $i < sizeof($content); $i++)
                {
                    $increment = 0;
                    $start     = 0;
                    for ($j = 0; $j < sizeof($content[$i]); $j++)
                    {
                        $date = '';
                        if (preg_match('/[0-9]{0,2}\.[0-9]{0,2}\.[0-9]{4}/', trim($content[$i][$j]), $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd.m.Y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\-[0-9]{0,2}\-[0-9]{4}/', trim($content[$i][$j]), $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd-m-Y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\/[0-9]{0,2}\/[0-9]{4}/', trim($content[$i][$j]), $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd/m/Y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\s[0-9]{0,2}\s[0-9]{4}/', $content[$i][$j], $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd m Y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\.[0-9]{0,2}\.[0-9]{2}/', trim($content[$i][$j]), $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd.m.y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\-[0-9]{0,2}\-[0-9]{2}/', trim($content[$i][$j]), $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd-m-y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\/[0-9]{0,2}\/[0-9]{2}/', trim($content[$i][$j]), $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd/m/y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\s[0-9]{0,2}\s[0-9]{2}/', $content[$i][$j], $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd m y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\.([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])\.[0-9]{2,4}/', trim($content[$i][$j]), $matches) ||
                                preg_match('/[0-9]{0,2}\.([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])\.[0-9]{2,4}/', trim($content[$i][$j]), $matches))
                        {



                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $value = new DateTime($matches[0]);
                                $date  = $value->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\-([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])\-[0-9]{2,4}/', trim($content[$i][$j]), $matches) ||
                                preg_match('/[0-9]{0,2}\-([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])\-[0-9]{2,4}/', trim($content[$i][$j]), $matches))
                        {



                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $value = new DateTime($matches[0]);
                                $date  = $value->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\/([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])\/[0-9]{2,4}/', trim($content[$i][$j]), $matches) ||
                                preg_match('/[0-9]{0,2}\/([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])\/[0-9]{2,4}/', trim($content[$i][$j]), $matches))
                        {



                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $value = new DateTime($matches[0]);
                                $date  = $value->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\s([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])\s[0-9]{2,4}/', $content[$i][$j], $matches) ||
                                preg_match('/[0-9]{0,2}\s([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])\s[0-9]{2,4}/', $content[$i][$j], $matches))
                        {



                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $strdate = preg_replace('/\s+/', '-', $matches[0]);
                                $value   = new DateTime($matches[0]);
                                $date    = $value->format('d-m-Y');
                                $month   = date("m", strtotime($strdate));
                                $start++;
                            }
                        } if ($start != 0)
                        {

                            if ($month != $status[0]->month && $start == 1)
                            {
                                $increment++;
                                break;
                            }
                            if ($date == '')
                            {
                                $records[$i][$j] = $content[$i][$j];
                            }
                            else
                            {
                                $records[$i][$j] = $date;
                            }
                        }
                    }
                    if ($increment != 0)
                    {
                        $i += $increment - 1;
                    }
                }
                $i           = 0;
                $new_records = array();
                foreach ($records as $arr_value)
                {
                    $new_records[$i] = array_values($arr_value);
                    $i++;
                }
                $records = $new_records;
                $records = array_values($records);


                $final_records = array();
                for ($m = 0; $m < sizeof($records); $m++)
                {
                    $duplicate_entry = 0;
                    for ($a = $m; $a < sizeof($records); $a++)
                    {
                        $increase = 0;
                        if ($m != $a)
                        {
                            for ($n = 0; $n < sizeof($records[$m]); $n++)
                            {
                                if ($records[$m][$n] == $records[$a][$n])
                                {
                                    $increase++;
                                }
                            }
                        }
                        if ($increase == sizeof($records[$m]))
                        {
                            $duplicate_entry = 1;
                            break;
                        }
                    }

                    if ($duplicate_entry == 0)
                    {
                        for ($b = 0; $b < sizeof($records[$m]); $b++)
                        {
                            $final_records[$m][$b] = $records[$m][$b];
                        }
                    }
                }



                $final_records = array_values($final_records);
                $sortdata      = $this->sortArray($final_records, '0');

                $myFile   = base_url() . "assets/json/bank_statement_keywords3.json";
                $arr_data = array();

                try
                {

                    $jsondata = file_get_contents($myFile);
                    $arr_data = json_decode($jsondata, true);
                    $i        = 0;
                    $keywords = array();
                    foreach ($arr_data as $key => $value)
                    {
                        foreach ($value as $key1 => $val)
                        {
                            foreach ($val as $k => $v)
                            {
                                $keywords[$i][$k] = $v;
                            }
                            $i++;
                        }
                    }
                } catch (Exception $e)
                {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                } $arr_index = array();
                $arr_key   = array();

                $keywords_lower = array();
                $arr            = array();
                foreach ($keywords as $arr)
                {
                    $keywords_lower[] = array_map('strtolower', $arr);
                } $i          = 0;
                $final_keys = array();
                foreach ($meta_keys as $value)
                {
                    if (preg_match('/[dD][Aa][Tt][Ee]/', $value))
                        $i            = 1;
                    if ($i == 1)
                        $final_keys[] = $value;
                }
                $i          = 0;
                $j          = 0;
                $desc_index = 0;
                foreach ($final_keys as $value)
                {
                    $arr_index[$i] = 0;
                    $val           = strtolower($value);
                    foreach ($keywords_lower as $value)
                    {
                        if ($key_val = array_search($val, $value, true))
                        {
                            if ($key_val == 'reference_no')
                                $desc_index    = $j;
                            $arr_index[$i] = 1;
                            $arr_key[]     = $key_val;
                            $j++;
                        }
                    }
                    $i++;
                } $correct_meta_keys = $arr_key;



                $correct_data = array();
                for ($i = 0; $i < sizeof($sortdata); $i++)
                {
                    for ($j = 0; $j < sizeof($sortdata[$i]); $j++)
                    {
                        if ($arr_index[$j] == 1)
                        {
                            $correct_data[$i][$j] = $sortdata[$i][$j];
                        }
                    }
                } $correct_data = array_values($correct_data);


                $myFile2   = base_url() . "assets/json/bank_stat_desc_keyword.json";
                $arr_data2 = array();

                try
                {

                    $jsondata2 = file_get_contents($myFile2);
                    $arr_data2 = json_decode($jsondata2, true);
                } catch (Exception $e)
                {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                }
                $arr_data2 = array_map('strtolower', $arr_data2);
                $i         = 0;
                foreach ($correct_data as $key => $value)
                {
                    if (isset($value[$desc_index]))
                    {
                        $desc_val = strtolower($value[$desc_index]);
                        foreach ($arr_data2 as $key => $val)
                        {
                            if (strpos($desc_val, $val) !== false)
                            {
                                $i++;
                            }
                        }
                    }
                } if ($i > 2)
                {

                }
                else
                {

                } $_view = $this->load->view("pdfviewer/data-view", array(
                        "dataarr"        => $correct_data,
                        "meta_keys"      => $correct_meta_keys,
                        "file_id"        => $fileid
                        // "active_modules" => $modules_data['active_modules'],
                        // "active_view"    => $modules_data['active_view'],
                        // "active_edit"    => $modules_data['active_edit'],
                        // "active_delete"  => $modules_data['active_delete'],
                        // "active_add"     => $modules_data['active_add']
                        ), TRUE);
                $this->load->view('pdfviewer/index', array(
                        "_view" => $_view ));
            }
            else
            {
                $modules_data['data'] = $this->general_model->getRecords('*', 'bank_statement', array(
                        'file_id'       => $fileid,
                        'month(date)'   => $status[0]->month,
                        'delete_status' => 0 ), array(
                        'date' => 'asc' ));
                // echo "<pre>";
                // print_r($modules_data['data']);
                // exit;
                $this->load->view('pdfviewer/bank_data-view', $modules_data);
            }
        }
    }

    function sortArray($data, $field)
    {
        $field = (array) $field;
        uasort($data, function($a, $b) use($field)
        {
            $retval = 0;
            foreach ($field as $fieldname)
            {
                if ($retval == 0)
                    $retval = strnatcmp($a[$fieldname], $b[$fieldname]);
            }
            return $retval;
        });
        return $data;
    }

    public function showdata_file($fileid, $contain_files)
    {
        // $modules = $this->modules;
        // foreach ($modules['modules'] as $key => $value)
        // {
        //     $modules_data['active_modules'][$key] = $value->module_id;
        //     if ($value->view_privilege == "yes")
        //     {
        //         $modules_data['active_view'][$key] = $value->module_id;
        //     }
        //     if ($value->edit_privilege == "yes")
        //     {
        //         $modules_data['active_edit'][$key] = $value->module_id;
        //     }
        //     if ($value->delete_privilege == "yes")
        //     {
        //         $modules_data['active_delete'][$key] = $value->module_id;
        //     }
        //     if ($value->add_privilege == "yes")
        //     {
        //         $modules_data['active_add'][$key] = $value->module_id;
        //     }
        // }

        if ($fileid)
        {

            $status = $this->general_model->getRecords('*', 'file_details', array(
                    'file_id' => $fileid ));
            if ($status[0]->categorized_status == 0)
            {
                $file_data = $this->general_model->getRecords('meta_key,meta_value', 'file_data', array(
                        'file_id'       => $fileid,
                        'contain_files' => $contain_files ));
                $meta_keys = array();
                $test      = 0;

                foreach ($file_data as $data)
                {
                    if ($test == 0)
                    {
                        $test2 = $data->meta_key;
                    } if (!in_array($data->meta_key, $meta_keys))
                    {

                        $meta_keys[] = $data->meta_key;
                    }
                    else
                    {
                        if ($test2 != $data->meta_key)
                        {
                            $meta_keys[] = $data->meta_key;
                        }
                        else
                        {
                            break;
                        }
                    }
                    $test++;
                }
                $dataarr        = array();
                $count_meta_key = count($meta_keys);
                $sample_array   = array();
                $i              = 0;
                $k              = 0;
                foreach ($file_data as $data)
                {

                    $sample_array[$k][] = $data->meta_value;
                    $i++;
                    if ($i >= $count_meta_key)
                    {
                        $i = 0;
                        $k++;
                    }
                }
                $content = array();
                for ($i = 0; $i < $k; $i++)
                {
                    for ($j = 0; $j < $test; $j++)
                    {
                        if ($sample_array[$i][$test - 1] != '')
                        {
                            $content[$i][$j] = $sample_array[$i][$j];
                            $count           = 0;
                            for ($l = $i + 1; $l < $k; $l++)
                            {
                                if ($sample_array[$l][0] == '' && $sample_array[$l][$test - 1] == '')
                                {
                                    $count++;
                                    $content[$i][$j] .= ' ' . $sample_array[$l][$j];
                                }
                                else
                                {
                                    break;
                                }
                            }
                        }
                    }
                    if ($count != 0)
                    {
                        $i += $count;
                    }
                }
                $content = array_values($content);
                $records = array();
                for ($i = 0; $i < sizeof($content); $i++)
                {
                    $increment = 0;
                    $start     = 0;
                    for ($j = 0; $j < sizeof($content[$i]); $j++)
                    {
                        $date = '';
                        if (preg_match('/[0-9]{0,2}\.[0-9]{0,2}\.[0-9]{4}/', trim($content[$i][$j]), $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd.m.Y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\-[0-9]{0,2}\-[0-9]{4}/', trim($content[$i][$j]), $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd-m-Y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\/[0-9]{0,2}\/[0-9]{4}/', trim($content[$i][$j]), $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd/m/Y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\s[0-9]{0,2}\s[0-9]{4}/', $content[$i][$j], $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd m Y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\.[0-9]{0,2}\.[0-9]{2}/', trim($content[$i][$j]), $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd.m.y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\-[0-9]{0,2}\-[0-9]{2}/', trim($content[$i][$j]), $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd-m-y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\/[0-9]{0,2}\/[0-9]{2}/', trim($content[$i][$j]), $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd/m/y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\s[0-9]{0,2}\s[0-9]{2}/', $content[$i][$j], $matches))
                        {
                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $str   = 'd m y';
                                $date  = DateTime::createFromFormat($str, $matches[0])->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                                $value = new DateTime($date);
                                $date  = $value->format('d-m-Y');
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\.([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])\.[0-9]{2,4}/', trim($content[$i][$j]), $matches) ||
                                preg_match('/[0-9]{0,2}\.([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])\.[0-9]{2,4}/', trim($content[$i][$j]), $matches))
                        {



                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $value = new DateTime($matches[0]);
                                $date  = $value->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\-([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])\-[0-9]{2,4}/', trim($content[$i][$j]), $matches) ||
                                preg_match('/[0-9]{0,2}\-([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])\-[0-9]{2,4}/', trim($content[$i][$j]), $matches))
                        {



                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $value = new DateTime($matches[0]);
                                $date  = $value->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\/([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])\/[0-9]{2,4}/', trim($content[$i][$j]), $matches) ||
                                preg_match('/[0-9]{0,2}\/([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])\/[0-9]{2,4}/', trim($content[$i][$j]), $matches))
                        {



                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $value = new DateTime($matches[0]);
                                $date  = $value->format('d-m-Y');
                                $month = date("m", strtotime($date));
                                $start++;
                            }
                        }
                        elseif (preg_match('/[0-9]{0,2}\s([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])\s[0-9]{2,4}/', $content[$i][$j], $matches) ||
                                preg_match('/[0-9]{0,2}\s([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])\s[0-9]{2,4}/', $content[$i][$j], $matches))
                        {



                            if (strlen($content[$i][$j]) > 20)
                            {
                                $date = $content[$i][$j];
                            }
                            else
                            {
                                $strdate = preg_replace('/\s+/', '-', $matches[0]);
                                $value   = new DateTime($matches[0]);
                                $date    = $value->format('d-m-Y');
                                $month   = date("m", strtotime($strdate));
                                $start++;
                            }
                        } if ($start != 0)
                        {

                            if ($month != $status[0]->month && $start == 1)
                            {
                                $increment++;
                                break;
                            }
                            if ($date == '')
                            {
                                $records[$i][$j] = $content[$i][$j];
                            }
                            else
                            {
                                $records[$i][$j] = $date;
                            }
                        }
                    }
                    if ($increment != 0)
                    {
                        $i += $increment - 1;
                    }
                }
                $i           = 0;
                $new_records = array();
                foreach ($records as $arr_value)
                {
                    $new_records[$i] = array_values($arr_value);
                    $i++;
                }
                $records = $new_records;
                $records = array_values($records);


                $final_records = array();
                for ($m = 0; $m < sizeof($records); $m++)
                {
                    $duplicate_entry = 0;
                    for ($a = $m; $a < sizeof($records); $a++)
                    {
                        $increase = 0;
                        if ($m != $a)
                        {
                            for ($n = 0; $n < sizeof($records[$m]); $n++)
                            {
                                if ($records[$m][$n] == $records[$a][$n])
                                {
                                    $increase++;
                                }
                            }
                        }
                        if ($increase == sizeof($records[$m]))
                        {
                            $duplicate_entry = 1;
                            break;
                        }
                    }

                    if ($duplicate_entry == 0)
                    {
                        for ($b = 0; $b < sizeof($records[$m]); $b++)
                        {
                            $final_records[$m][$b] = $records[$m][$b];
                        }
                    }
                }



                $final_records = array_values($final_records);
                $sortdata      = $this->sortArray($final_records, '0');

                $myFile   = base_url() . "assets/json/bank_statement_keywords3.json";
                $arr_data = array();

                try
                {

                    $jsondata = file_get_contents($myFile);
                    $arr_data = json_decode($jsondata, true);
                    $i        = 0;
                    $keywords = array();
                    foreach ($arr_data as $key => $value)
                    {
                        foreach ($value as $key1 => $val)
                        {
                            foreach ($val as $k => $v)
                            {
                                $keywords[$i][$k] = $v;
                            }
                            $i++;
                        }
                    }
                } catch (Exception $e)
                {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                } $arr_index = array();
                $arr_key   = array();

                $keywords_lower = array();
                $arr            = array();
                foreach ($keywords as $arr)
                {
                    $keywords_lower[] = array_map('strtolower', $arr);
                } $i          = 0;
                $final_keys = array();
                foreach ($meta_keys as $value)
                {
                    if (preg_match('/[dD][Aa][Tt][Ee]/', $value))
                        $i            = 1;
                    if ($i == 1)
                        $final_keys[] = $value;
                }
                $i          = 0;
                $j          = 0;
                $desc_index = 0;
                foreach ($final_keys as $value)
                {
                    $arr_index[$i] = 0;
                    $val           = strtolower($value);
                    foreach ($keywords_lower as $value)
                    {
                        if ($key_val = array_search($val, $value, true))
                        {
                            if ($key_val == 'reference_no')
                                $desc_index    = $j;
                            $arr_index[$i] = 1;
                            $arr_key[]     = $key_val;
                            $j++;
                        }
                    }
                    $i++;
                } $correct_meta_keys = $arr_key;




                $correct_data = array();
                for ($i = 0; $i < sizeof($sortdata); $i++)
                {
                    for ($j = 0; $j < sizeof($sortdata[$i]); $j++)
                    {
                        if ($arr_index[$j] == 1)
                        {
                            $correct_data[$i][$j] = $sortdata[$i][$j];
                        }
                    }
                } $correct_data = array_values($correct_data);





                $myFile2   = base_url() . "assets/json/bank_stat_desc_keyword.json";
                $arr_data2 = array();

                try
                {

                    $jsondata2 = file_get_contents($myFile2);
                    $arr_data2 = json_decode($jsondata2, true);
                } catch (Exception $e)
                {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                }
                $arr_data2 = array_map('strtolower', $arr_data2);
                $i         = 0;
                foreach ($correct_data as $key => $value)
                {
                    if (isset($value[$desc_index]))
                    {
                        $desc_val = strtolower($value[$desc_index]);
                        foreach ($arr_data2 as $key => $val)
                        {
                            if (strpos($desc_val, $val) !== false)
                            {
                                $i++;
                            }
                        }
                    }
                } if ($i > 2)
                {

                }
                else
                {

                } $_view = $this->load->view("pdfviewer/data-view", array(
                        "dataarr"        => $correct_data,
                        "meta_keys"      => $correct_meta_keys,
                        "file_id"        => $fileid,
                        "contain_files"  => $contain_files
                        // "active_modules" => $modules_data['active_modules'],
                        // "active_view"    => $modules_data['active_view'],
                        // "active_edit"    => $modules_data['active_edit'],
                        // "active_delete"  => $modules_data['active_delete'],
                        // "active_add"     => $modules_data['active_add']
                        ), TRUE);
                $this->load->view('pdfviewer/index', array(
                        "_view" => $_view ));
            }
            else
            {
                $modules_data['data'] = $this->general_model->getRecords('*', 'bank_statement', array(
                        'file_id'     => $fileid,
                        'month(date)' => $status[0]->month ), array(
                        'date' => 'asc' ));

                $this->load->view('pdfviewer/bank_data-view', $modules_data);
            }
        }
    }

    public function delete_file_data($file_id, $contain_files)
    {
        $this->general_model->deleteData('file_data', array(
                'file_id'       => $file_id,
                'contain_files' => $contain_files ));
        $this->session->set_userdata('delete_statement', 'success');
        redirect('bank_statement');
    }

    public function delete_file_details($file_id)
    {
        $this->general_model->deleteData('file_data', array(
                'file_id' => $file_id ));
        $this->general_model->deleteData('bank_statement', array(
                'file_id' => $file_id ));
        $this->general_model->deleteData('file_details', array(
                'file_id' => $file_id ));
        $this->session->set_userdata('delete_statement', 'success');
        redirect('bank_statement');
    }

    private function fetchpdf($data = false, $fileid = FALSE)
    {

        $_view = "";
        if (is_array($data) && $fileid)
        {
            extract(config_item('filelocationdetails'));
            $srcfile          = $data['full_path'];
            $filename         = $data['file_name'];
            $converted_folder = $converted_folder . $fileid;
            if (!file_exists($converted_folder))
            {
                mkdir($converted_folder);
            }
            $convertedfile = $converted_folder . "\\" . $filename;
            $err           = "";
            try
            {
                $executor = ExecutorBuilder::create()->setGhostScriptBinary('gswin64c')->build();
                $input    = Input::singleFile($srcfile);
                $output   = Output::create($convertedfile);
                $options  = Options::create(Device::pdfWrite())
                        ->withOption('-q', NULL)
                        ->withOption('-dSAFER', NULL)
                        ->withOption('-dNOPAUSE', NULL)
                        ->withOption('-dBATCH', NULL);
                $executor->execute($input, $output, $options);
            } catch (Webit\PHPgs\GhostScriptExecutionException $e)
            {
                $output = strtolower($e->output());
                if (preg_match('/\bfile requires a password for access\b/', $output))
                {
                    $err = 'password required';
                }
            } if (!empty($err))
            {
                $_view = $this->load->view('pdfviewer/password-protected', array(
                        "fileid" => $fileid ), TRUE);
            }
            else
            {
                $result = $this->generate($fileid, $srcfile, $convertedfile, $filename);

                $_view = $this->load->view('pdfviewer/image-viewer', $result, TRUE);
            }
        } $this->load->view('pdfviewer/index', array(
                "_view" => $_view ));
    }

    public function generate($fileid = FALSE, $srcfile = "", $convertedfile = "", $filename = "", $echo = false)
    {
        $_view = "";
        if (file_exists($convertedfile))
        {
            extract(config_item('filelocationdetails'));
            $cppath               = $jarpath . " technology.tabula.debug.Debug";
            $web_converted_folder = base_url() . "$webfolder/$fileid/";
            $debugcommand         = '"' . $java_home . '" -cp ' . $cppath . ' -p all -d "' . $convertedfile . '"';
            shell_exec($debugcommand);
            $converted_folder     .= $fileid;
            $jsonfile             = $converted_folder . "\\" . trim((basename($filename, ".pdf") . PHP_EOL)) . ".json";
            $jsonobj              = FALSE;
            if (file_exists($jsonfile))
            {
                $jsonobj    = json_decode(file_get_contents($jsonfile));
                $jarpdfpath = $convertedfile;
            }
            if (is_array($jsonobj) && count($jsonobj) === 0)
            {
                $tiffconvertedfile = $converted_folder . "\\" . trim((basename($filename, ".pdf") . PHP_EOL)) . ".tiff";
                $convertcommand    = '"' . $convert_exe_path . '" -density 400 "' . $srcfile . '" -depth 8 -strip -background white -alpha off "' . $tiffconvertedfile . '"';
                shell_exec($convertcommand);
                $onlyfilename      = trim((basename($filename, ".pdf") . PHP_EOL));
                $pdfpath           = dirname($convertedfile) . "\\$onlyfilename";
                $pdfcommand        = '"' . $tesseract_exe_path . '" -l eng "' . $tiffconvertedfile . '" "' . $pdfpath . '" pdf';
                shell_exec($pdfcommand);
                shell_exec($debugcommand);
            }
            $debugcommand = '"' . $java_home . '" -cp ' . $cppath . ' -p all "' . $convertedfile . '"';
            shell_exec($debugcommand);
            $file_name    = explode('.', $filename);





            $files = preg_grep('~' . $file_name[0] . '-1\.(jpeg|jpg|png)$~', scandir($converted_folder));
            $files = array_values($files);



            if ($echo)
            {
                $this->load->view('pdfviewer/index', array(
                        "_view" => $_view ), TRUE);
            }
            else
            {
                return array(
                        'files'  => $files,
                        "url"    => $web_converted_folder,
                        "fileid" => $fileid );
            }
        }
    }

    public function detecttable($fileid = FALSE)
    {
        if ($fileid)
        {
            $converted_folder = FCPATH . "upload\\converted\\$fileid";

            $file_data = $this->general_model->getRecords('*', 'file_details', array(
                    'file_id' => $fileid ));
            if (is_array($file_data[0]))
            {
                extract($file_data[0]);
                if (isset($file_name))
                {
                    $ext      = pathinfo($file_name, PATHINFO_EXTENSION);
                    $jsonfile = $converted_folder . "\\" . trim((basename($file_name, ".$ext") . PHP_EOL)) . ".json";

                    $jsonobj = FALSE;
                    if (file_exists($jsonfile))
                    {
                        $jsonobj = file_get_contents($jsonfile);
                    }
                    echo $jsonobj;
                }
            }
        }
    }

    private function DetectDelimiter($fh)
    {
        $data_1    = null;
        $data_2    = null;
        $delimiter = self::$delim_list['comma'];
        foreach (self::$delim_list as $key => $value)
        {
            $data_1    = fgetcsv($fh, 4096, $value);
            $delimiter = sizeof($data_1) > sizeof($data_2) ? $value : $delimiter;
            $data_2    = $data_1;
        }
        return $delimiter;
    }

    public function categorized_bank_statement()
    {
        $res    = json_decode($this->session->userdata('categorized_content'));
        $select = $this->input->post('ivalue');


        $file           = $this->general_model->getRecords('*', 'file_details', array(
                'file_id' => $this->input->post('file_id') ));
        $bank_statement = $this->general_model->getRecords('*', 'bank_statement', array(
                'file_id' => $this->input->post('file_id') ));

        $reference_no   = 0;
        $content_select = json_decode($this->session->userdata('content_select'));



        foreach ($content_select as $value)
        {
            if ($value == 'reference_no')
            {
                $reference_no = 1;
            }
        }
        if ($reference_no == 0)
        {
            array_push($content_select, 'reference_no');
        } $i    = 0;
        $j    = 0;
        $rows = array();
        foreach ($res as $row)
        {
            $records = array();
            $j       = 0;
            $amount  = '';
            $dr_cr   = '';
            foreach ($row as $value)
            {
                if ($content_select[$j] != '')
                {
                    if ($content_select[$j] == 'date')
                    {
                        if (preg_match('/[0-9]{0,2}\.[0-9]{0,2}\.[0-9]{2,4}/', trim($value), $matches))
                        {
                            $str   = 'd.m.Y';
                            $value = $matches[0];
                        }
                        elseif (preg_match('/[0-9]{0,2}\-[0-9]{0,2}\-[0-9]{2,4}/', trim($value), $matches))
                        {
                            $str   = 'd-m-Y';
                            $value = $matches[0];
                        }
                        elseif (preg_match('/[0-9]{0,2}\/[0-9]{0,2}\/[0-9]{2,4}/', trim($value), $matches))
                        {
                            $str   = 'd/m/Y';
                            $value = $matches[0];
                        }
                        elseif (preg_match('/[0-9]{0,2}\s[0-9]{0,2}\s[
                            0-9]{2,4}/', trim($value), $matches))
                        {
                            $str   = 'd m Y';
                            $value = $matches[0];
                        }
                        elseif (preg_match('/[0-9]{0,2}\.([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])\.[0-9]{2,4}/', trim($value), $matches) ||
                                preg_match('/[0-9]{0,2}\.([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])\.[0-9]{2,4}/', trim($value), $matches))
                        {
                            $str   = 'd.M.y';
                            $value = $matches[0];
                        }
                        elseif (preg_match('/[0-9]{0,2}\-([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])\-[0-9]{2,4}/', trim($value), $matches) ||
                                preg_match('/[0-9]{0,2}\-([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])\-[0-9]{2,4}/', trim($value), $matches))
                        {
                            $str   = 'd-M-y';
                            $value = $matches[0];
                        }
                        elseif (preg_match('/[0-9]{0,2}\/([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])\/[0-9]{2,4}/', trim($value), $matches) ||
                                preg_match('/[0-9]{0,2}\/([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])\/[0-9]{2,4}/', trim($value), $matches))
                        {
                            $str   = 'd/M/y';
                            $value = $matches[0];
                        }
                        elseif (preg_match('/[0-9]{0,2}\s([Jj][Aa][Nn]|[Ff][Ee][Bb]|[Mm][Aa][Rr]|[Aa][Pp][Rr]|[Mm][Aa][Yy]|[Jj][Uu][Nn]|[Jj][Uu][Ll]|[Aa][Uu][Gg]|[Ss][Ee][Pp]|[Oo][Cc][Tt]|[Nn][Oo][Vv]|[Dd][Ee][Cc])\s[0-9]{2,4}/', trim($value), $matches) ||
                                preg_match('/[0-9]{0,2}\s([Jj][Aa][Nn][Uu][Aa][Rr][Yy]|[Ff][Ee][Bb][Rr][Uu][Aa][Rr][Yy]|[Mm][Aa][Rr][Cc][Hh]|[Aa][Pp][Rr][Ii][Ll]|[Mm][Aa][Yy]|[Jj][Uu][Nn][Ee]|[Jj][Uu][Ll][Yy]|[Aa][Uu][Gg][Uu][Ss][Tt]|[Ss][Ee][Pp][Tt][Ee][Mm][Bb][Ee][Rr]|[Oo][Cc][Tt][Oo][Bb][Ee][Rr]|[Nn][Oo][Vv][Ee][Mm][Bb][Ee][Rr]|[Dd][Ee][Cc][Ee][Mm][Bb][Ee][Rr])\s[0-9]{2,4}/', trim($value), $matches))
                        {
                            $str   = 'd M y';
                            $value = $matches[0];
                        }
                        $value = DateTime::createFromFormat($str, $value)->format('y-m-d');
                        $date  = new DateTime($value);
                        $value = $date->format('Y-m-d');
                    }
                    if ($content_select[$j] == 'debit')
                    {
                        $value = str_replace(',', '', $value);
                    }
                    if ($content_select[$j] == 'credit')
                    {
                        $value = str_replace(',', '', $value);
                    }
                    if ($content_select[$j] == 'closing_balance')
                    {
                        $value = str_replace(',', '', $value);
                    }
                    if ($content_select[$j] == 'dr/cr')
                    {
                        $dr_cr = $value;
                        if ($amount != '')
                        {
                            if (trim($value) == 'DR')
                            {
                                $records += array(
                                        'debit' => $amount );
                                $records += array(
                                        'credit' => '0.00' );
                            }
                            if (trim($value) == 'CR')
                            {
                                $records += array(
                                        'debit' => '0.00' );
                                $records += array(
                                        'credit' => $amount );
                            }
                            $amount = '';
                        }
                    }

                    if ($content_select[$j] == 'amount')
                    {
                        $value  = str_replace(',', '', $value);
                        $amount = $value;
                        if ($dr_cr != '')
                        {
                            if (trim($dr_cr) == 'DR')
                            {
                                $records += array(
                                        'debit' => $amount );
                                $records += array(
                                        'credit' => '0.00' );
                            }
                            if (trim($dr_cr) == 'CR')
                            {
                                $records += array(
                                        'debit' => '0.00' );
                                $records += array(
                                        'credit' => $amount );
                            }
                            $dr_cr = '';
                        }
                    } if ($content_select[$j] != 'amount' && $content_select[$j] != 'dr/cr')
                    {
                        if ($content_select[$j] == 'debit' || $content_select[$j] == 'credit')
                        {
                            $debit_credit = sprintf("%d", $value);

                            if ($debit_credit == '0')
                            {
                                $records += array(
                                        $content_select[$j] => '0.00' );
                            }
                            else
                            {
                                $records += array(
                                        $content_select[$j] => $value );
                            }
                        }
                        else
                        {
                            $records += array(
                                    $content_select[$j] => $value );
                        }
                    }
                }
                $j++;
            }
            if ($reference_no == 0)
            {
                $records += array(
                        'reference_no' => NULL );
            }
            $records += array(
                    'import_by' => 'offline' );
            $records += array(
                    'file_id' => $this->input->post('file_id') );
            $records += array(
                    'bank_ledger_id' => $file[0]->ledger_id );
            $records += array(
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') );
            $records += array(
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID') );
            ksort($records);
            $rows[]  = $records;
            $i++;
        }

        $bank = array();
        foreach ($bank_statement as $row)
        {
            $bank2 = array();
            foreach ($row as $key => $value)
            {
                if ($key == 'date' || $key == 'description' || $key == 'reference_no' || $key == 'debit' || $key == 'credit' || $key == 'closing_balance' || $key == 'import_by' || $key == 'file_id' || $key == 'bank_ledger_id' || $key == 'financial_year_id' || $key == 'branch_id')
                    $bank2 += array(
                            $key => $value );
            }
            ksort($bank2);
            $bank[] = $bank2;
        }

        $final_records = array();
        $keys          = array();
        foreach ($rows as $key => $value)
        {
            foreach ($value as $k => $val)
            {
                $keys[] = $k;
            }
            break;
        }
        for ($m = 0; $m < sizeof($rows); $m++)
        {
            $duplicate_entry = 0;
            for ($a = 0; $a < sizeof($bank); $a++)
            {
                $increase = 0;
                for ($n = 0; $n < sizeof($rows[$m]); $n++)
                {
                    // echo $rows[$m][$keys[$n]].'=='.str_replace("\\\\n", "\\n", $bank[$a][$keys[$n]]).'<br>';
                    if (trim($rows[$m][$keys[$n]]) == trim(str_replace("\\\\n", "\\n", $bank[$a][$keys[$n]])))
                    {
                        // echo "hi".'<br>';
                        $increase++;
                    }
                }
                // echo $increase.'--'.sizeof($rows[$m]).'<br>';
                if ($increase == sizeof($rows[$m]))
                {
                    $duplicate_entry = 1;
                    break;
                }
            }
            // echo $duplicate_entry.'<br>';
            if ($duplicate_entry == 0)
            {
                for ($b = 0; $b < sizeof($rows[$m]); $b++)
                {
                    $final_records[$m][$keys[$b]] = $rows[$m][$keys[$b]];
                }
            }
        }
        $insert_records = array();
        $i              = 0;
        foreach ($final_records as $row)
        {
            foreach ($row as $key => $value)
            {
                $insert_records[$i][$key] = $value;
            }
            $i++;
        }
        // echo "<pre>";
        // print_r($keys);
        // print_r($bank);
        // print_r($rows);
        // print_r($insert_records);
        // exit;
        // foreach ($insert_records as $key => $value)
        // {
        // $where=array(
        //                 'bank_ledger_id'=>$value['bank_ledger_id'],
        //                 'closing_balance'=>$value['closing_balance'],
        //                 'credit'=>$value['credit'],
        //                 'date'=>$value['date'],
        //                 'debit'=>$value['debit'],
        //                 // 'description'=>$value['description'],
        //                 'file_id'=>$value['file_id'],
        //                 'financial_year_id'=>$value['financial_year_id'],
        //                 'import_by'=>$value['import_by'],
        //                 'reference_no'=>$value['reference_no'],
        //                 'branch_id'=>$this->session->userdata('SESS_BRANCH_ID'),
        //                 'delete_status'=>0
        //             );
        // $bank_statement_id=$this->general_model->getRecords('bank_statement_id','bank_statement',$where);
        // if($bank_statement_id)
        // {
        //     $this->general_model->updateData('bank_statement',array('delete_status'=>1),array('bank_statement_id'=>$bank_statement_id[0]->bank_statement_id));
        // }
        // $insert_records[$key]['branch_id']=$this->session->userdata('SESS_BRANCH_ID');
        // }
        // echo "<pre>";
        // print_r($insert_records);
        // exit;

        if ($insert_records)
        {
            $this->general_model->insertBatchData('bank_statement', $insert_records);
        }

        $data = array(
                'categorized_status' => '1' );
        $this->general_model->updateData('file_details', $data, array(
                'file_id' => $this->input->post('file_id') ));

        $this->general_model->deleteData('file_data', array(
                'file_id' => $this->input->post('file_id') ));
        redirect('bank_statement');
    }

    public function view_list_data()
    {
        $bank_ledger_id = $this->input->post('bank_ledger_id');

        $string                 = 'b.*,l.*';
        $table                  = 'bank_account b';
        $join['ledgers l']      = 'l.ledger_id=b.ledger_id';
        $where                  = array(
                'b.delete_status' => 0,
                'b.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') );
        $data['bank']           = $this->general_model->getJoinRecords($string, $table, $where, $join, $order                  = "");
        $data['bank_ledger_id'] = $bank_ledger_id;
        if ($bank_ledger_id == 'all')
        {
            $string            = 'f.*,l.ledger_title';
            $table             = 'file_details f';
            $join['ledgers l'] = 'l.ledger_id=f.ledger_id';
            $where             = array(
                    'f.delete_status'   => 0,
                    "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'f.branch_id'       => $this->session->userdata('SESS_BRANCH_ID') );
            $file_details      = $this->general_model->getJoinRecords($string, $table, $where, $join, $order             = "");
        }
        else
        {
            $string            = 'f.*,l.ledger_title';
            $table             = 'file_details f';
            $join['ledgers l'] = 'l.ledger_id=f.ledger_id';
            $where             = array(
                    'f.delete_status'   => 0,
                    "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'f.branch_id'       => $this->session->userdata('SESS_BRANCH_ID'),
                    'f.ledger_id'       => $bank_ledger_id );
            $file_details      = $this->general_model->getJoinRecords($string, $table, $where, $join, $order             = "");
        } $output = '';
        foreach ($file_details as $row)
        {
            if ($row->categorized_status == 0)
            {
                $color  = 'red';
                $a_text = '<a title="History" class="statement_history btn btn-xs btn-info" data-file_id="' . $row->file_id . '" data-target="#view_history" data-toggle="modal"><span class="fa fa-book"></span></a>';
            }
            else
            {
                $color  = 'green';
                $a_text = '';
            }
            $timestamp = mktime(0, 0, 0, $row->month, 1, 2011);
            $output    .= '<tr style="color: ' . $color . ';">
                <td>' . date('d-m-Y H:i:s', strtotime($row->added_date)) . '</td>
                <td>' . $row->ledger_title . '</td>
                <td>' . date("F", $timestamp) . '</td>
                <td>' . $a_text . '
                    <a title="View" class="btn btn-xs btn-warning" href="' . base_url() . 'bank_statement/showdata/' . $row->file_id . '"><span class="fa fa-eye">  </span></a>
                    <a title="Delete" class="delete_statement btn btn-xs btn-danger " href="" data-file_id="' . $row->file_id . '" data-target="#delete_file_details" data-toggle="modal"><span class="fa fa-trash"></span></a>
                </td>
            </tr>';
        }
        echo json_encode($output);
    }

    public function bank_group()
    {
        $bank_reconciliation_module_id = $this->config->item('bank_reconciliation_module');
        $data['module_id']             = $bank_reconciliation_module_id;
        $modules                       = $this->modules;
        $privilege                     = "view_privilege";
        $data['privilege']             = "view_privilege";
        $section_modules               = $this->get_section_modules($bank_reconciliation_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $string       = 'b.*,l.*';
        $from         = 'bank_account b';
        $join         = array(
                'ledgers l' => 'l.ledger_id = b.ledger_id' );
        $where        = array(
                'b.delete_status' => 0,
                'b.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') );
        $data['bank'] = $this->general_model->getJoinRecords($string, $from, $where, $join);

        //op
        $myFile2   = base_url() . "assets/json/type.json";
        $arr_data2 = array(); // create empty array

        try
        {
            //Get data from existing json file
            $jsondata2 = file_get_contents($myFile2);

            // converts json data into array
            $arr_data2 = json_decode($jsondata2, true);
            $this->session->set_userdata('arr_data2', $arr_data2);
        } catch (Exception $e)
        {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }


        $myFile3   = base_url() . "assets/json/type_d.json";
        $arr_data3 = array(); // create empty array

        try
        {
            //Get data from existing json file
            $jsondata3 = file_get_contents($myFile3);

            // converts json data into array
            $arr_data3 = json_decode($jsondata3, true);
            $this->session->set_userdata('arr_data3', $arr_data3);
        } catch (Exception $e)
        {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        $myFile4   = base_url() . "assets/json/type_q.json";
        $arr_data4 = array(); // create empty array

        try
        {
            //Get data from existing json file
            $jsondata4 = file_get_contents($myFile4);

            // converts json data into array
            $arr_data4 = json_decode($jsondata4, true);
            $this->session->set_userdata('arr_data4', $arr_data4);
        } catch (Exception $e)
        {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        $this->load->view('bank_statement/bank_group', $data);
    }

    public function list_data()
    {
        $bank_reconciliation_module_id = $this->config->item('bank_reconciliation_module');
        $data['module_id']             = $bank_reconciliation_module_id;
        $modules                       = $this->modules;
        $privilege                     = "view_privilege";
        $data['privilege']             = "view_privilege";
        $section_modules               = $this->get_section_modules($bank_reconciliation_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $string            = 'b.*,l.*';
        $table             = 'bank_account b';
        $join['ledgers l'] = 'l.ledger_id=b.ledger_id';
        $where             = array(
                'b.delete_status' => 0,
                'b.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') );
        $data['bank']      = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $bank_ledger_id = $this->input->post('bank_ledger_id');
        if ($this->input->post('from_date') == '')
        {
            $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));
            $month          = $this->input->post('month');

            if ($month == '01' || $month == '02' || $month == '03')
            {
                $year = $financial_year[1];
            }
            else
            {
                $year = $financial_year[0];
            }

            $last_day = date('t', strtotime($month . '/01/2018'));

            $from_date = $year . '-' . $month . '-01';
            $to_date   = $year . '-' . $month . '-' . $last_day;

            // $from_date = new DateTime($start_date)->format('Y-m-d');
            // $to_date = new DateTime($end_date)->format('Y-m-d');
        }
        else
        {
            $this->session->set_userdata('advance_search', 'true');
            $from_date = $this->input->post('from_date');
            $to_date   = $this->input->post('to_date');
        }

        $post_data = array(
                'bank_ledger_id' => $bank_ledger_id,
                'from_date'      => $from_date,
                'to_date'        => $to_date
        );

        $data['post_data'] = $post_data;
        // $data['bank_ledger_id'] = $bank_ledger_id;
        // $data['from_date'] = $from_date;
        // $data['to_date'] = $to_date;

        $this->session->set_userdata('bank_ledger_id', $bank_ledger_id);
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);

        $data['data'] = $this->list_raw_data();

        $data['data2'] = $this->list_categorized_data();

        $data['data3'] = $this->list_suspense_data();

        // $data['data2'] = $this->bank_statement_model->fetch_bank_statement2($post_data);

        $this->load->view('bank_statement/bank_group', $data);
    }

    public function list_data2()
    {
        // $data['bank'] = $this->payment_model->get_bank_data();
        $string            = 'b.*,l.*';
        $table             = 'bank_account b';
        $join['ledgers l'] = 'l.ledger_id=b.ledger_id';
        $where             = array(
                'b.delete_status' => 0,
                'b.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') );
        $data['bank']      = $this->general_model->getJoinRecords($string, $table, $where, $join, $order             = "");

        $post_data = array(
                'financial_year' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'bank_ledger_id' => $this->session->userdata('bank_ledger_id'),
                'from_date'      => $this->session->userdata('from_date'),
                'to_date'        => $this->session->userdata('to_date')
        );

        $data['post_data'] = $post_data;

        $data['data'] = $this->list_raw_data();

        $data['data2'] = $this->list_categorized_data();

        $data['data3'] = $this->list_suspense_data();

        $this->load->view('bank_statement/bank_group', $data);
    }

    public function list_data3()
    {
        // $data['bank'] = $this->payment_model->get_bank_data();
        $string            = 'b.*,l.*';
        $table             = 'bank_account b';
        $join['ledgers l'] = 'l.ledger_id=b.ledger_id';
        $where             = array(
                'b.delete_status' => 0,
                'b.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') );
        $data['bank']      = $this->general_model->getJoinRecords($string, $table, $where, $join, $order             = "");

        $to_date   = date('Y-m-d');
        $from_date = date('Y-m-d', strtotime("-2 months"));
        // $from_date=date('Y-m-d',strtotime("-2 months",date('Y-m-d')));

        $post_data = array(
                'financial_year' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'bank_ledger_id' => $this->input->post('bank_ledger_id'),
                'from_date'      => $from_date,
                'to_date'        => $to_date
        );

        $this->session->set_userdata('bank_ledger_id', $this->input->post('bank_ledger_id'));
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);

        $data['post_data'] = $post_data;

        $output[0] = $this->list_raw_data();

        $output[1] = $this->list_categorized_data();

        $output[2] = $this->list_suspense_data();

        echo json_encode($output);
    }

    public function list_raw_data()
    {
        $post_data = array(
                'bank_ledger_id' => $this->session->userdata('bank_ledger_id'),
                'from_date'      => $this->session->userdata('from_date'),
                'to_date'        => $this->session->userdata('to_date')
        );

        $condition = '(';
        $i         = 0;
        foreach ($post_data['bank_ledger_id'] as $key => $value)
        {
            if ($i == 0)
            {
                $condition .= 'bs.bank_ledger_id = ' . $value;
                $i         = 1;
            }
            else
            {
                $condition .= ' or bs.bank_ledger_id = ' . $value;
            }
        }
        $condition .= ')';

        $res2   = array();
        $string = 'bs.*';
        $from   = 'bank_statement bs';
        // $where  = array(
        //                'bs.bank_ledger_id'    => $post_data['bank_ledger_id'],
        //                'bs.date >='           => $post_data['from_date'],
        //                'bs.date <='           => $post_data['to_date'],
        //                'bs.delete_status'     => 0,
        //                'bs.category'          => 'raw_data',
        //                // 'bs.financial_year_id' => $this->session->userdata('financial_year_id')
        // );
        $where  = $condition . " and bs.date >= '" . $post_data['from_date'] . "' and bs.date <= '" . $post_data['to_date'] . "' and bs.delete_status = 0 and bs.category = 'raw_data'";

        $data = $this->general_model->getRecords($string, $from, $where);
        // echo "<pre>";
        // print_r($data);
        // exit;
        foreach ($data as $row)
        {
            if ($row->display_status == 0 && $row->suspense_status == 0)
            {
                if ($row->split_status == '1')
                {
                    $sub_stat = $this->general_model->getRecords('*', 'sub_statement', array(
                            'bank_statement_id' => $row->bank_statement_id ));

                    foreach ($sub_stat as $row2)
                    {
                        if ($row2->amount_type == 'debit' && $row2->display_status == 0 && $row2->suspense_status == 0)
                        {
                            $res    = array();
                            $res    = array(
                                    'sub_statement_id'  => $row2->sub_statement_id,
                                    'bank_statement_id' => $row->bank_statement_id,
                                    'date'              => $row->date,
                                    'description'       => $row->description,
                                    'reference_no'      => $row->reference_no,
                                    'debit'             => $row2->amount,
                                    'credit'            => $row->credit,
                                    'closing_balance'   => $row->closing_balance,
                                    'split_status'      => $row->split_status,
                                    'recon_status'      => $row2->recon_status
                            );
                            $res2[] = $res;
                        }
                        if ($row2->amount_type == 'credit' && $row2->display_status == 0 && $row2->suspense_status == 0)
                        {
                            $res    = array();
                            $res    = array(
                                    'sub_statement_id'  => $row2->sub_statement_id,
                                    'bank_statement_id' => $row->bank_statement_id,
                                    'date'              => $row->date,
                                    'description'       => $row->description,
                                    'reference_no'      => $row->reference_no,
                                    'debit'             => $row->debit,
                                    'credit'            => $row2->amount,
                                    'closing_balance'   => $row->closing_balance,
                                    'split_status'      => $row->split_status,
                                    'recon_status'      => $row2->recon_status
                            );
                            $res2[] = $res;
                        }
                    }
                }
                else
                {
                    $res    = array();
                    $res    = array(
                            'sub_statement_id'  => 0,
                            'bank_statement_id' => $row->bank_statement_id,
                            'date'              => $row->date,
                            'description'       => $row->description,
                            'reference_no'      => $row->reference_no,
                            'debit'             => $row->debit,
                            'credit'            => $row->credit,
                            'closing_balance'   => $row->closing_balance,
                            'split_status'      => $row->split_status,
                            'recon_status'      => $row->recon_status
                    );
                    $res2[] = $res;
                }
            }
        }
        $output = '';

        $rec         = 0;
        $st_id       = 0;
        $closing_bal = '';
        foreach ($res2 as $row)
        {
            $id     = $row['sub_statement_id'];
            $sid    = $row['bank_statement_id'];
            $amount = 0;
            $name   = '';
            if ($row['credit'] > 0)
            {
                $amount = $row['credit'];
            }
            elseif ($row['debit'] > 0)
            {
                $amount = $row['debit'];
            }

            if ($row['credit'] > 0)
            {
                $name = "credit";
            }
            elseif ($row['debit'] > 0)
            {
                $name = "debit";
            }

            $closing_bal = $row["closing_balance"];
            if ($row['split_status'] == 0)
            {
                if ($row['recon_status'] == 0)
                {
                    $li_recon = '<li>
                        <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-amount="' . $amount . '" data-name="' . $name . '" data-toggle="modal" data-target="#split_statement" class="split_statement"><i class="fa fa-columns" aria-hidden="true"></i>Split Statement</a>
                    </li>';
                }
                else
                {
                    $li_recon = '';
                }
                $li = '
                    <li>
                        <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-amount="' . $amount . '" data-toggle="modal" data-target="#move_to_categorized" class="open-rawdata"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>Move to Categorized</a>
                    </li>
                    <li>
                        <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-toggle="modal" data-target="#move_to_suspense" class="suspense_data"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>Move to Suspense</a>
                    </li>
                    <li>
                        <a href="' . base_url("bank_statement/general_add") . '/' . $sid . '/' . $id . '"><i class="fa fa-file-text-o"></i>Go To General Voucher</a>
                    </li>' . $li_recon;
            }
            else
            {
                if ($sid == $st_id)
                {
                    $rec = 1;
                }
                else
                {
                    $rec = 0;
                }
                if ($rec == 0)
                {
                    $rec        = 1;
                    $rec_status = $this->general_model->getRecords('*', 'sub_statement', array(
                            'bank_statement_id' => $sid ));
                    $res        = 0;
                    foreach ($rec_status as $raw)
                    {
                        if ($raw->recon_status == 1)
                        {
                            $res = 1;
                        }
                    }
                    if ($res == 0)
                    {
                        $li_recon = '<li>
                            <a href="" data-sid="' . $sid . '" data-amount="' . $amount . '" data-name="' . $name . '" data-toggle="modal" data-target="#merge_statement" class="merge_statement"><i class="fa fa-compress" aria-hidden="true"></i>Merge Statement</a>
                        </li>';
                    }
                    else
                    {
                        $li_recon = '';
                    }
                    $li = '
                        <li>
                            <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-amount="' . $amount . '" data-toggle="modal" data-target="#move_to_categorized" class="open-rawdata"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>Move to Categorized</a>
                        </li>
                        <li>
                            <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-toggle="modal" data-target="#move_to_suspense" class="suspense_data"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>Move to Suspense</a>
                        </li>
                        <li>
                            <a href="' . base_url("bank_statement/general_add") . '/' . $sid . '/' . $id . '"><i class="fa fa-file-text-o"></i>Go To General Voucher</a>
                        </li>' . $li_recon;
                }
                else
                {
                    $closing_bal = '';

                    $li = '
                        <li>
                            <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-amount="' . $amount . '" data-toggle="modal" data-target="#move_to_categorized" class="open-rawdata"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>Move to Categorized</a>
                        </li>
                        <li>
                            <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-toggle="modal" data-target="#move_to_suspense" class="suspense_data"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>Move to Suspense</a>
                        </li>
                        <li>
                            <a href="' . base_url("bank_statement/general_add") . '/' . $sid . '/' . $id . '"><i class="fa fa-file-text-o"></i>Go To General Voucher</a>
                        </li>';
                }
            }

            $st_id = $sid;
            if ($id == 0)
            {
                $bank    = $this->general_model->getRecords('*', 'bank_statement', array(
                        'bank_statement_id' => $sid ));
                $comment = $bank[0]->comment;
            }
            else
            {
                $sub_bank = $this->general_model->getRecords('*', 'sub_statement', array(
                        'sub_statement_id' => $id ));
                $comment  = $sub_bank[0]->comment;
            }
            $output .= '
                        <tr>

                            <td>' . $row["date"] . '</td>
                            <td>' . $row["description"] . '</td>
                            <td>' . $row["reference_no"] . '</td>
                            <td>' . $row["debit"] . '</td>
                            <td>' . $row["credit"] . '</td>
                            <td>' . $closing_bal . '</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-sm-default gropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">

                                        ' . $li . '
                                    <li>
                                        <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-comment="' . $comment . '" data-toggle="modal" data-target="#add_comment" class="add_comment"><i class="fa fa-commenting-o" aria-hidden="true"></i>Add/View Comment</a>
                                    </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>';
        }
        return $output;
    }

    public function list_categorized_data()
    {
        $post_data = array(
                'bank_ledger_id' => $this->session->userdata('bank_ledger_id'),
                'from_date'      => $this->session->userdata('from_date'),
                'to_date'        => $this->session->userdata('to_date')
        );

        $condition = '(';
        $i         = 0;
        foreach ($post_data['bank_ledger_id'] as $key => $value)
        {
            if ($i == 0)
            {
                $condition .= 'bs.bank_ledger_id = ' . $value;
                $i         = 1;
            }
            else
            {
                $condition .= ' or bs.bank_ledger_id = ' . $value;
            }
        }
        $condition .= ')';

        $res2   = array();
        // $data2 = $this->bank_statement_model->fetch_bank_statement($post_data);
        $string = 'bs.*';
        $from   = 'bank_statement bs';
        // $where  = array(
        //                'bs.bank_ledger_id'    => $post_data['bank_ledger_id'],
        //                'bs.date >='           => $post_data['from_date'],
        //                'bs.date <='           => $post_data['to_date'],
        //                'bs.delete_status'     => 0,
        //                'bs.category'          => 'raw_data',
        //                // 'bs.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID')
        // );
        $where  = $condition . " and bs.date >= '" . $post_data['from_date'] . "' and bs.date <= '" . $post_data['to_date'] . "' and bs.delete_status = 0 and bs.category = 'raw_data'";

        $data2 = $this->general_model->getRecords($string, $from, $where);
        // $cat_stat=$this->bank_statement_model->get_categorized_statement();

        foreach ($data2 as $row)
        {
            $res = array();
            if ($row->split_status == '1')
            {
                $sub_stat = $this->general_model->getRecords('*', 'sub_statement', array(
                        'bank_statement_id' => $row->bank_statement_id ));

                foreach ($sub_stat as $row2)
                {
                    $res = array();
                    if ($row2->display_status == 1)
                    {
                        if ($row2->amount_type == 'debit')
                        {
                            $res = array(
                                    'sub_statement_id'  => $row2->sub_statement_id,
                                    'bank_statement_id' => $row->bank_statement_id,
                                    'date'              => $row->date,
                                    'description'       => $row->description,
                                    'reference_no'      => $row->reference_no,
                                    'debit'             => $row2->amount,
                                    'credit'            => $row->credit,
                                    'closing_balance'   => $row->closing_balance,
                                    'split_status'      => $row->split_status,
                                    'recon_status'      => $row2->recon_status
                            );
                        }
                        if ($row2->amount_type == 'credit')
                        {
                            $res = array(
                                    'sub_statement_id'  => $row2->sub_statement_id,
                                    'bank_statement_id' => $row->bank_statement_id,
                                    'date'              => $row->date,
                                    'description'       => $row->description,
                                    'reference_no'      => $row->reference_no,
                                    'debit'             => $row->debit,
                                    'credit'            => $row2->amount,
                                    'closing_balance'   => $row->closing_balance,
                                    'split_status'      => $row->split_status,
                                    'recon_status'      => $row2->recon_status
                            );
                        }
                        $res2[] = $res;
                    }
                }
            }
            else
            {
                if ($row->display_status == 1)
                {
                    $res    = array(
                            'sub_statement_id'  => 0,
                            'bank_statement_id' => $row->bank_statement_id,
                            'date'              => $row->date,
                            'description'       => $row->description,
                            'reference_no'      => $row->reference_no,
                            'debit'             => $row->debit,
                            'credit'            => $row->credit,
                            'closing_balance'   => $row->closing_balance,
                            'split_status'      => $row->split_status,
                            'recon_status'      => $row->recon_status
                    );
                    $res2[] = $res;
                }
            }
        }
        $output = '';
        $st_id  = 0;
        foreach ($res2 as $row)
        {
            $id     = $row['sub_statement_id'];
            $sid    = $row['bank_statement_id'];
            $amount = 0;
            $name   = '';
            if ($row['credit'] > 0)
            {
                $amount = $row['credit'];
            }
            elseif ($row['debit'] > 0)
            {
                $amount = $row['debit'];
            }

            if ($row['credit'] > 0)
            {
                $name = "credit";
            }
            elseif ($row['debit'] > 0)
            {
                $name = "debit";
            }

            $closing_bal = $row["closing_balance"];
            if ($row['split_status'] == 1)
            {
                if ($sid == $st_id)
                {
                    $rec = 1;
                }
                else
                {
                    $rec = 0;
                }
                if ($rec == 0)
                {
                    $rec = 1;
                }
                else
                {
                    $closing_bal = '';
                }
            }

            $st_id = $sid;
            if ($row['recon_status'] == 0)
            {
                $li_recon = '<div class="dropdown">
                                <button type="button" class="btn btn-sm btn-sm-default gropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">

                                    <li>
                                        <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-toggle="modal" data-target="#remove_categorized" class="remove_categorized"><i class="fa fa-times" aria-hidden="true"></i>Remove Categorized</a>
                                    </li>

                                </ul>
                            </div>';
            }
            else
            {
                $li_recon = '';
            }
            $output .= '
                        <tr>

                            <td>' . $row["date"] . '</td>
                            <td>' . $row["description"] . '</td>
                            <td>' . $row["reference_no"] . '</td>
                            <td>' . $row["debit"] . '</td>
                            <td>' . $row["credit"] . '</td>
                            <td>' . $closing_bal . '</td>
                            <td>' . $li_recon . '</td>
                        </tr>';
        }
        return $output;
    }

    public function list_suspense_data()
    {
        $post_data = array(
                'bank_ledger_id' => $this->session->userdata('bank_ledger_id'),
                'from_date'      => $this->session->userdata('from_date'),
                'to_date'        => $this->session->userdata('to_date')
        );

        $condition = '(';
        $i         = 0;
        foreach ($post_data['bank_ledger_id'] as $key => $value)
        {
            if ($i == 0)
            {
                $condition .= 'bs.bank_ledger_id = ' . $value;
                $i         = 1;
            }
            else
            {
                $condition .= ' or bs.bank_ledger_id = ' . $value;
            }
        }
        $condition .= ')';

        $res2   = array();
        // $data3 = $this->bank_statement_model->fetch_bank_statement($post_data);
        $string = 'bs.*';
        $from   = 'bank_statement bs';
        // $where  = array(
        //                'bs.bank_ledger_id'    => $post_data['bank_ledger_id'],
        //                'bs.date >='           => $post_data['from_date'],
        //                'bs.date <='           => $post_data['to_date'],
        //                'bs.delete_status'     => 0,
        //                'bs.category'          => 'raw_data',
        //                'bs.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID')
        // );
        $where  = $condition . " and bs.date >= '" . $post_data['from_date'] . "' and bs.date <= '" . $post_data['to_date'] . "' and bs.delete_status = 0 and bs.category = 'raw_data'";

        $data3 = $this->general_model->getRecords($string, $from, $where);

        foreach ($data3 as $row)
        {
            if ($row->display_status == 0)
            {
                if ($row->split_status == '1')
                {
                    $sub_stat = $this->general_model->getRecords('*', 'sub_statement', array(
                            'bank_statement_id' => $row->bank_statement_id ));

                    foreach ($sub_stat as $row2)
                    {
                        if ($row2->suspense_status == 1)
                        {
                            if ($row2->amount_type == 'debit' && $row2->display_status == 0)
                            {
                                $res    = array();
                                $res    = array(
                                        'sub_statement_id'  => $row2->sub_statement_id,
                                        'bank_statement_id' => $row->bank_statement_id,
                                        'date'              => $row->date,
                                        'description'       => $row->description,
                                        'reference_no'      => $row->reference_no,
                                        'debit'             => $row2->amount,
                                        'credit'            => $row->credit,
                                        'closing_balance'   => $row->closing_balance,
                                        'split_status'      => $row->split_status,
                                        'recon_status'      => $row2->recon_status
                                );
                                $res2[] = $res;
                            }
                            if ($row2->amount_type == 'credit' && $row2->display_status == 0)
                            {
                                $res    = array();
                                $res    = array(
                                        'sub_statement_id'  => $row2->sub_statement_id,
                                        'bank_statement_id' => $row->bank_statement_id,
                                        'date'              => $row->date,
                                        'description'       => $row->description,
                                        'reference_no'      => $row->reference_no,
                                        'debit'             => $row->debit,
                                        'credit'            => $row2->amount,
                                        'closing_balance'   => $row->closing_balance,
                                        'split_status'      => $row->split_status,
                                        'recon_status'      => $row2->recon_status
                                );
                                $res2[] = $res;
                            }
                        }
                    }
                }
                else
                {
                    if ($row->suspense_status == 1)
                    {
                        $res    = array();
                        $res    = array(
                                'sub_statement_id'  => 0,
                                'bank_statement_id' => $row->bank_statement_id,
                                'date'              => $row->date,
                                'description'       => $row->description,
                                'reference_no'      => $row->reference_no,
                                'debit'             => $row->debit,
                                'credit'            => $row->credit,
                                'closing_balance'   => $row->closing_balance,
                                'split_status'      => $row->split_status,
                                'recon_status'      => $row->recon_status
                        );
                        $res2[] = $res;
                    }
                }
            }
        }
        $output = '';

        $rec         = 0;
        $st_id       = 0;
        $closing_bal = '';
        foreach ($res2 as $row)
        {
            $id     = $row['sub_statement_id'];
            $sid    = $row['bank_statement_id'];
            $amount = 0;
            $name   = '';
            if ($row['credit'] > 0)
            {
                $amount = $row['credit'];
            }
            elseif ($row['debit'] > 0)
            {
                $amount = $row['debit'];
            }

            if ($row['credit'] > 0)
            {
                $name = "credit";
            }
            elseif ($row['debit'] > 0)
            {
                $name = "debit";
            }

            $closing_bal = $row["closing_balance"];
            if ($row['split_status'] == 0)
            {
                if ($row['recon_status'] == 0)
                {
                    $li_recon = '<li>
                        <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-amount="' . $amount . '" data-name="' . $name . '" data-toggle="modal" data-target="#split_statement" class="split_statement"><i class="fa fa-columns" aria-hidden="true"></i>Split Statement</a>
                    </li>';
                }
                else
                {
                    $li_recon = '';
                }
                $li = '
                    <li>
                        <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-amount="' . $amount . '" data-toggle="modal" data-target="#move_to_categorized" class="open-rawdata"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>Move to Categorized</a>
                    </li>
                    <li>
                        <a href="' . base_url("bank_statement/general_add") . '/' . $sid . '/' . $id . '"><i class="fa fa-file-text-o"></i>Go To General Voucher</a>
                    </li>' . $li_recon;
            }
            else
            {
                if ($sid == $st_id)
                {
                    $rec = 1;
                }
                else
                {
                    $rec = 0;
                }
                if ($rec == 0)
                {
                    $rec        = 1;
                    // $rec_status = $this->bank_statement_model->get_recon_status($sid);
                    $rec_status = $this->general_model->getRecords('*', 'sub_statement', array(
                            'bank_statement_id' => $sid ));
                    $res        = 0;
                    foreach ($rec_status as $raw)
                    {
                        if ($raw->recon_status == 1)
                        {
                            $res = 1;
                        }
                    }
                    if ($res == 0)
                    {
                        $li_recon = '<li>
                            <a href="" data-sid="' . $sid . '" data-amount="' . $amount . '" data-name="' . $name . '" data-toggle="modal" data-target="#merge_statement" class="merge_statement"><i class="fa fa-compress" aria-hidden="true"></i>Merge Statement</a>
                        </li>';
                    }
                    else
                    {
                        $li_recon = '';
                    }
                    $li = '
                        <li>
                            <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-amount="' . $amount . '" data-toggle="modal" data-target="#move_to_categorized" class="open-rawdata"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>Move to Categorized</a>
                        </li>
                        <li>
                            <a href="' . base_url("bank_statement/general_add") . '/' . $sid . '/' . $id . '"><i class="fa fa-file-text-o"></i>Go To General Voucher</a>
                        </li>' . $li_recon;
                }
                else
                {
                    $closing_bal = '';

                    $li = '
                        <li>
                            <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-amount="' . $amount . '" data-toggle="modal" data-target="#move_to_categorized" class="open-rawdata"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>Move to Categorized</a>
                        </li>
                        <li>
                            <a href="' . base_url("bank_statement/general_add") . '/' . $sid . '/' . $id . '"><i class="fa fa-file-text-o"></i>Go To General Voucher</a>
                        </li>';
                }
            }

            $st_id = $sid;
            if ($id == 0)
            {
                $bank    = $this->general_model->getRecords('*', 'bank_statement', array(
                        'bank_statement_id' => $sid ));
                $comment = $bank[0]->comment;
            }
            else
            {
                $sub_bank = $this->general_model->getRecords('*', 'sub_statement', array(
                        'sub_statement_id' => $id ));
                $comment  = $sub_bank[0]->comment;
            }
            $output .= '
                        <tr>

                            <td>' . $row["date"] . '</td>
                            <td>' . $row["description"] . '</td>
                            <td>' . $row["reference_no"] . '</td>
                            <td>' . $row["debit"] . '</td>
                            <td>' . $row["credit"] . '</td>
                            <td>' . $closing_bal . '</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-sm-default gropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">

                                        ' . $li . '
                                        <li>
                                            <a href="" data-id="' . $id . '" data-sid="' . $sid . '" data-comment="' . $comment . '" data-toggle="modal" data-target="#add_comment" class="add_comment"><i class="fa fa-commenting-o" aria-hidden="true"></i>Add/View Comment</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>';
        }
        return $output;
    }

    public function get_question_answer()
    {
        $sid    = $this->input->post('sid');
        $id     = $this->input->post('id');
        $amount = $this->input->post('amount');

        $name = $this->input->post('name');

        $this->session->set_userdata('statement_id', $sid);
        $this->session->set_userdata('id', $id);
        $this->session->set_userdata('amount', $amount);

        $this->session->set_userdata('name', $name);

        $i    = 0;
        $cid  = 0;
        $arr2 = $this->session->userdata("arr_data2");

        foreach ($arr2 as $key => $value)
        {
            foreach ($value as $k => $v)
            {

                if (($k == "name") AND ( $v == $name))
                {

                    $cid = $arr2[$i]["cid"];
                }
            }
            $i++;
        }
        $this->session->set_userdata('cid', $cid);

        $arr4      = $this->session->userdata("arr_data4");
        $i         = 0;
        $output[0] = '';

        foreach ($arr4 as $key => $value)
        {
            foreach ($value as $k => $v)
            {

                if (($k == "cid") AND ( $v == $cid))
                {
                    $output[0] .= $arr4[$i]["name"];
                }
            }
            $i++;
        }

        $i    = 0;
        $type = '';

        foreach ($arr4 as $key => $value)
        {
            foreach ($value as $k => $v)
            {
                if (($k == "cid") AND ( $v == $cid))
                {
                    $type = $arr4[$i]["type"];
                }
            }
            $i++;
        }

        $this->session->set_userdata('cat_type', $type);

        $output[1] = '';
        if ($type == "suppliers" || $type == "expense")
        {
            // $res=$this->bank_statement_model->getSuppliers();
            $res       = $this->supplier_call();
            $output[1] .= '<div class="form-group">
                        <input type="hidden" id="category_type" name="category_type" value="' . $type . '">
                         <select class="form-control" id="suppliers" name="suppliers">
                            <option value="">Select</option>';

            foreach ($res as $row)
            {
                $output[1] .= '<option value="' . $row->supplier_id . '">' . $row->supplier_name . '</option>';
            }

            $output[1] .= '</select>
                         </div>';

            $amount          = $this->session->userdata('amount');
            $amount_per      = ($amount * 5) / 100;
            $receipt_amount  = $amount - $amount_per;
            $receipt_amount1 = $amount + $amount_per;

            $string = "currency_converted_amount, payment_id, voucher_status, voucher_date, voucher_number";
            $from   = "payment_voucher";
            // $where = "party_type='supplier' and voucher_status = '1' and currency_converted_amount <= ".$receipt_amount1." and currency_converted_amount >=".$receipt_amount." and (reference_type = 'purchase' or reference_type = 'expense') and financial_year_id = ".$this->session->userdata('SESS_FINANCIAL_YEAR_ID')." and branch_id = ".$this->session->userdata('SESS_BRANCH_ID')." and delete_status = 0";
            $where  = array(
                    'party_type'                   => 'supplier',
                    'reference_type'               => $type,
                    'voucher_status'               => 1,
                    'currency_converted_amount <=' => $receipt_amount1,
                    'currency_converted_amount >=' => $receipt_amount,
                    'financial_year_id'            => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'                    => $this->session->userdata('SESS_BRANCH_ID'),
                    'delete_status'                => 0
            );
            // if($type=='suppliers')
            // {
            //     $where['reference_type'] = 'purchase';
            // }
            // else if($type=='expense')
            // {
            //     $where['reference_type'] = 'expense';
            // }
            $order  = array(
                    'voucher_number' => 'asc' );
            $res    = $this->general_model->getRecords($string, $from, $where, $order);

            $output[2] = '';
            $output[2] .= '<div class="form-group">
                            <h4>Voucher List</h4>
                        <table border="1" class="table table-bordered table-striped dataTable">
                        <tr><th>Select</th>
                        <th>Voucher Date</th>
                         <th>Voucher Number</th>
                         <th>Reciept Amount</th>
                        </tr>';
            foreach ($res as $row)
            {
                $output[2] .= '<tr>
                            <td><label class="form-check-label"><input class="checkbox" type="checkbox" name="checkbox"
                                    value="' . $row->payment_id . '"></label></td>
                            <td>' . $row->voucher_date . '</td>
                            <td>' . $row->voucher_number . '</td>
                            <td>' . $row->currency_converted_amount . '</td>
                          </tr>';
            }

            $output[2] .= "</table></div>";
        }

        if ($type == "customer" || $type == "customer_advance" || $type == "customer_refund")
        {
            $res       = $this->customer_call();
            $output[1] .= '<div class="form-group">
                        <select class="form-control" id="customer" name="customer">
                        <option value="">Select</option>';

            foreach ($res as $row)
            {
                $output[1] .= '<option value="' . $row->customer_id . '">' . $row->customer_name . '</option>';
            }

            $output[1] .= '</select>
                         </div>';

            $id = $this->input->post('id');

            $amount          = $this->session->userdata('amount');
            $amount_per      = ($amount * 5) / 100;
            $receipt_amount  = $amount - $amount_per;
            $receipt_amount1 = $amount + $amount_per;

            $output[2] = '';
            $output[2] .= '<div class="form-group">
                            <h4>Voucher List</h4>
                        <table border="1" class="table table-bordered table-striped dataTable">
                        <tr><th>Select</th>
                            <th>Voucher Date</th>
                            <th>Voucher Number</th>
                            <th>Reciept Amount</th>
                        </tr>';

            if ($this->session->userdata('cat_type') == "customer")
            {
                $string = "currency_converted_amount, receipt_id, voucher_status, voucher_date, voucher_number";
                $from   = "receipt_voucher";
                $where  = array(
                        'party_type'                   => 'customer',
                        'reference_type'               => 'sales',
                        'voucher_status'               => 1,
                        'currency_converted_amount <=' => $receipt_amount1,
                        'currency_converted_amount >=' => $receipt_amount,
                        'financial_year_id'            => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        'branch_id'                    => $this->session->userdata('SESS_BRANCH_ID'),
                        'delete_status'                => 0
                );
                $order  = array(
                        'voucher_number' => 'asc' );
                $res    = $this->general_model->getRecords($string, $from, $where, $order);

                foreach ($res as $row)
                {
                    $output[2] .= '<tr>
                                <td>
                                <label class="form-check-label"><input class="checkbox" type="checkbox" name="checkbox" value="' . $row->receipt_id . '"></label></td>
                                <td>' . $row->voucher_date . '</td>
                                <td>' . $row->voucher_number . '</td>
                                <td>' . $row->currency_converted_amount . '</td>
                            </tr>';
                }
            }

            if ($this->session->userdata('cat_type') == "customer_advance")
            {
                $string = "currency_converted_amount, advance_id, voucher_status, voucher_date, voucher_number";
                $from   = "advance_voucher";
                $where  = array(
                        'party_type'                   => 'customer',
                        // 'reference_type' => 'sales',
                        'voucher_status'               => 1,
                        'currency_converted_amount <=' => $receipt_amount1,
                        'currency_converted_amount >=' => $receipt_amount,
                        'financial_year_id'            => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        'branch_id'                    => $this->session->userdata('SESS_BRANCH_ID'),
                        'delete_status'                => 0
                );
                $order  = array(
                        'voucher_number' => 'asc' );
                $res    = $this->general_model->getRecords($string, $from, $where, $order);

                foreach ($res as $row)
                {
                    $output[2] .= '<tr>
                                <td>
                                <label class="form-check-label"><input class="checkbox" type="checkbox" name="checkbox" value="' . $row->advance_id . '"></label></td>
                                <td>' . $row->voucher_date . '</td>
                                <td>' . $row->voucher_number . '</td>
                                <td>' . $row->currency_converted_amount . '</td>
                            </tr>';
                }
            }

            if ($this->session->userdata('cat_type') == "customer_refund")
            {
                $string = "currency_converted_amount, refund_id, voucher_status, voucher_date, voucher_number";
                $from   = "refund_voucher";
                $where  = array(
                        'party_type'                   => 'customer',
                        // 'reference_type' => 'sales',
                        'voucher_status'               => 1,
                        'currency_converted_amount <=' => $receipt_amount1,
                        'currency_converted_amount >=' => $receipt_amount,
                        'financial_year_id'            => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        'branch_id'                    => $this->session->userdata('SESS_BRANCH_ID'),
                        'delete_status'                => 0
                );
                $order  = array(
                        'voucher_number' => 'asc' );
                $res    = $this->general_model->getRecords($string, $from, $where, $order);

                foreach ($res as $row)
                {
                    $output[2] .= '<tr>
                                <td>
                                <label class="form-check-label"><input class="checkbox" type="checkbox" name="checkbox" value="' . $row->refund_id . '"></label></td>
                                <td>' . $row->voucher_date . '</td>
                                <td>' . $row->voucher_number . '</td>
                                <td>' . $row->currency_converted_amount . '</td>
                            </tr>';
                }
            }

            $output[2] .= "</table></div>";
        }

        if ($type == "loan")
        {
            $output[1] .= '<div class="form-group">
                        <input type="hidden" id="category_type" name="category_type" value="' . $type . '">
                        <select class="form-control" id="general_type" name="general_type">
                        <option value="">Select</option>';

            $output[1] .= '<option value="loan_borrowed">Loan Borrowed</option>
                        <option value="loan_paid">Loan Paid</option>
                        <option value="emi_paid">Instalment / EMI Paid</option>';

            $output[1] .= '</select>
                         </div>';

            $output[2] = '';
        }

        if ($type == "indirect_income")
        {
            $output[1] .= '<div class="form-group">
                        <input type="hidden" id="category_type" name="category_type" value="' . $type . '">
                        <select class="form-control" id="general_type" name="general_type">
                        <option value="">Select</option>';

            $output[1] .= '<option value="indirect_income">Indirect Income</option>';

            $output[1] .= '</select>
                         </div>';

            $output[2] = '';
        }

        if ($type == "capital")
        {
            $output[1] .= '<div class="form-group">
                        <input type="hidden" id="category_type" name="category_type" value="' . $type . '">
                        <select class="form-control" id="general_type" name="general_type">
                        <option value="">Select</option>';

            $output[1] .= '<option value="capital_invested">Capital Invested</option>
                        <option value="additional_capital_invested">Additional capital Invested</option>
                        <option value="capital_withdrawn">Capital Withdrawn</option>';

            $output[1] .= '</select>
                         </div>';

            $output[2] = '';
        }

        // if($type=="cash")
        // {
        //     $output[1].='<div class="form-group">
        //                 <input type="hidden" id="category_type" name="category_type" value="'.$type.'">
        //                 <select class="form-control" id="general_type" name="general_type">
        //                 <option value="">Select</option>';
        //     $output[1].='<option value="cash_deposit">Cash Deposit in Bank</option>
        //                 <option value="cash_withdrawal">Cash Withdrawal from Bank</option>
        //                 <option value="cash_receipt">Cash Receipt</option>
        //                 <option value="cash_payment">Cash Payment</option>';
        //     $output[1].='</select>
        //                  </div>';
        //     $output[2] = '';
        // }

        if ($type == "investment")
        {
            $output[1] .= '<div class="form-group">
                        <input type="hidden" id="category_type" name="category_type" value="' . $type . '">
                        <select class="form-control" id="general_type" name="general_type">
                        <option value="">Select</option>';

            $output[1] .= '<option value="investment_made">Investment made</option>
                        <option value="investment_withdraw">Investment withdraw / sold / redeem / mature</option>';

            $output[1] .= '</select>
                        </div>';

            $output[2] = '';
        }

        if ($type == "deposit")
        {
            $output[1] .= '<div class="form-group">
                        <input type="hidden" id="category_type" name="category_type" value="' . $type . '">
                        <select class="form-control" id="general_type" name="general_type">
                        <option value="">Select</option>';

            $output[1] .= '<option value="deposit_made">Deposit Made</option>
                        <option value="deposit_withdraw">Deposit Withdraw</option>';

            $output[1] .= '</select>
                        </div>';

            $output[2] = '';
        }

        if ($type == "duties_taxes")
        {
            $output[1] .= '<div class="form-group">
                        <input type="hidden" id="category_type" name="category_type" value="' . $type . '">
                        <select class="form-control" id="general_type" name="general_type">
                        <option value="">Select</option>';

            $output[1] .= '<option value="tax_receivable">Tax Receivable</option>
                        <option value="tax_payable">Tax Payable</option>';

            $output[1] .= '</select>
                        </div>';

            $output[2] = '';
        }

        if ($type == "bank_to_bank")
        {
            $output[1] .= '<div class="form-group">
                        <input type="hidden" id="category_type" name="category_type" value="' . $type . '">
                        <select class="form-control" id="general_type" name="general_type">
                        <option value="">Select</option>';

            $output[1] .= '<option value="bank_to_bank">Bank to Bank</option>';

            $output[1] .= '</select>
                        </div>';

            $output[2] = '';
        }

        if ($type == "advance")
        {
            $output[1] .= '<div class="form-group">
                        <input type="hidden" id="category_type" name="category_type" value="' . $type . '">
                        <select class="form-control" id="general_type" name="general_type">
                        <option value="">Select</option>';

            $output[1] .= '<option value="advance_taken">Advance Taken</option>
                        <option value="advance_given">Advance Given</option>
                        <option value="advance_tax_paid">Advance Tax Paid</option>
                        <option value="payment_advance_taken">Payment of Advance Taken</option>
                        <option value="receipt_advance_given">Receipt of Advance Given</option>';

            $output[1] .= '</select>
                        </div>';

            $output[2] = '';
        }

        if ($type == "fixed_asset")
        {
            $output[1] .= '<div class="form-group">
                        <input type="hidden" id="category_type" name="category_type" value="' . $type . '">
                        <select class="form-control" id="general_type" name="general_type">
                        <option value="">Select</option>';

            $output[1] .= '<option value="fixed_asset_purchase">Fixed asset purchase</option>
                        <option value="fixed_asset_sold">Fixed asset sold / disposed</option>';

            $output[1] .= '</select>
                        </div>';

            $output[2] = '';
        }

        echo json_encode($output);
    }

    public function getCategory($term)
    {
        $cname = array();

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
        $i = 0;
        foreach ($arr_data as $key => $value)
        {

            $cname[] = $arr_data[$i]['name'];

            $i++;
        }


        echo json_encode($cname);
    }

    public function getSubCategory($term)
    {
        if ($this->session->userdata('name') == 'Expense')
        {

        }
        else
        {

            $cname = array();

            $myFile   = base_url() . "assets/json/type_d.json";
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
            $i = 0;
            foreach ($arr_data as $key => $value)
            {
                foreach ($value as $k => $v)
                {
                    if (($k == "cid") AND ( $v == $this->session->userdata('cid')))
                    {
                        $cname[] = $arr_data[$i]['name'];
                    }
                }
                $i++;
            }
        }
        echo json_encode($cname);
    }

    public function get_customer_invoices()
    {
        $id = $this->input->post('id');

        $amount          = $this->session->userdata('amount');
        $amount_per      = ($amount * 5) / 100;
        $receipt_amount  = $amount - $amount_per;
        $receipt_amount1 = $amount + $amount_per;

        $output = '';
        $output .= '<div class="form-group">
                        <h4>Voucher List</h4>
                    <table border="1" class="table table-bordered table-striped dataTable">
                    <tr><th>Select</th>
                        <th>Voucher Date</th>
                        <th>Voucher Number</th>
                        <th>Reciept Amount</th>
                    </tr>';

        if ($this->session->userdata('cat_type') == "customer")
        {
            $string = "currency_converted_amount, receipt_id, voucher_status, voucher_date, voucher_number";
            $from   = "receipt_voucher";
            $where  = array(
                    'party_id'                     => $id,
                    'party_type'                   => 'customer',
                    'reference_type'               => 'sales',
                    'voucher_status'               => 1,
                    'currency_converted_amount <=' => $receipt_amount1,
                    'currency_converted_amount >=' => $receipt_amount,
                    'financial_year_id'            => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'                    => $this->session->userdata('SESS_BRANCH_ID'),
                    'delete_status'                => 0
            );
            $order  = array(
                    'voucher_number' => 'asc' );
            $res    = $this->general_model->getRecords($string, $from, $where, $order);

            foreach ($res as $row)
            {
                $output .= '<tr>
                            <td>
                            <label class="form-check-label"><input class="checkbox" type="checkbox" name="checkbox" value="' . $row->receipt_id . '"></label></td>
                            <td>' . $row->voucher_date . '</td>
                            <td>' . $row->voucher_number . '</td>
                            <td>' . $row->currency_converted_amount . '</td>
                        </tr>';
            }
        }

        if ($this->session->userdata('cat_type') == "customer_advance")
        {
            $string = "currency_converted_amount, advance_id, voucher_status, voucher_date, voucher_number";
            $from   = "advance_voucher";
            $where  = array(
                    'party_id'                     => $id,
                    'party_type'                   => 'customer',
                    // 'reference_type' => 'sales',
                    'voucher_status'               => 1,
                    'currency_converted_amount <=' => $receipt_amount1,
                    'currency_converted_amount >=' => $receipt_amount,
                    'financial_year_id'            => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'                    => $this->session->userdata('SESS_BRANCH_ID'),
                    'delete_status'                => 0
            );
            $order  = array(
                    'voucher_number' => 'asc' );
            $res    = $this->general_model->getRecords($string, $from, $where, $order);

            foreach ($res as $row)
            {
                $output .= '<tr>
                            <td>
                            <label class="form-check-label"><input class="checkbox" type="checkbox" name="checkbox" value="' . $row->advance_id . '"></label></td>
                            <td>' . $row->voucher_date . '</td>
                            <td>' . $row->voucher_number . '</td>
                            <td>' . $row->currency_converted_amount . '</td>
                        </tr>';
            }
        }

        if ($this->session->userdata('cat_type') == "customer_refund")
        {
            $string = "currency_converted_amount, refund_id, voucher_status, voucher_date, voucher_number";
            $from   = "refund_voucher";
            $where  = array(
                    'party_id'                     => $id,
                    'party_type'                   => 'customer',
                    // 'reference_type' => 'sales',
                    'voucher_status'               => 1,
                    'currency_converted_amount <=' => $receipt_amount1,
                    'currency_converted_amount >=' => $receipt_amount,
                    'financial_year_id'            => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'                    => $this->session->userdata('SESS_BRANCH_ID'),
                    'delete_status'                => 0
            );
            $order  = array(
                    'voucher_number' => 'asc' );
            $res    = $this->general_model->getRecords($string, $from, $where, $order);

            foreach ($res as $row)
            {
                $output .= '<tr>
                            <td>
                            <label class="form-check-label"><input class="checkbox" type="checkbox" name="checkbox" value="' . $row->refund_id . '"></label></td>
                            <td>' . $row->voucher_date . '</td>
                            <td>' . $row->voucher_number . '</td>
                            <td>' . $row->currency_converted_amount . '</td>
                        </tr>';
            }
        }

        $output .= "</table></div>";

        echo json_encode($output);
    }

    public function get_suppliers_expense_invoices()
    {
        $id              = $this->input->post('id');
        $category_type   = $this->input->post('category_type');
        // $res=$this->bank_statement_model->getSuppliersInvoices($id);
        $amount          = $this->session->userdata('amount');
        $amount_per      = ($amount * 5) / 100;
        $receipt_amount  = $amount - $amount_per;
        $receipt_amount1 = $amount + $amount_per;

        $string = "currency_converted_amount, payment_id, voucher_status, voucher_date, voucher_number";
        $from   = "payment_voucher";
        $where  = array(
                'party_id'                     => $id,
                'party_type'                   => 'supplier',
                // 'reference_type' => $category_type,
                'voucher_status'               => 1,
                'currency_converted_amount <=' => $receipt_amount1,
                'currency_converted_amount >=' => $receipt_amount,
                'financial_year_id'            => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id'                    => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status'                => 0
        );
        if ($category_type == 'suppliers')
        {
            $where['reference_type'] = 'purchase';
        }
        else if ($category_type == 'expense')
        {
            $where['reference_type'] = 'expense';
        }
        $order = array(
                'voucher_number' => 'asc' );
        $res   = $this->general_model->getRecords($string, $from, $where, $order);

        $output = '';
        // $output.='<div class="form-group">
        //             <h4>Invoice List</h4>
        //             <table border="1" class="table table-bordered table-striped dataTable">
        //             <tr>
        //                 <th>Select</th>
        //                 <th>Invoice Date</th>
        //                 <th>Voucher Date</th>
        //                 <th>Invoice No.</th>
        //                 <th>Grand Total</th>
        //                 <th>Paid Amount</th>
        //                 <th>Remaining Amount</th>
        //             </tr>';
        // foreach ($res as $row)
        // {
        //     if($row->voucher_status==0 || $row->voucher_status==3)
        //     {
        //         $remaining_amount=sprintf('%0.2f', ($row->receipt_amount-$row->paid_amount));
        //         $output.='<tr style="color:red;">
        //                 <td><label class="form-check-label"><input class="checkbox_v" type="checkbox" name="checkbox_v"
        //                         value="'.$row->id.':'.$row->purchase_id.'"></label></td>
        //                 <td>'.$row->date.'</td>
        //                 <td>'.$row->voucher_date.'</td>
        //                 <td><a href="'.base_url('purchase/view/').$row->purchase_id.'">'.$row->reference_no.'</a>
        //                 </td>
        //                 <td>'.$row->inv_total.'</td>
        //                 <td>'.$row->paid_amount.'</td>
        //                 <td>'.$remaining_amount.'</td>
        //               </tr>';
        //     }
        //     else
        //     {
        //     }
        // }
        // $output.="</table></div>";
        $output .= '<div class="form-group">
                        <h4>Voucher List</h4>
                    <table border="1" class="table table-bordered table-striped dataTable">
                    <tr><th>Select</th>
                    <th>Voucher Date</th>
                     <th>Voucher Number</th>
                     <th>Reciept Amount</th>
                    </tr>';
        foreach ($res as $row)
        {
            $output .= '<tr>
                        <td><label class="form-check-label"><input class="checkbox" type="checkbox" name="checkbox"
                                value="' . $row->payment_id . '"></label></td>
                        <td>' . $row->voucher_date . '</td>
                        <td>' . $row->voucher_number . '</td>
                        <td>' . $row->currency_converted_amount . '</td>
                      </tr>';
        }

        $output .= "</table></div>";

        echo json_encode($output);
    }

    public function get_general_voucher()
    {
        $general_type    = $this->input->post('general_type');
        $category_type   = $this->input->post('category_type');
        // $res=$this->bank_statement_model->getSuppliersInvoices($id);
        $amount          = $this->session->userdata('amount');
        $amount_per      = ($amount * 5) / 100;
        $receipt_amount  = $amount - $amount_per;
        $receipt_amount1 = $amount + $amount_per;

        $output = '';
        if ($general_type == 'loan_paid' || $general_type == 'emi_paid' || $general_type == 'capital_withdrawn' || $general_type == 'investment_made' || $general_type == 'deposit_made' || $general_type == 'tax_payable' || $general_type == 'advance_given' || $general_type == 'payment_advance_taken' || $general_type == 'advance_tax_paid' || $general_type == 'fixed_asset_purchase')
        {
            $this->session->set_userdata('voucher_type', 'payment_voucher');
            $string = "pv.currency_converted_amount, pv.payment_id, pv.voucher_status, pv.voucher_date, pv.voucher_number";
            $from   = "payment_voucher pv";
            $join   = array(
                    'general_bill g' => 'g.general_bill_id=pv.reference_id' );
            $where  = array(
                    // 'party_id' => $id,
                    // 'g.purpose_of_transaction' => 'Loan',
                    // 'g.type_of_transaction' => 'Loan Paid',
                    'pv.party_type'                   => 'ledger',
                    'pv.reference_type'               => 'general_bill',
                    'pv.voucher_status'               => 1,
                    'pv.currency_converted_amount <=' => $receipt_amount1,
                    'pv.currency_converted_amount >=' => $receipt_amount,
                    'pv.financial_year_id'            => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'pv.branch_id'                    => $this->session->userdata('SESS_BRANCH_ID'),
                    'pv.delete_status'                => 0
            );
            if ($general_type == 'loan_paid')
            {
                $where['g.purpose_of_transaction'] = 'Loan';
                $where['g.type_of_transaction']    = 'Loan Paid';
            }
            else if ($general_type == 'emi_paid')
            {
                $where['g.purpose_of_transaction'] = 'Loan';
                $where['g.type_of_transaction']    = 'Instalment or EMI Paid';
            }
            // else if ($general_type == 'capital_withdrawn')
            // {
            //     $where['g.purpose_of_transaction'] = 'Capital';
            //     $where['g.type_of_transaction'] = 'Capital Withdrawn';
            // }
            // else if ($general_type == 'cash_payment')
            // {
            //     $where['g.purpose_of_transaction'] = 'Cash';
            //     $where['g.type_of_transaction'] = 'cash payment';
            // }
            else if ($general_type == 'investment_made')
            {
                $where['g.purpose_of_transaction'] = 'Investment';
                $where['g.type_of_transaction']    = 'Investment Made';
            }
            else if ($general_type == 'deposit_made')
            {
                $where['g.purpose_of_transaction'] = 'Deposit';
                $where['g.type_of_transaction']    = 'Deposit Made';
            }
            else if ($general_type == 'tax_payable')
            {
                $where['g.purpose_of_transaction'] = 'Duties & Taxes';
                $where['g.type_of_transaction']    = 'Tax Payable';
            }
            else if ($general_type == 'advance_given')
            {
                $where['g.purpose_of_transaction'] = 'Advance';
                $where['g.type_of_transaction']    = 'Advance Given';
            }
            else if ($general_type == 'payment_advance_taken')
            {
                $where['g.purpose_of_transaction'] = 'Advance';
                $where['g.type_of_transaction']    = 'Payment of Advance Taken';
            }
            else if ($general_type == 'advance_tax_paid')
            {
                $where['g.purpose_of_transaction'] = 'Advance';
                $where['g.type_of_transaction']    = 'Advance Tax Paid';
            }
            else if ($general_type == 'fixed_asset_purchase')
            {
                $where['g.purpose_of_transaction'] = 'Fixed Assets';
                $where['g.type_of_transaction']    = 'Fixed Asset Purchase';
            }

            $order = array(
                    'pv.voucher_number' => 'asc' );
            $res   = $this->general_model->getJoinRecords($string, $from, $where, $join, $order);

            $output = '';
            $output .= '<div class="form-group">
                            <h4>Voucher List</h4>
                        <table border="1" class="table table-bordered table-striped dataTable">
                        <tr><th>Select</th>
                        <th>Voucher Date</th>
                         <th>Voucher Number</th>
                         <th>Reciept Amount</th>
                        </tr>';
            foreach ($res as $row)
            {
                $output .= '<tr>
                            <td><label class="form-check-label"><input class="checkbox" type="checkbox" name="checkbox"
                                    value="' . $row->payment_id . '"></label></td>
                            <td>' . $row->voucher_date . '</td>
                            <td>' . $row->voucher_number . '</td>
                            <td>' . $row->currency_converted_amount . '</td>
                          </tr>';
            }
            $output .= "</table></div>";
        }
        else if ($general_type == 'loan_borrowed' || $general_type == 'indirect_income' || $general_type == 'capital_invested' || $general_type == 'additional_capital_invested' || $general_type == 'investment_withdraw' || $general_type == 'deposit_withdraw' || $general_type == 'tax_receivable' || $general_type == 'advance_taken' || $general_type == 'receipt_advance_given' || $general_type == 'fixed_asset_sold')
        {
            $this->session->set_userdata('voucher_type', 'receipt_voucher');
            $string = "rv.currency_converted_amount, rv.receipt_id, rv.voucher_status, rv.voucher_date, rv.voucher_number";
            $from   = "receipt_voucher rv";
            $join   = array(
                    'general_bill g' => 'g.general_bill_id=rv.reference_id' );
            $where  = array(
                    // 'party_id' => $id,
                    // 'g.purpose_of_transaction' => 'Loan',
                    'rv.party_type'                   => 'ledger',
                    'rv.reference_type'               => 'general_bill',
                    'rv.voucher_status'               => 1,
                    'rv.currency_converted_amount <=' => $receipt_amount1,
                    'rv.currency_converted_amount >=' => $receipt_amount,
                    'rv.financial_year_id'            => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'rv.branch_id'                    => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.delete_status'                => 0
            );
            if ($general_type == 'loan_borrowed')
            {
                $where['g.purpose_of_transaction'] = 'Loan';
                $where['g.type_of_transaction']    = 'Loan Borrowed';
            }
            elseif ($general_type == 'indirect_income')
            {
                $where['g.purpose_of_transaction'] = 'Indirect Income';
            }
            else if ($general_type == 'capital_invested')
            {
                $where['g.purpose_of_transaction'] = 'Capital';
                $where['g.type_of_transaction']    = 'Capital Invested';
            }
            else if ($general_type == 'additional_capital_invested')
            {
                $where['g.purpose_of_transaction'] = 'Capital';
                $where['g.type_of_transaction']    = 'Additional Capital Invested';
            }
            // else if ($general_type == 'cash_receipt')
            // {
            //     $where['g.purpose_of_transaction'] = 'Cash';
            //     $where['g.type_of_transaction'] = 'cash receipt';
            // }
            else if ($general_type == 'investment_withdraw')
            {
                $where['g.purpose_of_transaction'] = 'Investment';
                $where['g.type_of_transaction']    = 'Investment Withdraw / Sold / Redeem / Mature';
            }
            else if ($general_type == 'deposit_withdraw')
            {
                $where['g.purpose_of_transaction'] = 'Deposit';
                $where['g.type_of_transaction']    = 'Deposit Withdraw';
            }
            else if ($general_type == 'tax_receivable')
            {
                $where['g.purpose_of_transaction'] = 'Duties & Taxes';
                $where['g.type_of_transaction']    = 'Tax Receivable';
            }
            else if ($general_type == 'advance_taken')
            {
                $where['g.purpose_of_transaction'] = 'Advance';
                $where['g.type_of_transaction']    = 'Advance Taken';
            }
            else if ($general_type == 'receipt_advance_given')
            {
                $where['g.purpose_of_transaction'] = 'Advance';
                $where['g.type_of_transaction']    = 'Receipt of Advance Given';
            }
            else if ($general_type == 'fixed_asset_sold')
            {
                $where['g.purpose_of_transaction'] = 'Fixed Assets';
                $where['g.type_of_transaction']    = 'Fixed Asset Sold or Disposed';
            }

            $order = array(
                    'rv.voucher_number' => 'asc' );
            $res   = $this->general_model->getJoinRecords($string, $from, $where, $join, $order);

            $output = '';
            $output .= '<div class="form-group">
                            <h4>Voucher List</h4>
                        <table border="1" class="table table-bordered table-striped dataTable">
                        <tr><th>Select</th>
                        <th>Voucher Date</th>
                         <th>Voucher Number</th>
                         <th>Reciept Amount</th>
                        </tr>';
            foreach ($res as $row)
            {
                $output .= '<tr>
                            <td><label class="form-check-label"><input class="checkbox" type="checkbox" name="checkbox"
                                    value="' . $row->receipt_id . '"></label></td>
                            <td>' . $row->voucher_date . '</td>
                            <td>' . $row->voucher_number . '</td>
                            <td>' . $row->currency_converted_amount . '</td>
                          </tr>';
            }
            $output .= "</table></div>";
        }
        else if ($general_type == 'bank_to_bank')
        {
            $this->session->set_userdata('voucher_type', 'contra_voucher');
            $string = "cv.currency_converted_amount, cv.contra_voucher_id, cv.voucher_status, cv.voucher_date, cv.voucher_number";
            $from   = "contra_voucher cv";
            $join   = array(
                    'general_bill g' => 'g.general_bill_id=cv.reference_id' );
            $where  = array(
                    // 'party_id' => $id,
                    // 'g.purpose_of_transaction' => 'Loan',
                    // 'g.type_of_transaction' => 'Loan Paid',
                    'cv.party_type'                   => 'ledger',
                    'cv.reference_type'               => 'general_bill',
                    'cv.voucher_status'               => 1,
                    'cv.currency_converted_amount <=' => $receipt_amount1,
                    'cv.currency_converted_amount >=' => $receipt_amount,
                    'cv.financial_year_id'            => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'cv.branch_id'                    => $this->session->userdata('SESS_BRANCH_ID'),
                    'cv.delete_status'                => 0
            );
            if ($general_type == 'bank_to_bank')
            {
                $where['g.purpose_of_transaction'] = 'Bank to Bank';
            }

            $order = array(
                    'cv.voucher_number' => 'asc' );
            $res   = $this->general_model->getJoinRecords($string, $from, $where, $join, $order);

            $output = '';
            $output .= '<div class="form-group">
                            <h4>Voucher List</h4>
                        <table border="1" class="table table-bordered table-striped dataTable">
                        <tr><th>Select</th>
                        <th>Voucher Date</th>
                         <th>Voucher Number</th>
                         <th>Reciept Amount</th>
                        </tr>';
            foreach ($res as $row)
            {
                $output .= '<tr>
                            <td><label class="form-check-label"><input class="checkbox" type="checkbox" name="checkbox"
                                    value="' . $row->contra_voucher_id . '"></label></td>
                            <td>' . $row->voucher_date . '</td>
                            <td>' . $row->voucher_number . '</td>
                            <td>' . $row->currency_converted_amount . '</td>
                          </tr>';
            }
            $output .= "</table></div>";
        }

        echo json_encode($output);
    }

    public function store_statement()
    {
        $bank_reconciliation_module_id = $this->config->item('bank_reconciliation_module');
        $data['module_id']             = $bank_reconciliation_module_id;
        $modules                       = $this->modules;
        $privilege                     = "add_privilege";
        $data['privilege']             = "add_privilege";
        $section_modules               = $this->get_section_modules($bank_reconciliation_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $arr4 = $this->session->userdata("arr_data4");
        $i    = 0;
        $type = '';

        foreach ($arr4 as $key => $value)
        {
            foreach ($value as $k => $v)
            {
                if (($k == "cid") AND ( $v == $this->session->userdata('cid')))
                {
                    $type = $arr4[$i]["type"];
                }
            }
            $i++;
        }

        $id  = $this->session->userdata('id');
        $sid = $this->session->userdata('statement_id');
        $tid = $this->input->post('tid');

        if ($id > 0)
        {
            $sub_statement_data = $this->general_model->getRecords('*', 'sub_statement', array(
                    'sub_statement_id'  => $id,
                    'bank_statement_id' => $sid ));
            $statement_amount   = $sub_statement_data[0]->amount;
        }
        else
        {
            $bank_statement_data = $this->general_model->getRecords('*', 'bank_statement', array(
                    'bank_statement_id' => $sid ));
            if ($bank_statement_data[0]->credit > 0)
            {
                $statement_amount = $bank_statement_data[0]->credit;
            }
            else
            {
                $statement_amount = $bank_statement_data[0]->debit;
            }
        }

        $voucher_type = $this->session->userdata('voucher_type');
        $this->session->unset_userdata('voucher_type');

        if ($type == "suppliers" || $type == "expense" || $voucher_type == 'payment_voucher')
        {
            $voucher_data   = $this->general_model->getRecords('*', 'payment_voucher', array(
                    'payment_id'    => $tid,
                    'delete_status' => 0 ));
            $voucher_amount = $voucher_data[0]->currency_converted_amount;
        }

        if ($type == "customer" || $voucher_type == 'receipt_voucher')
        {
            $voucher_data   = $this->general_model->getRecords('*', 'receipt_voucher', array(
                    'receipt_id'    => $tid,
                    'delete_status' => 0 ));
            $voucher_amount = $voucher_data[0]->currency_converted_amount;
        }

        if ($type == "customer_advance")
        {
            $voucher_data   = $this->general_model->getRecords('*', 'advance_voucher', array(
                    'advance_id'    => $tid,
                    'delete_status' => 0 ));
            $voucher_amount = $voucher_data[0]->currency_converted_amount;
        }

        if ($type == "customer_refund")
        {
            $voucher_data   = $this->general_model->getRecords('*', 'refund_voucher', array(
                    'refund_id'     => $tid,
                    'delete_status' => 0 ));
            $voucher_amount = $voucher_data[0]->currency_converted_amount;
        }

        if ($voucher_type == 'contra_voucher')
        {
            $voucher_data   = $this->general_model->getRecords('*', 'contra_voucher', array(
                    'contra_voucher_id' => $tid,
                    'delete_status'     => 0 ));
            $voucher_amount = $voucher_data[0]->currency_converted_amount;
        }

        $success = 0;
        if ($statement_amount == $voucher_amount)
        {
            $result = $this->general_model->getRecords('*', 'bank_statement', array(
                    'bank_statement_id' => $sid ));
            foreach ($result as $raw)
            {
                if ($raw->split_status == 0)
                {
                    $data = array(
                            'bank_statement_id' => $sid,
                            'voucher_id'        => $tid,
                            'sub_statement_id'  => $id,
                            'split_status'      => '0',
                            'party_type'        => $type
                    );
                    if ($type == "suppliers" || $type == "expense" || $voucher_type == "payment_voucher")
                    {
                        $data['voucher_type'] = 'payment_voucher';
                    }
                    elseif ($type == 'customer' || $voucher_type == "receipt_voucher")
                    {
                        $data['voucher_type'] = 'receipt_voucher';
                    }
                    elseif ($type == 'customer_advance')
                    {
                        $data['voucher_type'] = 'advance_voucher';
                    }
                    elseif ($type == 'customer_refund')
                    {
                        $data['voucher_type'] = 'refund_voucher';
                    }
                    elseif ($voucher_type == 'contra_voucher')
                    {
                        $data['voucher_type'] = 'contra_voucher';
                    }
                    $this->general_model->insertData('categorized_statement', $data);
                }
                else
                {
                    $data = array(
                            'bank_statement_id' => $sid,
                            'voucher_id'        => $tid,
                            'sub_statement_id'  => $id,
                            'split_status'      => '1',
                            'party_type'        => $type
                    );
                    if ($type == "suppliers" || $type == "expense" || $voucher_type == "payment_voucher")
                    {
                        $data['voucher_type'] = 'payment_voucher';
                    }
                    elseif ($type == 'customer' || $voucher_type == "receipt_voucher")
                    {
                        $data['voucher_type'] = 'receipt_voucher';
                    }
                    elseif ($type == 'customer_advance')
                    {
                        $data['voucher_type'] = 'advance_voucher';
                    }
                    elseif ($type == 'customer_refund')
                    {
                        $data['voucher_type'] = 'refund_voucher';
                    }
                    elseif ($voucher_type == "contra_voucher")
                    {
                        $data['voucher_type'] = 'contra_voucher';
                    }
                    $this->general_model->insertData('categorized_statement', $data);
                }
            }

            $res = 0;

            if ($type == "suppliers" || $type == "expense" || $voucher_type == "payment_voucher")
            {
                $res = $this->bank_statement_model->updateSuppliers($tid);
            }

            if ($type == "customer" || $voucher_type == "receipt_voucher")
            {
                $res = $this->bank_statement_model->updateCustomer($tid);
            }

            if ($type == "customer_advance")
            {
                $res = $this->bank_statement_model->updateCustomerAdvance($tid);
            }

            if ($type == "customer_refund")
            {
                $res = $this->bank_statement_model->updateCustomerRefund($tid);
            }

            if ($voucher_type == "contra_voucher")
            {
                $res = $this->bank_statement_model->updateContra($tid);
            }
            $success = 1;
        }

        // $this->categorized_statement();

        $output[0] = $this->list_raw_data();

        $output[1] = $this->list_categorized_data();

        $output[2] = $this->list_suspense_data();

        $output[3] = $success;

        echo json_encode($output);
    }

    public function categorized_statement()
    {
        $arr4 = $this->session->userdata("arr_data4");
        $i    = 0;
        $type = '';

        foreach ($arr4 as $key => $value)
        {
            foreach ($value as $k => $v)
            {
                if (($k == "cid") AND ( $v == $this->session->userdata('cid')))
                {
                    $type = $arr4[$i]["type"];
                }
            }
            $i++;
        }

        $id  = $this->session->userdata('id');
        $sid = $this->session->userdata('statement_id');
        $tid = $this->input->post('tid');

        // if($this->session->userdata('transaction_id')=='empty' || !$this->session->userdata('transaction_id'))
        // {
        //     $tid=$this->input->post('tid');
        //     $trans_id=explode(':',$tid);
        //     if(isset($trans_id[1]))
        //     {
        //         $tid=$trans_id[0];
        //         $type=$trans_id[1];
        //         if($type=="payment_header")
        //         {
        //             $type="suppliers";
        //         }
        //         if($type=="receipt_header")
        //         {
        //             $type="customer";
        //         }
        //     }
        // }
        // else
        // {
        //     $tid=$this->session->userdata('transaction_id');
        //     if($this->session->userdata('table_header')=='payment_header')
        //     {
        //         $type="suppliers";
        //     }
        //     if($this->session->userdata('table_header')=='receipt_header')
        //     {
        //         $type="customer";
        //     }
        //     $this->session->unset_userdata('table_header');
        // }
        // $this->session->set_userdata('transaction_id','empty');

        $result = $this->general_model->getRecords('*', 'bank_statement', array(
                'bank_statement_id' => $sid ));
        foreach ($result as $raw)
        {
            if ($raw->split_status == 0)
            {
                $data = array(
                        'bank_statement_id' => $sid,
                        'voucher_id'        => $tid,
                        'sub_statement_id'  => $id,
                        'split_status'      => '0',
                        'party_type'        => $type
                );
                if ($type == "suppliers" || $type == "expense")
                {
                    $data['voucher_type'] = 'payment_voucher';
                }
                elseif ($type == 'customer')
                {
                    $data['voucher_type'] = 'receipt_voucher';
                }
                elseif ($type == 'customer_advance')
                {
                    $data['voucher_type'] = 'advance_voucher';
                }
                elseif ($type == 'customer_refund')
                {
                    $data['voucher_type'] = 'refund_voucher';
                }
                $this->general_model->insertData('categorized_statement', $data);
            }
            else
            {
                $data = array(
                        'bank_statement_id' => $sid,
                        'voucher_id'        => $tid,
                        'sub_statement_id'  => $id,
                        'split_status'      => '1',
                        'party_type'        => $type
                );
                if ($type == "suppliers" || $type == "expense")
                {
                    $data['voucher_type'] = 'payment_voucher';
                }
                elseif ($type == 'customer')
                {
                    $data['voucher_type'] = 'receipt_voucher';
                }
                elseif ($type == 'customer_advance')
                {
                    $data['voucher_type'] = 'advance_voucher';
                }
                elseif ($type == 'customer_refund')
                {
                    $data['voucher_type'] = 'refund_voucher';
                }
                $this->general_model->insertData('categorized_statement', $data);
            }
        }

        $res = 0;

        if ($type == "suppliers" || $type == "expense")
        {
            $res = $this->bank_statement_model->updateSuppliers($tid);
        }

        if ($type == "customer")
        {
            $res = $this->bank_statement_model->updateCustomer($tid);
        }

        if ($type == "customer_advance")
        {
            $res = $this->bank_statement_model->updateCustomerAdvance($tid);
        }

        if ($type == "customer_refund")
        {
            $res = $this->bank_statement_model->updateCustomerRefund($tid);
        }

        // if($type=="expense")
        // {
        //     $res=$this->bank_statement_model->updateSuppliers($tid);
        // }
        // if($type=="income")
        // {
        //     $res=$this->bank_statement_model->updateIncome($tid);
        // }
    }

    public function invoice_add()
    {
        if ($this->session->userdata('cat_type') == 'customer')
        {

        }
        if ($this->session->userdata('cat_type') == 'suppliers')
        {

        }

        if ($this->session->userdata('cat_type') == 'expense')
        {

        }
        if ($this->session->userdata('cat_type') == 'journal')
        {

        }
    }

    public function add_voucher_invoice()
    {
        // $sales_purchase_id=$this->input->post('sales_purchase_id');
        // echo $this->session->userdata('cat_type');
        // exit;
        if ($this->session->userdata('cat_type') == 'customer')
        {
            redirect('receipt_voucher/add');
        }
        if ($this->session->userdata('cat_type') == 'suppliers')
        {
            redirect('payment_voucher/add');
        }
        if ($this->session->userdata('cat_type') == 'expense')
        {
            redirect('payment_voucher/add');
        }
        if ($this->session->userdata('cat_type') == 'customer_advance')
        {
            redirect('advance_voucher/add');
        }
        if ($this->session->userdata('cat_type') == 'customer_refund')
        {
            redirect('refund_voucher/add');
        }
    }

    public function remove_categorized()
    {
        $sid = $this->input->post('sid');
        $id  = $this->input->post('id');

        $where = array(
                'bank_statement_id' => $sid,
                'sub_statement_id'  => $id );
        $data  = $this->general_model->getRecords('*', 'categorized_statement', $where);
        // $data=$this->bank_statement_model->get_categorized_statement($sid,$id);
        foreach ($data as $raw)
        {
            if (($raw->party_type == "suppliers" || $raw->party_type == "expense" || $raw->party_type == "loan" || $raw->party_type == "capital" || $raw->party_type == "investment" || $raw->party_type == "deposit" || $raw->party_type == "duties_taxes" || $raw->party_type == "advance" || $raw->party_type == "fixed_asset" || $raw->party_type == "ledger") && $raw->voucher_type == 'payment_voucher')
            {
                $res = $this->bank_statement_model->removeSuppliers($raw->voucher_id, $raw->bank_statement_id, $raw->sub_statement_id);
            }

            if (($raw->party_type == "customer" || $raw->party_type == "loan" || $raw->party_type == "indirect_income" || $raw->party_type == "capital" || $raw->party_type == "investment" || $raw->party_type == "deposit" || $raw->party_type == "duties_taxes" || $raw->party_type == "advance" || $raw->party_type == "fixed_asset" || $raw->party_type == "ledger") && $raw->voucher_type == 'receipt_voucher')
            {
                $res = $this->bank_statement_model->removeCustomer($raw->voucher_id, $raw->bank_statement_id, $raw->sub_statement_id);
            }

            if ($raw->party_type == "customer_advance" && $raw->voucher_type == 'advance_voucher')
            {
                $res = $this->bank_statement_model->removeCustomerAdvance($raw->voucher_id, $raw->bank_statement_id, $raw->sub_statement_id);
            }

            if ($raw->party_type == "customer_refund" && $raw->voucher_type == 'refund_voucher')
            {
                $res = $this->bank_statement_model->removeCustomerRefund($raw->voucher_id, $raw->bank_statement_id, $raw->sub_statement_id);
            }

            if (($raw->party_type == "bank_to_bank" || $raw->party_type == "ledger") && $raw->voucher_type == 'contra_voucher')
            {
                $res = $this->bank_statement_model->removeContra($raw->voucher_id, $raw->bank_statement_id, $raw->sub_statement_id);
            }

            // if($raw->party_type=="expense")
            // {
            //     $res=$this->bank_statement_model->removeSuppliers($raw->transaction_id,$raw->statement_id,$raw->sub_statement_id);
            // }
            // if($raw->party_type=="income")
            // {
            //     $res=$this->bank_statement_model->removeIncome($raw->voucher_id,$raw->bank_statement_id,$raw->sub_statement_id);
            // }
        }

        $this->session->set_userdata('transaction_id', 'empty');

        $output[0] = $this->list_raw_data();

        $output[1] = $this->list_categorized_data();

        $output[2] = $this->list_suspense_data();

        // if($res)
        // {
        //     $output[3]=$res[0];
        //     $output[4]=$res[1];
        // }
        // else
        // {
        $output[3] = '';
        // }

        echo json_encode($output);
    }

    public function split_statement()
    {
        $id   = $this->input->post('id');
        $sid  = $this->input->post('sid');
        $type = $this->input->post('type');
        $form = $this->input->post('form');
        $i    = 0;
        $arr  = array();
        foreach ($form as $value)
        {
            foreach ($value as $key => $val)
            {
                if ($key == 'value')
                {
                    $arr2[$i] = $val;
                    $i++;
                }
            }
        }
        $res = $this->general_model->getRecords('*', 'bank_statement', array(
                'bank_statement_id' => $sid,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID') ));
        if ($res[0]->suspense_status == 0)
        {
            for ($j = 0; $j < $i; $j++)
            {
                $insert = array(
                        'amount'            => $arr2[$j],
                        'amount_type'       => $type,
                        'bank_statement_id' => $sid
                );
                $this->general_model->insertData('sub_statement', $insert);
            }

            $where  = array(
                    'bank_statement_id' => $sid,
                    'split_status'      => 0 );
            $update = array(
                    'split_status' => 1 );
            $this->general_model->updateData('bank_statement', $update, $where);
        }
        else
        {
            for ($j = 0; $j < $i; $j++)
            {
                $insert = array(
                        'amount'            => $arr2[$j],
                        'amount_type'       => $type,
                        'bank_statement_id' => $sid,
                        'suspense_status'   => 1
                );
                // $this->bank_statement_model->insert_substatement($insert);
                $this->general_model->insertData('sub_statement', $insert);
            }
            // $this->bank_statement_model->update_statement_status($sid,$id);
            $where  = array(
                    'bank_statement_id' => $sid,
                    'split_status'      => 0 );
            $update = array(
                    'split_status' => 1 );
            $this->general_model->updateData('bank_statement', $update, $where);
        }

        $output = $this->list_raw_data();

        echo json_encode($output);
    }

    public function merge_statement()
    {
        $sid = $this->input->post('sid');

        $res = $this->bank_statement_model->merge_statement($sid);

        $output[0] = $this->list_raw_data();
        $output[1] = '';
        $output[2] = '';
        $output[3] = '';
        // for ($i=0; $i < sizeof($res); $i++)
        // {
        //     if($i==0)
        //     {
        //         $output[1].='Invoice No : ';
        //         $j=0;
        //         foreach ($res[$i] as $val)
        //         {
        //             if($j==0)
        //                 $output[1].=$val;
        //             else
        //                 $output[1].=', '.$val;
        //             $j++;
        //         }
        //     }
        //     else
        //     {
        //         $output[2]='';
        //         $output[3]='';
        //         foreach ($res[$i] as $val)
        //         {
        //             $value=explode('_', $val);
        //             $output[2].=$value[0].'_';
        //             $output[3].=$value[1].'_';
        //         }
        //     }
        // }
        echo json_encode($output);
    }

    public function suspense_statement()
    {
        $sid = $this->input->post('sid');
        $id  = $this->input->post('id');

        $this->bank_statement_model->suspense_statement($sid, $id);

        $output = $this->list_suspense_data();

        echo json_encode($output);
    }

    public function add_comment()
    {
        $id   = $this->input->post('id');
        $sid  = $this->input->post('sid');
        $data = array(
                'comment' => $this->input->post('comment') );
        // $res=$this->bank_statement_model->add_comment($id,$sid,$data);
        if ($id == 0)
        {
            $this->db->where('bank_statement_id', $sid);
            $res = $this->db->update('bank_statement', $data);
        }
        else
        {
            $condition = array(
                    'sub_statement_id'  => $id,
                    'bank_statement_id' => $sid );
            $this->db->where($condition);
            $res       = $this->db->update('sub_statement', $data);
        }
        echo json_encode($res);
    }

    // public function bank_reconciliation()
    // {
    //     $string            = 'b.*,l.*';
    //     $table             = 'bank_account b';
    //     $join['ledgers l'] = 'l.ledger_id=b.ledger_id';
    //     $where             = array('b.delete_status' => 0, 'b.branch_id' => $this->session->userdata('SESS_BRANCH_ID'));
    //     $data['bank']      = $this->general_model->getJoinRecords($string, $table, $where, $join, $order = "");
    //     $this->load->view('reconciliation/list',$data);
    // }
}

