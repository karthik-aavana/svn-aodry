<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Report_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    public function getJoinRecords($string, $table, $where, $join, $order = "", $group_by = "")
    {
        $this->db->select($string);
        $this->db->from("$table");
        foreach ($join as $key => $value)
        {
            $val = explode('#', $value);
            if (isset($val[1]))
            {
                $this->db->join("$key", $val[0], $val[1]);
            }
            else
            {
                $this->db->join("$key", $val[0]);
            }
        } $this->db->where($where);
        if ($order != "")
        {
            foreach ($order as $key => $value)
            {
                $this->db->order_by($key, $value);
            }
        } if ($group_by != "")
        {
            foreach ($group_by as $key => $value)
            {
                $this->db->group_by($value);
            }
        } return $this->db->get()->result();
    }
    public function getExempReceiptData($month)
    {
        $financial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $branch_id         = $this->session->userdata('SESS_BRANCH_ID');
        $reg_query1        = $this->db->query('SELECT s.sales_grand_total as total_val FROM sales_item si LEFT JOIN sales s on s.sales_id = si.sales_id LEFT JOIN services sr on sr.service_id=si.item_id AND si.item_type="service" LEFT JOIN products pr on pr.product_id=si.item_id AND si.item_type="product" LEFT JOIN tax t on t.tax_id=sr.service_tax_id LEFT JOIN tax tx on tx.tax_id=pr.product_tax_id JOIN customer cs on cs.customer_id=s.sales_party_id JOIN branch b on b.branch_id=s.branch_id  WHERE s.financial_year_id="' . $financial_year_id . '" && MONTH(s.sales_date)= "' . $month . '" && s.branch_id= "' . $branch_id . '" && s.sales_billing_state_id =cs.customer_state_id && s.sales_tax_amount =0 && s.delete_status=0 &&  t.tax_name !="Exempted" && cs.customer_gst_registration_type="Registered"');
        $unreg_query1      = $this->db->query('SELECT s.sales_grand_total as total_val FROM sales_item si LEFT JOIN sales s on s.sales_id = si.sales_id LEFT JOIN services sr on sr.service_id=si.item_id AND si.item_type="service" LEFT JOIN products pr on pr.product_id=si.item_id AND si.item_type="product" LEFT JOIN tax t on t.tax_id=sr.service_tax_id LEFT JOIN tax tx on tx.tax_id=pr.product_tax_id JOIN customer cs on cs.customer_id=s.sales_party_id JOIN branch b on b.branch_id=s.branch_id  WHERE s.financial_year_id="' . $financial_year_id . '" && MONTH(s.sales_date)= "' . $month . '" && s.branch_id= "' . $branch_id . '" && s.sales_billing_state_id =cs.customer_state_id && s.sales_tax_amount =0 && s.delete_status=0 &&  t.tax_name !="Exempted" && cs.customer_gst_registration_type="Unregistered"');
        $reg_query2        = $this->db->query('SELECT s.sales_grand_total as total_val FROM sales_item si LEFT JOIN sales s on s.sales_id = si.sales_id LEFT JOIN services sr on sr.service_id=si.item_id AND si.item_type="service" LEFT JOIN products pr on pr.product_id=si.item_id AND si.item_type="product" LEFT JOIN tax t on t.tax_id=sr.service_tax_id LEFT JOIN tax tx on tx.tax_id=pr.product_tax_id JOIN customer cs on cs.customer_id=s.sales_party_id JOIN branch b on b.branch_id=s.branch_id  WHERE s.financial_year_id="' . $financial_year_id . '" && MONTH(s.sales_date)= "' . $month . '" && s.branch_id= "' . $branch_id . '" && s.sales_billing_state_id =cs.customer_state_id && s.sales_tax_amount =0 && s.delete_status=0 &&  t.tax_name ="Exempted" && cs.customer_gst_registration_type="Registered"');
        $unreg_query2      = $this->db->query('SELECT s.sales_grand_total as total_val FROM sales_item si LEFT JOIN sales s on s.sales_id = si.sales_id LEFT JOIN services sr on sr.service_id=si.item_id AND si.item_type="service" LEFT JOIN products pr on pr.product_id=si.item_id AND si.item_type="product" LEFT JOIN tax t on t.tax_id=sr.service_tax_id LEFT JOIN tax tx on tx.tax_id=pr.product_tax_id JOIN customer cs on cs.customer_id=s.sales_party_id JOIN branch b on b.branch_id=s.branch_id  WHERE s.financial_year_id="' . $financial_year_id . '" && MONTH(s.sales_date)= "' . $month . '" && s.branch_id= "' . $branch_id . '" && s.sales_billing_state_id =cs.customer_state_id && s.sales_tax_amount =0 && s.delete_status=0 &&  t.tax_name ="Exempted" && cs.customer_gst_registration_type="Unregistered"');
        $reg_query3        = $this->db->query('SELECT s.sales_grand_total as total_val FROM sales_item si LEFT JOIN sales s on s.sales_id = si.sales_id LEFT JOIN services sr on sr.service_id=si.item_id AND si.item_type="service" LEFT JOIN products pr on pr.product_id=si.item_id AND si.item_type="product" LEFT JOIN tax t on t.tax_id=sr.service_tax_id LEFT JOIN tax tx on tx.tax_id=pr.product_tax_id JOIN customer cs on cs.customer_id=s.sales_party_id JOIN branch b on b.branch_id=s.branch_id  WHERE s.financial_year_id="' . $financial_year_id . '" && MONTH(s.sales_date)= "' . $month . '" && s.branch_id= "' . $branch_id . '" && s.sales_billing_state_id != cs.customer_state_id && s.sales_tax_amount =0 && s.delete_status=0 &&  t.tax_name !="Exempted" && cs.customer_gst_registration_type="Registered"');
        $unreg_query3      = $this->db->query('SELECT s.sales_grand_total as total_val FROM sales_item si LEFT JOIN sales s on s.sales_id = si.sales_id LEFT JOIN services sr on sr.service_id=si.item_id AND si.item_type="service" LEFT JOIN products pr on pr.product_id=si.item_id AND si.item_type="product" LEFT JOIN tax t on t.tax_id=sr.service_tax_id LEFT JOIN tax tx on tx.tax_id=pr.product_tax_id JOIN customer cs on cs.customer_id=s.sales_party_id JOIN branch b on b.branch_id=s.branch_id  WHERE s.financial_year_id="' . $financial_year_id . '" && MONTH(s.sales_date)= "' . $month . '" && s.branch_id= "' . $branch_id . '" && s.sales_billing_state_id !=cs.customer_state_id && s.sales_tax_amount =0 && s.delete_status=0 &&  t.tax_name !="Exempted" && cs.customer_gst_registration_type="Unregistered"');
        $reg_query4        = $this->db->query('SELECT s.sales_grand_total as total_val FROM sales_item si LEFT JOIN sales s on s.sales_id = si.sales_id LEFT JOIN services sr on sr.service_id=si.item_id AND si.item_type="service" LEFT JOIN products pr on pr.product_id=si.item_id AND si.item_type="product" LEFT JOIN tax t on t.tax_id=sr.service_tax_id LEFT JOIN tax tx on tx.tax_id=pr.product_tax_id JOIN customer cs on cs.customer_id=s.sales_party_id JOIN branch b on b.branch_id=s.branch_id  WHERE s.financial_year_id="' . $financial_year_id . '" && MONTH(s.sales_date)= "' . $month . '" && s.branch_id= "' . $branch_id . '" && s.sales_billing_state_id !=cs.customer_state_id && s.sales_tax_amount =0 && s.delete_status=0 &&  t.tax_name ="Exempted" && cs.customer_gst_registration_type="Registered"');
        $unreg_query4      = $this->db->query('SELECT s.sales_grand_total as total_val FROM sales_item si LEFT JOIN sales s on s.sales_id = si.sales_id LEFT JOIN services sr on sr.service_id=si.item_id AND si.item_type="service" LEFT JOIN products pr on pr.product_id=si.item_id AND si.item_type="product" LEFT JOIN tax t on t.tax_id=sr.service_tax_id LEFT JOIN tax tx on tx.tax_id=pr.product_tax_id JOIN customer cs on cs.customer_id=s.sales_party_id JOIN branch b on b.branch_id=s.branch_id  WHERE s.financial_year_id="' . $financial_year_id . '" && MONTH(s.sales_date)= "' . $month . '" && s.branch_id= "' . $branch_id . '" && s.sales_billing_state_id !=cs.customer_state_id && s.sales_tax_amount =0 && s.delete_status=0 &&  t.tax_name ="Exempted" && cs.customer_gst_registration_type="Unregistered"');
        if (empty($reg_query1->result()))
        {
            $reg_query1_data = 0;
        }
        else
        {
            $reg_result1     = $reg_query1->result();
            $reg_query1_data = $result1[0]->total_val;
        } if (empty($unreg_query1->result()))
        {
            $unreg_query1_data = 0;
        }
        else
        {
            $unreg_result1     = $unreg_query1->result();
            $unreg_query1_data = $unreg_result1[0]->total_val;
        } if (empty($reg_query2->result()))
        {
            $reg_query2_data = 0;
        }
        else
        {
            $reg_result2     = $reg_query2->result();
            $reg_query2_data = $reg_result2[0]->total_val;
        } if (empty($unreg_query2->result()))
        {
            $unreg_query2_data = 0;
        }
        else
        {
            $unreg_result2     = $unreg_query2->result();
            $unreg_query2_data = $unreg_result2[0]->total_val;
        } if (empty($reg_query3->result()))
        {
            $reg_query3_data = 0;
        }
        else
        {
            $reg_result3     = $reg_query3->result();
            $reg_query3_data = $reg_result3[0]->total_val;
        } if (empty($unreg_query3->result()))
        {
            $unreg_query3_data = 0;
        }
        else
        {
            $unreg_result3     = $unreg_query3->result();
            $unreg_query3_data = $unreg_result3[0]->total_val;
        } if (empty($reg_query4->result()))
        {
            $reg_query4_data = 0;
        }
        else
        {
            $reg_result4     = $reg_query4->result();
            $reg_query4_data = $reg_result4[0]->total_val;
        } if (empty($unreg_query4->result()))
        {
            $unreg_query4_data = 0;
        }
        else
        {
            $unreg_result4     = $unreg_query4->result();
            $unreg_query4_data = $unreg_result4[0]->total_val;
        } $result = array(
                "reg_intra_nill"     => $reg_query1_data,
                "unreg_intra_nill"   => $unreg_query1_data,
                "reg_intra_exempt"   => $reg_query2_data,
                "unreg_intra_exempt" => $unreg_query2_data,
                "reg_inter_nill"     => $reg_query3_data,
                "unreg_inter_nill"   => $unreg_query3_data,
                "reg_inter_exempt"   => $reg_query4_data,
                "unreg_inter_exempt" => $unreg_query4_data );
        return $result;
    }

    function MaingroupReport($data){
        $this->db->select('main_grp_id,grp_name');
        $r = $this->db->get('tbl_main_group');
        $total_grp = $r->result_array();
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $grp_array = array();

        foreach ($total_grp as $key => $value) {
            $get_resp = $this->db->query("SELECT ledger_name,ledger_id,sub_group_name_1,sub_group_name_2 FROM `tbl_main_group` m LEFT JOIN  `tbl_sub_group` s ON m.main_grp_id=s.main_grp_id LEFT JOIN `tbl_ledgers` l ON s.sub_grp_id=l.sub_group_id WHERE ledger_name !='' AND  l.branch_id='{$branch_id}' AND m.main_grp_id='{$value['main_grp_id']}'");
            $ledgers_list = $get_resp->result_array();
            $grp_array[$value['grp_name']] = array();
            $total_loss= 0;
            $sub_group_name_1 = $sub_group_name_2 = '';
            $temp_ledger_ary = array();
            if(!empty($ledgers_list)){
                foreach ($ledgers_list as $key => $ledger) {
                    $report_ary = array('branch_id' => $branch_id,'ledger_id' => $ledger['ledger_id'],'from_date' => $data['from_date'], 'to_date' => $data['to_date']);
       
                    $final_ary = $this->getLedgerReportAry($report_ary);
                    $total_loss += abs($final_ary['closing_balance']);
                    $grp_array[$value['grp_name']]['sub_group_name_1'] = $ledger['sub_group_name_1'];
                    $grp_array[$value['grp_name']]['sub_group_name_2'] = $ledger['sub_group_name_2'];
                    if($ledger['sub_group_name_1'] != ''){
                        if($ledger['sub_group_name_2'] != ''){
                            $temp_ledger_ary['is_sub_2'][$ledger['sub_group_name_1']][$ledger['sub_group_name_2']][] = array('name' => $ledger['ledger_name'],'ledger_id' => $ledger['ledger_id'],'total' => abs($final_ary['closing_balance']));
                        }else{
                            $temp_ledger_ary['is_sub_1'][$ledger['sub_group_name_1']][] = array('name' => $ledger['ledger_name'],'ledger_id' => $ledger['ledger_id'],'total' => abs($final_ary['closing_balance']));
                        }
                    }else{
                        $temp_ledger_ary['is_ledger']['ledgers'][]  = array('name' => $ledger['ledger_name'],'ledger_id' => $ledger['ledger_id'],'total' => abs($final_ary['closing_balance']));
                    }
                }
            }
            $grp_array[$value['grp_name']] = $temp_ledger_ary;
            if(!empty($temp_ledger_ary)) $grp_array[$value['grp_name']]['total_loss'] = $total_loss;
        }

        return $grp_array;
    }

    function getPLReportAry($grp_array,$data){
       
        $blank = array('','','');
        $total_grp = array('Sales group','Direct Income','Purchases group','Direct Expenses','Indirect Incomes','Indirect Expenses');
        /*$this->db->select('closing_stock,opening_stock,stock_id');
        $this->db->where('firm_id',$data['firm_id']);
        $this->db->where('acc_id',$data['acc_id']);
        $stock_qry = $this->db->get('tbl_stock');
        $stocks = $stock_qry->result_array();
        $closing_stock = $opening_stock = $stock_id = 0;
        if(!empty($stocks)){
            $closing_stock = $stocks[0]['closing_stock'];
            $opening_stock = $stocks[0]['opening_stock'];
            $stock_id = $stocks[0]['stock_id'];
        }
        $grp_array['Closing Stock']['total_loss'] = $closing_stock;
        $grp_array['Opening Stock']['total_loss'] = $opening_stock;
        $grp_array['stock_id'] = $stock_id;*/

        $total_direct_in_ary = array('Sales group' => 'Sales (A)','Direct Income' => 'Direct Income (B)','Closing Stock' => 'Closing Stock (C)');
        $total_direct_income = 0;
        $final_ary = array();
        $i = 0;
        $final_ary[$i] = array('Calculation of Gross Profit/(Loss)','','','space'=>0,'is_heading'=>true); 
        $i++;
        foreach ($total_direct_in_ary as $k => $temp_grp) {
            $main_grp_ary = $grp_array[$k];
            $final_ary[$i] = array($temp_grp,'',''); 
            $main_key = $i;
            $i++;
            $total_loss = 0;

            foreach ($main_grp_ary as $key => $main_grp) {
                if($key == 'is_sub_2'){
                    foreach ($main_grp as $k1 => $sub_1) {
                        $total_sub_1 = 0;
                        $final_ary[$i] = array($k1,'','');
                        $final_ary[$i]['space'] = 1;
                        $sub_key_1 = $i;
                        $i++;
                        foreach ($sub_1 as $k2 => $sub_2) {
                            $final_ary[$i] = array($k2,'','');
                            
                            $sub_key_2 = $i;
                            $i++;
                            $total_sub_2 = 0;
                            foreach ($sub_2 as $key => $value) {
                                $total_sub_2 += abs($value['total']);
                                $final_ary[$i] = array($value['name'],number_format($value['total'],2),'');
                                $final_ary[$i]['space'] = 3;
                                $final_ary[$i]['is_left'] = 1;
                                $final_ary[$i]['ledger_id'] =$value['ledger_id'];
                                $i++;
                            }
                            $final_ary[$sub_key_2] = array($k2,number_format($total_sub_2,2),'');
                            $final_ary[$sub_key_2]['space'] = 2;
                            $total_sub_1 += abs($total_sub_2);
                        }
                        $final_ary[$sub_key_1] = array($k1,number_format($total_sub_1,2),'');
                        $final_ary[$sub_key_1]['space'] = 1;
                        $total_loss += abs($total_sub_1);
                    }
                }elseif($key == 'is_sub_1'){
                    foreach ($main_grp as $k1 => $sub_1) {
                        $total_sub_1 = 0;
                        $final_ary[$i] = array($k1,'','');
                        $sub_key_1 = $i;
                        $i++;
                        foreach ($sub_1 as $key => $value) {
                            $total_sub_1 += abs($value['total']);
                            $final_ary[$i] = array($value['name'],number_format($value['total'],2),'');
                            $final_ary[$i]['space'] = 2;
                            $final_ary[$i]['is_left'] = 1;
                            $final_ary[$i]['ledger_id'] =$value['ledger_id'];
                            $i++;
                        }
                        $final_ary[$sub_key_1] = array($k1,number_format($total_sub_1,2),'');
                        $final_ary[$sub_key_1]['space'] = 1;
                        $total_loss += abs($total_sub_1);
                    }
                }elseif ($key == 'is_ledger') {
                    
                    foreach ($main_grp['ledgers'] as $key => $value) {
                        $total_loss += abs($value['total']);
                        $final_ary[$i] = array($value['name'],number_format($value['total'],2),'');
                        $final_ary[$i]['space'] = 1;
                        $final_ary[$i]['is_left'] = 1;
                        $final_ary[$i]['ledger_id'] =$value['ledger_id'];
                        $i++;
                    }
                }/*elseif ($k == 'Closing Stock') {
                    $total_loss += $closing_stock;
                }*/
            }
           
            $final_ary[$main_key] = array($temp_grp,'',(is_numeric($total_loss) ? number_format($total_loss,2) : round($total_loss)),'is_main'=>true);
            $final_ary[$main_key]['space'] = 0;
            $total_direct_income += abs($total_loss); 
            $final_ary[$i] = $blank;
            $i++;
        }
        $final_ary[$i] = array('Total Direct Income (D= A+B+C)','',number_format($total_direct_income,2),'space'=>0,'is_main'=>true); 
        $i++;
        $final_ary[$i] = $blank;
        $i++;
        $total_direct_exp_ary = array('Opening Stock' => 'Opening Stock (E)','Purchases group' => 'Purchases (F)','Direct Expenses' => 'Direct Expenses (G)');
        $total_direct_exp = 0;

        foreach ($total_direct_exp_ary as $k => $temp_grp) {
            $main_grp_ary = $grp_array[$k];
            $final_ary[$i] = array($temp_grp,'',''); 
            $main_key = $i;
            $i++;
            $total_loss = 0;

            foreach ($main_grp_ary as $key => $main_grp) {
                if($key == 'is_sub_2'){
                    foreach ($main_grp as $k1 => $sub_1) {
                        $total_sub_1 = 0;
                        $final_ary[$i] = array($k1,'','');
                        $final_ary[$i]['space'] = 1;
                        $sub_key_1 = $i;
                        $i++;
                        foreach ($sub_1 as $k2 => $sub_2) {
                            $final_ary[$i] = array($k2,'','');
                            
                            $sub_key_2 = $i;
                            $i++;
                            $total_sub_2 = 0;
                            foreach ($sub_2 as $key => $value) {
                                $total_sub_2 += abs($value['total']);
                                $final_ary[$i] = array($value['name'],number_format($value['total'],2),'');
                                $final_ary[$i]['space'] = 3;
                                $final_ary[$i]['is_left'] = 1;
                                $final_ary[$i]['ledger_id'] =$value['ledger_id'];
                                $i++;
                            }
                            $final_ary[$sub_key_2] = array($k2,number_format($total_sub_2,2),'');
                            $final_ary[$sub_key_2]['space'] = 2;
                            $total_sub_1 += abs($total_sub_2);
                        }
                        $final_ary[$sub_key_1] = array($k1,number_format($total_sub_1,2),'');
                        $final_ary[$sub_key_1]['space'] = 1;
                        $total_loss += abs($total_sub_1);
                    }
                }elseif($key == 'is_sub_1'){
                    foreach ($main_grp as $k1 => $sub_1) {
                        $total_sub_1 = 0;
                        $final_ary[$i] = array($k1,'','');
                        $sub_key_1 = $i;
                        $i++;
                        foreach ($sub_1 as $key => $value) {
                            $total_sub_1 += abs($value['total']);
                            $final_ary[$i] = array($value['name'],number_format($value['total'],2),'');
                            $final_ary[$i]['space'] = 2;
                            $final_ary[$i]['is_left'] = 1;
                            $final_ary[$i]['ledger_id'] =$value['ledger_id'];
                            $i++;
                        }
                        $final_ary[$sub_key_1] = array($k1,number_format($total_sub_1,2),'');
                        $final_ary[$sub_key_1]['space'] = 1;
                        $total_loss += abs($total_sub_1);
                    }
                }elseif ($key == 'is_ledger') {
                    
                    foreach ($main_grp['ledgers'] as $key => $value) {
                        $total_loss += abs($value['total']);
                        $final_ary[$i] = array($value['name'],number_format($value['total'],2),'');
                        $final_ary[$i]['space'] = 1;
                        $final_ary[$i]['is_left'] = 1;
                        $final_ary[$i]['ledger_id'] =$value['ledger_id'];
                        $i++;
                    }
                }/*elseif ($k == 'Opening Stock') {
                    $total_loss += $opening_stock;
                }*/
            }
            
            $final_ary[$main_key] = array($temp_grp,'',(is_numeric($total_loss) ? number_format($total_loss,2) : round($total_loss)),'is_main'=>true);
            $final_ary[$main_key]['space'] = 0;
            $total_direct_exp += abs($total_loss); 
            $final_ary[$i] = $blank;
            $i++;
        }

        $final_ary[$i] = array('Total Direct Expenses (H= E+F+G)','',number_format($total_direct_exp,2),'space'=>0,'is_main'=>true); 
        $i++;
        $final_ary[$i] = $blank;
        $i++;
        $gross_PL = $total_direct_income - $total_direct_exp;
        $final_ary[$i] = array('Gross Profit/ (Gross loss) (I=D-H)','',number_format($gross_PL,2),'space'=>0,'is_main'=>true); 
        $i++;
        $final_ary[$i] = $blank;
        $i++;
        $total_direct_exp_ary = array('Indirect Incomes' => 'Indirect Incomes (J)','Indirect Expenses' => 'Indirect Expenses (K)');
        foreach ($total_direct_exp_ary as $k => $temp_grp) {
            $main_grp_ary = $grp_array[$k];
            $final_ary[$i] = array($temp_grp,'',''); 
            $main_key = $i;
            $i++;
            $total_loss = 0;

            foreach ($main_grp_ary as $key => $main_grp) {
                if($key == 'is_sub_2'){
                    foreach ($main_grp as $k1 => $sub_1) {
                        $total_sub_1 = 0;
                        $final_ary[$i] = array($k1,'','');
                        $final_ary[$i]['space'] = 1;
                        $sub_key_1 = $i;
                        $i++;
                        foreach ($sub_1 as $k2 => $sub_2) {
                            $final_ary[$i] = array($k2,'','');
                            
                            $sub_key_2 = $i;
                            $i++;
                            $total_sub_2 = 0;
                            foreach ($sub_2 as $key => $value) {
                                $total_sub_2 += abs($value['total']);
                                $final_ary[$i] = array($value['name'],number_format($value['total'],2),'');
                                $final_ary[$i]['space'] = 3;
                                $final_ary[$i]['is_left'] = 1;
                                $final_ary[$i]['ledger_id'] =$value['ledger_id'];
                                $i++;
                            }
                            $final_ary[$sub_key_2] = array($k2,number_format($total_sub_2,2),'');
                            $final_ary[$sub_key_2]['space'] = 2;
                            $total_sub_1 += abs($total_sub_2);
                        }
                        $final_ary[$sub_key_1] = array($k1,number_format($total_sub_1,2),'');
                        $final_ary[$sub_key_1]['space'] = 1;
                        $total_loss += abs($total_sub_1);
                    }
                }elseif($key == 'is_sub_1'){
                    foreach ($main_grp as $k1 => $sub_1) {
                        $total_sub_1 = 0;
                        $final_ary[$i] = array($k1,'','');
                        $sub_key_1 = $i;
                        $i++;
                        foreach ($sub_1 as $key => $value) {
                            $total_sub_1 += abs($value['total']);
                            $final_ary[$i] = array($value['name'],number_format($value['total'],2),'');
                            $final_ary[$i]['space'] = 2;
                            $final_ary[$i]['is_left'] = 1;
                            $final_ary[$i]['ledger_id'] =$value['ledger_id'];
                            $i++;
                        }
                        $final_ary[$sub_key_1] = array($k1,number_format($total_sub_1,2),'');
                        $final_ary[$sub_key_1]['space'] = 1;
                        $total_loss += abs($total_sub_1);
                    }
                }elseif ($key == 'is_ledger') {
                    
                    foreach ($main_grp['ledgers'] as $key => $value) {
                        $total_loss += abs($value['total']);
                        $final_ary[$i] = array($value['name'],number_format($value['total'],2),'');
                        $final_ary[$i]['space'] = 1;
                        $final_ary[$i]['is_left'] = 1;
                        $final_ary[$i]['ledger_id'] =$value['ledger_id'];
                        $i++;
                    }
                }
            }
           
            $final_ary[$main_key] = array($temp_grp,'',(is_numeric($total_loss) ? number_format($total_loss,2) : round($total_loss)),'is_main'=>true);
            $final_ary[$main_key]['space'] = 0;
            $total_direct_exp += abs($total_loss); 
            $final_ary[$i] = $blank;
            $i++;
        }
        $indirect_total_loss = (@$grp_array['Indirect Incomes']['total_loss'] ? $grp_array['Indirect Incomes']['total_loss'] : 0);
        $indirect_expence_loss = (@$grp_array['Indirect Expenses']['total_loss'] ? $grp_array['Indirect Expenses']['total_loss'] : 0);
        $net_pl = (($gross_PL + $indirect_total_loss) - $indirect_expence_loss);
        $final_ary[$i] = array('Net Profit/ (Net loss) (L=I+J-K)','',number_format($net_pl,2),'space'=>0,'is_heading'=>true); 
        $i++;
        $json = array();
        $json['final_ary'] = $final_ary;
        /*$json['stock_id'] = $stock_id;*/
        $json['net_pl'] = $net_pl;
        return $json;
    }

    public function getLedgerReportAry($dt){

        $ledger_id = $dt['ledger_id'];
        $branch_id = $dt['branch_id'];
       
        $from_date = $dt['from_date'];
        $to_date = $dt['to_date'];
        $current_yr = date('Y',strtotime($from_date));
        $previous_year = $current_yr - 1;

        /*********** if there is any default openeing balance **********/
        $old_balance = 0;
        $this->db->select('balance_id');
        $this->db->where('balance_upto_date <=',$from_date);
        $this->db->where('branch_id',$branch_id);
        $qry = $this->db->get('tbl_default_balance_date');
        
        if($qry->num_rows() > 0){
            $this->db->select('amount,amount_type');
            $this->db->where('ledger_id',$ledger_id);
            $this->db->where('status','1');
            $this->db->where('branch_id',$branch_id);
            $qry = $this->db->get('tbl_default_opening_balance');
            if($qry->num_rows() > 0){
                $old_bal = $qry->result_array();
                $old_balance = $old_bal[0]['amount'];
                if($old_bal[0]['amount_type'] == 'DR'){
                    $old_balance = 0 - $old_balance;
                }
            }
        }
       
        /* --------- Start to calculate opening balance -----------*/
        $opening_balance = 0;
        $this->db->select('closing_balance');
        $this->db->where('year < ',$current_yr);
        $this->db->where('ledger_id',$ledger_id);
        $this->db->where('branch_id',$branch_id);
        $this->db->order_by('year','DESC');
        $this->db->limit(1);
        $qry = $this->db->get('tbl_reports_bunch');
        $close_resp = $qry->result_array();
        if(!empty($close_resp)){
            $opening_balance = $close_resp[0]['closing_balance'];
        }

        $opening_balance = $opening_balance + $old_balance;
        $month = date('m',strtotime($from_date));

        $quadrants = $this->config->item('quadrants');
        $quadrants_colum = array();
        foreach ($quadrants as $key => $value) {
            if(in_array($month, $value)){
                $current_months = $value;
                break;
            }
            array_push($quadrants_colum, $key);
        }
   
        /* find before months quantrant balance */
        if(!empty($quadrants_colum)){
            $columns_name = implode(',', $quadrants_colum);
          
            $this->db->select("{$columns_name}");
            $this->db->where('year =',$current_yr);
            $this->db->where('ledger_id',$ledger_id);
            $this->db->where('branch_id',$branch_id);
            $months_report = $this->db->get('tbl_reports_bunch');
            $q_months_report = $months_report->result_array();
            if(!empty($q_months_report)){
                foreach ($q_months_report[0] as $key => $value) {
                    $opening_balance += $value;
                }
            }
        }
        /* find remaining months and date balance */
        $start_from_date = date('Y-m-d',strtotime('01-'.$current_months[0].'-'.$current_yr));
        $from_date = date('Y-m-d',strtotime($from_date));

        $resp = $this->db->query("SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_sales_voucher` ts JOIN sales_voucher v ON ts.sales_voucher_id=v.sales_voucher_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0'  AND ts.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )"); //(v.invoice_date BETWEEN '{$start_from_date}' AND '{$from_date}' )
       
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];

        $resp = $this->db->query("SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_purchase_voucher` ts JOIN purchase_voucher v ON ts.purchase_voucher_id=v.purchase_voucher_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0'  AND ts.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )"); //(v.invoice_date BETWEEN '{$start_from_date}' AND '{$from_date}' )
       
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];

        $resp = $this->db->query("SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_advance_voucher` ts JOIN advance_voucher v ON ts.advance_voucher_id=v.advance_voucher_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0'  AND ts.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )"); //(v.invoice_date BETWEEN '{$start_from_date}' AND '{$from_date}' )
       
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];

        $resp = $this->db->query("SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_general_voucher` ts JOIN general_voucher v ON ts.general_voucher_id=v.general_voucher_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0'  AND ts.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )"); //(v.invoice_date BETWEEN '{$start_from_date}' AND '{$from_date}' )
       
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];

        $resp = $this->db->query("SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_payment_voucher` ts JOIN payment_voucher v ON ts.payment_voucher_id=v.payment_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0'  AND ts.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )"); 
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];

        $resp = $this->db->query("SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_receipt_voucher` ts JOIN receipt_voucher v ON ts.receipt_voucher_id=v.receipt_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0'  AND ts.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )"); 
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];

        $resp = $this->db->query("SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_refund_voucher` ts JOIN refund_voucher v ON ts.refund_voucher_id=v.refund_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0'  AND ts.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )"); 
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];

        $resp = $this->db->query("SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_expense_voucher` ts JOIN expense_voucher v ON ts.expense_voucher_id=v.expense_voucher_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0'  AND ts.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )"); 
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];

        $resp = $this->db->query("SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_bank_voucher` ts JOIN bank_voucher v ON ts.bank_voucher_id=v.bank_voucher_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0'  AND ts.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )"); //(v.invoice_date BETWEEN '{$start_from_date}' AND '{$from_date}' )
       
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];

        $resp = $this->db->query("SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_cash_voucher` ts JOIN cash_voucher v ON ts.cash_voucher_id=v.cash_voucher_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0'  AND ts.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )"); //(v.invoice_date BETWEEN '{$start_from_date}' AND '{$from_date}' )
       
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];

        $resp = $this->db->query("SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_contra_voucher` ts JOIN contra_voucher v ON ts.contra_voucher_id=v.contra_voucher_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0' AND ts.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )"); //(v.invoice_date BETWEEN '{$start_from_date}' AND '{$from_date}' )
       
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];

        $resp = $this->db->query("SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_journal_voucher` ts JOIN tbl_journal_voucher v ON ts.journal_voucher_id=v.journal_voucher_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0' AND ts.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )"); //(v.invoice_date BETWEEN '{$start_from_date}' AND '{$from_date}' )
       
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];

        
        /* ------------ finish opening balance ------------ */
        /*echo "SELECT SUM(CASE WHEN dr_amount > 0 THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `accounts_sales_voucher` ts LEFT JOIN sales_voucher v ON ts.sales_voucher_id=v.sales_voucher_id WHERE v.branch_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.delete_status='0' AND (v.voucher_date >= '{$start_from_date}' AND v.voucher_date < '{$from_date}' )";
        print_r($result);*/

        $temp_voucher = $final_report = array();
        $dr_total = $cr_total = 0;

        $all_ledger = $this->getAccountLedgers($dt['branch_id']);
        foreach ($all_ledger as $key => $value) {
            $ledger_name['ledger_'.$value['ledger_id']] = $value['ledger_name'];
        }
        $resp = $this->db->query("SELECT *,'sales' as type FROM `accounts_sales_voucher` ts  JOIN (SELECT sales_voucher_id FROM accounts_sales_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.sales_voucher_id=ts.sales_voucher_id JOIN sales_voucher v ON ts.sales_voucher_id=v.sales_voucher_id WHERE v.branch_id='{$branch_id}' AND v.delete_status='0' AND ts.delete_status='0' AND (v.voucher_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') ");
        $sales_result = $resp->result_array();

        $resp = $this->db->query("SELECT *,'purchase' as type FROM `accounts_purchase_voucher` ts  JOIN (SELECT purchase_voucher_id as sales_voucher_id FROM accounts_purchase_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.sales_voucher_id=ts.purchase_voucher_id JOIN purchase_voucher v ON ts.purchase_voucher_id=v.purchase_voucher_id WHERE v.branch_id='{$branch_id}' AND v.delete_status='0' AND ts.delete_status='0'  AND (v.voucher_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') ");
        $purchase_result = $resp->result_array();

        $resp = $this->db->query("SELECT *,'advance' as type FROM `accounts_advance_voucher` ts  JOIN (SELECT advance_voucher_id as sales_voucher_id FROM accounts_advance_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.sales_voucher_id=ts.advance_voucher_id JOIN advance_voucher v ON ts.advance_voucher_id=v.advance_voucher_id WHERE v.branch_id='{$branch_id}' AND v.delete_status='0' AND ts.delete_status='0' AND (v.voucher_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') ");
        $advance_result = $resp->result_array();

        $resp = $this->db->query("SELECT *,'jouranl' as type FROM `accounts_general_voucher` ts  JOIN (SELECT general_voucher_id as sales_voucher_id FROM accounts_general_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.sales_voucher_id=ts.general_voucher_id JOIN general_voucher v ON ts.general_voucher_id=v.general_voucher_id WHERE v.branch_id='{$branch_id}' AND v.delete_status='0' AND ts.delete_status='0' AND (v.voucher_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') ");
        $journal_result = $resp->result_array();

        $resp = $this->db->query("SELECT *,'payment' as type FROM `accounts_payment_voucher` ts  JOIN (SELECT payment_voucher_id as sales_voucher_id FROM accounts_payment_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.sales_voucher_id=ts.payment_voucher_id JOIN payment_voucher v ON ts.payment_voucher_id=v.payment_id WHERE v.branch_id='{$branch_id}' AND v.delete_status='0' AND ts.delete_status='0' AND (v.voucher_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') ");
        $payment_result = $resp->result_array();

        $resp = $this->db->query("SELECT *,'receipt' as type FROM `accounts_receipt_voucher` ts  JOIN (SELECT receipt_voucher_id as sales_voucher_id FROM accounts_receipt_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.sales_voucher_id=ts.receipt_voucher_id JOIN receipt_voucher v ON ts.receipt_voucher_id=v.receipt_id WHERE v.branch_id='{$branch_id}' AND v.delete_status='0' AND ts.delete_status='0'  AND (v.voucher_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') ");
        $receipt_result = $resp->result_array();

        $resp = $this->db->query("SELECT *,'refund' as type FROM `accounts_refund_voucher` ts  JOIN (SELECT refund_voucher_id as sales_voucher_id FROM accounts_refund_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.sales_voucher_id=ts.refund_voucher_id JOIN refund_voucher v ON ts.refund_voucher_id=v.refund_id WHERE v.branch_id='{$branch_id}' AND v.delete_status='0' AND ts.delete_status='0'  AND (v.voucher_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') ");
        $refund_result = $resp->result_array();

        $resp = $this->db->query("SELECT *,'expense' as type FROM `accounts_expense_voucher` ts JOIN (SELECT expense_voucher_id as sales_voucher_id FROM accounts_expense_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.sales_voucher_id=ts.expense_voucher_id JOIN expense_voucher v ON ts.expense_voucher_id=v.expense_voucher_id WHERE v.branch_id='{$branch_id}' AND v.delete_status='0' AND ts.delete_status='0'  AND (v.voucher_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') GROUP BY accounts_expense_id");

        $exp_result = $resp->result_array();

        $resp = $this->db->query("SELECT *,'bank' as type FROM `accounts_bank_voucher` ts  JOIN (SELECT bank_voucher_id as sales_voucher_id FROM accounts_bank_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.sales_voucher_id=ts.bank_voucher_id JOIN bank_voucher v ON ts.bank_voucher_id=v.bank_voucher_id WHERE v.branch_id='{$branch_id}' AND v.delete_status='0'  AND ts.delete_status='0' AND (v.voucher_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') ");

        $bank_result = $resp->result_array();

        $resp = $this->db->query("SELECT *,'cash' as type FROM `accounts_cash_voucher` ts  JOIN (SELECT cash_voucher_id as sales_voucher_id FROM accounts_cash_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.sales_voucher_id=ts.cash_voucher_id JOIN cash_voucher v ON ts.cash_voucher_id=v.cash_voucher_id WHERE v.branch_id='{$branch_id}' AND v.delete_status='0'  AND ts.delete_status='0' AND (v.voucher_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') ");

        $cash_result = $resp->result_array();

        $resp = $this->db->query("SELECT *,'contra' as type FROM `accounts_contra_voucher` ts  JOIN (SELECT contra_voucher_id as sales_voucher_id FROM accounts_contra_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.sales_voucher_id=ts.contra_voucher_id JOIN contra_voucher v ON ts.contra_voucher_id=v.contra_voucher_id WHERE v.branch_id='{$branch_id}' AND v.delete_status='0' AND ts.delete_status='0' AND (v.voucher_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') ");

        $contra_result = $resp->result_array();

        $resp = $this->db->query("SELECT *,'general' as type FROM `accounts_journal_voucher` ts  JOIN (SELECT journal_voucher_id as sales_voucher_id FROM accounts_journal_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.sales_voucher_id=ts.journal_voucher_id JOIN tbl_journal_voucher v ON ts.journal_voucher_id=v.journal_voucher_id WHERE v.branch_id='{$branch_id}' AND v.delete_status='0' AND ts.delete_status='0' AND (v.voucher_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') ");

        $general_result = $resp->result_array();
       
        $result = array_merge($sales_result,$purchase_result,$advance_result,$general_result,$payment_result,$receipt_result,$refund_result,$exp_result,$bank_result,$cash_result,$contra_result,$journal_result);
        $date_array = array();
        if(!empty($result)){
            foreach ($result as $key => $value) {
                
               // echo $date_asc;
                $temp_voucher['voucher_'.$value['type'].$value['sales_voucher_id']][] = $value;
            }
            $i = 0;

            foreach ($temp_voucher as $key => $voucher) {
               

                $ledger_detail = $this->findLedgerDetail($voucher,$dt['ledger_id']);
                if(!empty($ledger_detail)){
                   
                    $amount_type = $ledger_detail['amount_type'];
                    $ledger_amount = $ledger_detail['voucher_amount'];
                    $is_first = true;
                   
                    $same_ledger_ary = array();
                    foreach ($voucher as $key => $value) {
                        $date_asc = strtotime($value['voucher_date']);
                        $date_array[$date_asc] = $date_asc;  
                        ksort($date_array);                    
                        $value['amount_type'] = 'CR';
                        if($value['dr_amount'] > 0) $value['amount_type'] = 'DR';
                        if($value['ledger_id'] != $dt['ledger_id'] && $amount_type != $value['amount_type']){

                            $final_report[$date_asc][$i]['voucher_date'] = $value['voucher_date'];
                            /*$final_report[$i]['voucher_type'] = $value['voucher_type'];*/
                            $final_report[$date_asc][$i]['voucher_id'] = $value['sales_voucher_id'];
                            $final_report[$date_asc][$i]['voucher_type'] = $value['type'];
                            $final_report[$date_asc][$i]['voucher_no'] = $value['voucher_number'];//$value['sales_voucher_id'];
                            $final_report[$date_asc][$i]['voucher_number'] = $value['voucher_number'];
                            $final_report[$date_asc][$i]['ledger'] = (@$ledger_name['ledger_'.$value['ledger_id']] ? $ledger_name['ledger_'.$value['ledger_id']] : '');

                            if($is_first || in_array($value['ledger_id'], $same_ledger_ary)){//
                                if($amount_type == 'DR'){
                                    $final_report[$date_asc][$i]['DR'] = $ledger_amount;
                                    $dr_total += $ledger_amount;
                                }else{
                                    $final_report[$date_asc][$i]['CR'] = $ledger_amount;
                                    $cr_total += $ledger_amount;
                                }
                                $is_first = false;
                            }
                            array_push($same_ledger_ary,$value['ledger_id']);
                            $i++;
                        }
                    }
                }
            
            }
        }

        $closing_balance = 0;
        if($opening_balance < 0){
            $cr_total = $cr_total + abs($opening_balance);
        }else{
            $dr_total = $dr_total + abs($opening_balance);
        }

        $closing_balance = $dr_total - $cr_total;
        
        if($closing_balance < 0){
            $dr_total = $dr_total + abs($closing_balance);
        }else{
            $cr_total = $cr_total + abs($closing_balance);
        }
        $dr_total = number_format(abs($dr_total),2);
        $cr_total = number_format(abs($cr_total),2);

        $responce = array();
        $responce['final_report'] = $final_report;
        $responce['opening_balance'] = $opening_balance;
        $responce['closing_balance'] = $closing_balance;
        $responce['dr_total'] = $dr_total;
        $responce['cr_total'] = $cr_total;
        $responce['date_asc'] = $date_array;

        return $responce;
    }
    
    function findLedgerDetail($voucher,$ledger_id){
        $resp = array();
        $voucher_amount = 0;
        $dr_amount = 0;
        $cr_amount = 0;
      
        foreach ($voucher as $key => $v) {
            if($v['ledger_id'] == $ledger_id){
               
                $v['amount_type'] = 'CR';
                if($v['dr_amount'] > 0) $v['amount_type'] = 'DR';
                $resp = $v;
                $voucher_amount += $v['voucher_amount'];
                $dr_amount += $v['dr_amount'];
                $cr_amount += $v['cr_amount'];
                //break;
            }
        }
        if($voucher_amount > 0){
            $resp['voucher_amount'] = $voucher_amount;
            $resp['dr_amount'] = $dr_amount;
            $resp['cr_amount'] = $cr_amount;
        }
        return $resp;
    }

    public function getAccountLedgers($branch_id){
        $this->db->select('ledger_name,ledger_id');
        $this->db->where('ledger_name !=','');
        $this->db->where('branch_id',$branch_id);
        $resp = $this->db->get('tbl_ledgers');
        $resp = $resp->result_array();
        return $resp;
    }

    public function getAllMainGroup(){
        $this->db->select('grp_name,main_grp_id');
        $main_qry = $this->db->get('tbl_main_group');
        return $main_qry->result_array();
    }

    public function getLedgerName($ledger_id){
        $this->db->select('ledger_name');
        $this->db->where('ledger_id',$ledger_id);
        $q = $this->db->get('tbl_ledgers');
        $result = $q->result_array();
        if(!empty($result)){
            return $result[0]['ledger_name'];
        }else{
            return '';
        }
    }
}
