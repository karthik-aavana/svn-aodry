<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// modules list
$config['quotation_module'] = 1;
$config['sales_module'] = 2;
$config['sales_credit_note_module'] = 3;
$config['sales_debit_note_module'] = 4;
$config['purchase_order_module'] = 5;
$config['purchase_module'] = 6;
$config['purchase_return_module'] = 7;
$config['delivery_challan_module'] = 8;
$config['supplier_module'] = 9;
$config['customer_module'] = 10;
$config['product_module'] = 11;
$config['service_module'] = 12;
$config['category_module'] = 13;
$config['subcategory_module'] = 14;

$config['uqc_module'] = 16;
$config['discount_module'] = 17;
$config['expense_bill_module'] = 18;
$config['receipt_voucher_module'] = 19;
$config['payment_voucher_module'] = 20;
$config['expense_voucher_module'] = 21;
$config['advance_voucher_module'] = 22;
$config['refund_voucher_module'] = 23;
$config['notes_module'] = 24;
$config['email_module'] = 25;
$config['report_module'] = 26;
$config['purchase_credit_note_module'] = 27;
$config['purchase_debit_note_module'] = 28;
$config['recurrence_module'] = 29;
$config['accounts_module'] = 30;
$config['sales_voucher_module'] = 31;
$config['pos_voucher_module'] = 114;
$config['purchase_voucher_module'] = 32;
$config['tax_module'] = 33;
$config['expense_module'] = 34;
$config['logs_module'] = 35;
$config['user_module'] = 36;
$config['layout_module'] = 37;
$config['privilege_module'] = 38;
// $config['recurrence_module'] = 39;
$config['bank_account_module'] = 40;
$config['news_updates_module'] = 41;
$config['bank_reconciliation_module'] = 42;
$config['varients_module'] = 43;
$config['general_bill_module'] = 44;
$config['contra_voucher_module'] = 45;
$config['crm_module'] = 46;
$config['lead_module'] = 47;
$config['general_voucher_module'] = 48;
$config['manufacturing_module'] = 49;
$config['pos_billing_module'] = 50;
$config['labour_module'] = 51;
$config['over_head_module'] = 52;
$config['location_module'] = 53;
$config['hsn_module'] = 54;
$config['currency_module'] = 55;
$config['company_setting_module'] = 56;
$config['stock_module'] = 57;
$config['missing_stock_module'] = 58;
$config['damaged_stock_module'] = 59;
$config['BOE_module'] = 60;
$config['bank_voucher_module'] = 61;
$config['cash_voucher_module'] = 62;
$config['journal_voucher_module'] = 63;
$config['shareholder_module'] = 64;
$config['deposit_module'] = 65;
$config['fixed_assets_module'] = 66;
$config['investments_module'] = 67;
$config['loan_module'] = 68;
$config['department_module'] = 69;
$config['sub_department_module'] = 70;

$config['performa_module'] = 71;
$config['brand_module'] = 72;
$config['groups_module'] = 73;
$config['file_manager_module'] = 74;
$config['financial_year_module'] = 75;
$config['barcode_module'] = 76;
$config['sales_report_module'] = 77;
$config['sales_credit_note_report_module'] = 78;
$config['sales_debit_note_report_module'] = 79;
$config['purchase_report_module'] = 80;
$config['purchase_credit_note_report_module'] = 81;
$config['purchase_debit_note_report_module'] = 82;
$config['expense_bill_report_module'] = 83;
$config['advance_voucher_report_module'] = 84;
$config['refund_voucher_report_module'] = 85;
$config['receipt_voucher_report_module'] = 86;
$config['payment_voucher_report_module'] = 87;
$config['tds_sales_report_module'] = 88;
$config['tcs_sales_report_module'] = 89;
$config['tcs_purchase_report_module'] = 90;
$config['tds_expense_report_module'] = 91;
$config['gst_report'] = 92;
$config['gst_sales_credit_note_report'] = 93;
$config['gst_sales_debit_note_report'] = 94;
$config['hsn_report_module'] = 95;
$config['closing_balance_module'] = 96;
$config['ledger_view_report_module'] = 97;
$config['trial_balance_report_module'] = 98;
$config['profit_loss_report_module'] = 99;
$config['balance_sheet_report_module'] = 100;
$config['group_ledgers_module'] = 101;
$config['module_setting'] = 102;
$config['sales_credit_note_voucher'] = 103;
$config['sales_debit_note_voucher'] = 104;
$config['purchase_credit_note_voucher'] = 105;
$config['purchase_debit_note_voucher'] = 106;
$config['sales_stock_report'] = 107;
$config['purchase_stock_report'] = 108;
$config['closing_stock_report'] = 109;
$config['product_stock_report'] = 110;
$config['gstr1_report_module'] = 111;
$config['team_module'] = 112;
$config['outlet_module'] = 113;
$config['warehouse_module'] = 115;

//submodule list
$config['transporter_sub_module'] = 2;
$config['shipping_sub_module'] = 3;
$config['charges_sub_module'] = 4;
$config['notes_sub_module'] = 5;
$config['email_sub_module'] = 6;
$config['accounts_sub_module'] = 7;
$config['recurrence_sub_module'] = 8;

$config['section'] = array(
"bill" => array(
			"quotation"=>array($config['quotation_module']),
			"performa"=>array($config['performa_module']),
			"sales"=>array($config['sales_module'],$config['sales_credit_note_module'],$config['sales_debit_note_module']),
			"purchase_order"=>array($config['purchase_order_module']),
			"purchase"=>array($config['purchase_module'],$config['BOE_module'],$config['purchase_credit_note_module'],$config['purchase_debit_note_module'],$config['purchase_return_module']),
			"delivery_challan"=>array($config['delivery_challan_module']),
			"expense"=>array($config['expense_bill_module'])
),
"items" => array($config['product_module'],$config['service_module']),
"vouchers" => array($config['advance_voucher_module'],$config['receipt_voucher_module'],$config['payment_voucher_module'],$config['refund_voucher_module']),
"reports" => array($config['report_module']),
"people" => array($config['customer_module'],$config['supplier_module']),
"settings" => array($config['category_module'],$config['subcategory_module'],$config['tax_module'],$config['expense_module'],$config['discount_module'],$config['uqc_module'],$config['email_module']),
"templates" => array($config['notes_module'],$config['email_module']),
"privilege" => array($config['privilege_module']),
"logs" => array($config['logs_module'])
);

// modules assign


$config['modules_assigned_section'] = array(
"admin" => array($config['quotation_module'],$config['sales_module'],$config['sales_credit_note_module'],$config['sales_debit_note_module'],$config['purchase_order_module'],$config['purchase_module'],$config['BOE_module'],$config['purchase_return_module'],$config['delivery_challan_module'],$config['supplier_module'],$config['customer_module'],$config['product_module'],$config['service_module'],$config['category_module'],$config['subcategory_module'],$config['uqc_module'],$config['discount_module'],$config['expense_bill_module'],$config['receipt_voucher_module'],$config['payment_voucher_module'],$config['expense_voucher_module'],$config['advance_voucher_module'],$config['refund_voucher_module'],$config['report_module'],$config['accounts_module'],$config['sales_voucher_module'],$config['purchase_voucher_module'],$config['tax_module'],$config['expense_module'],$config['logs_module'],$config['user_module'],$config['layout_module'],$config['purchase_credit_note_module'],$config['purchase_debit_note_module'],$config['privilege_module'],$config['performa_module'],$config['brand_module']),
"sales_person" => array($config['quotation_module'],$config['sales_module'],$config['sales_credit_note_module'],$config['sales_debit_note_module'],$config['delivery_challan_module'],$config['customer_module'],$config['receipt_voucher_module'],$config['advance_voucher_module'],$config['refund_voucher_module'],$config['accounts_module'],$config['sales_voucher_module'],$config['layout_module'],$config['performa_module'],$config['brand_module']),

"purchaser" => array($config['purchase_order_module'],$config['purchase_module'],$config['BOE_module'],$config['purchase_return_module'],$config['supplier_module'],$config['payment_voucher_module'],$config['accounts_module'],$config['purchase_voucher_module'],$config['layout_module'],$config['purchase_credit_note_module'],$config['purchase_debit_note_module']),
"manager" => array($config['quotation_module'],$config['sales_module'],$config['sales_credit_note_module'],$config['sales_debit_note_module'],$config['delivery_challan_module'],$config['customer_module'],$config['receipt_voucher_module'],$config['advance_voucher_module'],$config['refund_voucher_module'],$config['accounts_module'],$config['sales_voucher_module'],$config['layout_module'],$config['purchase_order_module'],$config['purchase_module'],$config['purchase_return_module'],$config['supplier_module'],$config['payment_voucher_module'],$config['BOE_module'],$config['purchase_voucher_module'],$config['purchase_credit_note_module'],$config['purchase_debit_note_module'],$config['tax_module'],$config['product_module'],$config['service_module'],$config['category_module'],$config['subcategory_module'],$config['uqc_module'],$config['discount_module'],$config['expense_bill_module'],$config['expense_voucher_module'],$config['expense_module'],$config['performa_module'],$config['brand_module']),

"accountant" => array($config['quotation_module'],$config['sales_module'],$config['sales_credit_note_module'],$config['sales_debit_note_module'],$config['delivery_challan_module'],$config['customer_module'],$config['receipt_voucher_module'],$config['advance_voucher_module'],$config['refund_voucher_module'],$config['accounts_module'],$config['sales_voucher_module'],$config['layout_module'],$config['purchase_order_module'],$config['purchase_module'],$config['BOE_module'],$config['purchase_return_module'],$config['supplier_module'],$config['payment_voucher_module'],$config['purchase_voucher_module'],$config['purchase_credit_note_module'],$config['purchase_debit_note_module'],$config['report_module'],$config['expense_bill_module'],$config['expense_voucher_module'],$config['performa_module'],$config['brand_module']),

"members" => array($config['quotation_module'],$config['sales_module'],$config['sales_credit_note_module'],$config['sales_debit_note_module'],$config['delivery_challan_module'],$config['customer_module'],$config['receipt_voucher_module'],$config['advance_voucher_module'],$config['refund_voucher_module'],$config['accounts_module'],$config['sales_voucher_module'],$config['layout_module'],$config['purchase_order_module'],$config['purchase_module'],$config['BOE_module'],$config['purchase_return_module'],$config['supplier_module'],$config['payment_voucher_module'],$config['purchase_voucher_module'],$config['purchase_credit_note_module'],$config['purchase_debit_note_module'],$config['report_module'],$config['expense_bill_module'],$config['expense_voucher_module'],$config['performa_module'],$config['brand_module']),

);

$config['add_modules_assigned_section'] = array(
"admin" => $config['modules_assigned_section']['admin'],
"sales_person" => $config['modules_assigned_section']['sales_person'],
"purchaser" => $config['modules_assigned_section']['purchaser'],
"manager" => $config['modules_assigned_section']['manager'],
"accountant" => $config['modules_assigned_section']['accountant']
);

$config['edit_modules_assigned_section'] = array(
"admin" => $config['modules_assigned_section']['admin'],
"sales_person" => $config['modules_assigned_section']['sales_person'],
"purchaser" => $config['modules_assigned_section']['purchaser'],
"manager" => $config['modules_assigned_section']['manager'],
"accountant" => $config['modules_assigned_section']['accountant']

);

$config['delete_modules_assigned_section'] = array(
"admin" => $config['modules_assigned_section']['admin'],
"manager" => $config['modules_assigned_section']['manager'],
"accountant" => $config['modules_assigned_section']['accountant']
);

$config['view_modules_assigned_section'] = array(
"admin" => $config['modules_assigned_section']['admin'],
"sales_person" => $config['modules_assigned_section']['sales_person'],
"purchaser" => $config['modules_assigned_section']['purchaser'],
"manager" => $config['modules_assigned_section']['manager'],
"accountant" => $config['modules_assigned_section']['accountant'],
"members" => $config['modules_assigned_section']['members']

);
//table list

//groups
$config['admin_group'] = 1;
$config['members_group'] = 2;
$config['purchaser_group'] = 3;
$config['sales_person_group'] = 4;
$config['manager_group'] = 5;
$config['accountant_group'] = 6;

$config['sales_table'] = "sales";
$config['sales_item_table'] = "sales_item";
$config['sales_credit_note_table'] = "sales_credit_note";
$config['sales_credit_note_item_table'] = "sales_credit_note_item";
$config['sales_debit_note_table'] = "sales_debit_note";
$config['sales_debit_note_item_table'] = "sales_debit_note_item";
$config['purchase_credit_note_table'] = "purchase_credit_note";
$config['purchase_credit_note_item_table'] = "purchase_credit_note_item";
$config['purchase_debit_note_table'] = "purchase_debit_note";
$config['purchase_debit_note_item_table'] = "purchase_debit_note_item";
$config['purchase_table'] = "purchase";
$config['boe_table'] = "boe";
$config['purchase_order_table'] = "purchase_order";
$config['purchase_order_item_table'] = "purchase_order_item";
$config['purchase_item_table'] = "purchase_item";
$config['boe_item_table'] = "boe_items";
$config['quotation_table'] = "quotation";
$config['quotation_item_table'] = "quotation_item";
$config['performa_table'] = "performa";
$config['performa_item_table'] = "performa_item";
$config['product_table'] = "products";
$config['log_table'] = "log";
$config['receipt_voucher_table'] = "receipt_voucher";
$config['payment_voucher_table'] = "payment_voucher";
$config['advance_voucher_table'] = "advance_voucher";
$config['refund_voucher_table'] = "refund_voucher";
$config['expense_bill_table'] = "expense_bill";
$config['expense_voucher_table'] = "expense_voucher";
$config['expense_bill_item_table'] = "expense_bill_item";
$config['sales_voucher_table'] = "sales_voucher";
$config['pos_voucher_table'] = "pos_voucher";
$config['purchase_voucher_table'] = "purchase_voucher";
$config['general_bill_table'] = "general_bill";
$config['general_voucher_table'] = "general_voucher";
$config['contra_voucher_table'] = "contra_voucher";
$config['leads_table'] = "leads";

$config['Sundry Debtors'] = "Sundry Debtors";
$config['Sundry Creditors'] = "Sundry Creditors";
$config['Suspense'] = "Suspense";
$config['Cash-in-Hand'] = "Cash-in-Hand";
$config['Sales Accounts'] = "Sales Accounts";
$config['Indirect Expense'] = "Indirect Expense";
$config['Duties & Taxes'] = "Duties & Taxes";
$config['Fixed Assets'] = "Fixed Assets";
$config['Purchase Accounts'] = "Purchase Accounts";
$config['Indirect Income'] = "Indirect Income";
$config['Bank Accounts'] = "Bank Accounts";
$config['Current Liabilities'] = "Current Liabilities";
$config['Self'] = "Self ";
$config['Partners'] = "Partners ";
$config['Shareholder'] = "Shareholder ";
$config['Capital'] = "Capital ";

/* Added by Chetna */
$config['sql_details'] = array(
							'user' => 'root',
				            'pass' => '',
				            'db'   => 'aavana_v4_1',
				            'host' => 'localhost'									
						);

$config['quadrants'] = array(
						'1st_bunch' => array('01','02','03'),
						'2ed_bunch' => array('04','05','06'),
						'3rd_bunch' => array('07','08','09'),
						'4th_bunch' => array('10','11','12'),
						);
$config['voucher_types'] = array('bank','cash','journal','contra');
$config['alphaBatch'] = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z','AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

$config['UTGST'] = array('Chandigarh','Dadra and Nagar Haveli','Daman and Diu','Lakshadweep','Andaman and Nicobar islands');

include 'DefaultLedgerIds.php';
include 'customization.php';