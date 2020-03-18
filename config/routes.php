<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
/*echo "<pre>";
print_r($_SERVER['REDIRECT_QUERY_STRING']);exit();*/

$route['default_controller'] = 'auth';
$route['404_override'] = 'my_404';
$route['translate_uri_dashes'] = FALSE;
/*$route['sales_voucher'] = 'MainVoucher';*/
$route['sales_ledger'] = 'MainVoucher';
$route['sales_credit_note_voucher'] = 'Sales_voucher/sales_credit_note_voucher';
$route['sales_debit_note_voucher'] = 'Sales_voucher/sales_debit_note_voucher';
$route['purchase_credit_ledger'] = 'Purchase_ledger/purchase_credit_ledger';
$route['purchase_credit_note_voucher'] = 'Purchase_voucher/purchase_credit_note_voucher';
$route['purchase_debit_ledger'] = 'Purchase_ledger/purchase_debit_ledger';
$route['purchase_debit_note_voucher'] = 'Purchase_voucher/purchase_debit_note_voucher';
$route['boe_ledger'] = 'Payment_ledger/boe_ledger';
$route['sales_ledger/(:any)'] = 'MainVoucher';
/*$route['journal_voucher'] = 'General_voucher';*/

$route['ledger_view/details/(:any)'] = 'ledger_view/index/$1';