<?php
/* Sales Ledgers */
$config['sales_ledger']['CGST@X'] = 14;
$config['sales_ledger']['SGST@X'] = 15;
$config['sales_ledger']['UTGST@X'] = 23;
$config['sales_ledger']['IGST@X'] = 16;
$config['sales_ledger']['CESS@X'] = 18;
$config['sales_ledger']['IGST_PAY'] = 21;
$config['sales_ledger']['IGST_REV'] = 20;
$config['sales_ledger']['CESS_REV'] = 22;
$config['sales_ledger']['TDS_REV'] = 12;
$config['sales_ledger']['TCS_PAY'] = 13;
$config['sales_ledger']['SALES'] = 2;
$config['sales_ledger']['CUSTOMER'] = 1;
$config['sales_ledger']['Freight'] = 5;
$config['sales_ledger']['Insurance'] = 9;
$config['sales_ledger']['Packing'] = 8;
$config['sales_ledger']['Incidental'] = 10;
$config['sales_ledger']['Inclusive'] = 6;
$config['sales_ledger']['Exclusive'] = 4;
$config['sales_ledger']['Discount'] = 3;
$config['sales_ledger']['RoundOff_Given'] = 11;
$config['sales_ledger']['RoundOff_Received'] = 19;

/* Purchase Ledgers */
$config['purchase_ledger']['CGST@X'] = 43;
$config['purchase_ledger']['SGST@X'] = 44;
$config['purchase_ledger']['UTGST@X'] = 44;
$config['purchase_ledger']['IGST@X'] = 45;
$config['purchase_ledger']['CESS@X'] = 46;
$config['purchase_ledger']['IGST_PAY'] = 48;
$config['purchase_ledger']['IGST_REV'] = 47;
$config['purchase_ledger']['CESS_REV'] = 55;
$config['purchase_ledger']['CESS_PAY'] = 56;
$config['purchase_ledger']['CGST_PAY'] = 54;
$config['purchase_ledger']['CGST_REV'] = 53;
$config['purchase_ledger']['SGST_REV'] = 51;
$config['purchase_ledger']['SGST_PAY'] = 49;
$config['purchase_ledger']['UTGST_REV'] = 52;
$config['purchase_ledger']['UTGST_PAY'] = 50;
$config['purchase_ledger']['TCS_REV'] = 41;
$config['purchase_ledger']['TDS_PAY'] = 42;
$config['purchase_ledger']['PURCHASE'] = 57;
$config['purchase_ledger']['SUPPLIER'] = 31;
$config['purchase_ledger']['Freight'] = 34;
$config['purchase_ledger']['Insurance'] = 35;
$config['purchase_ledger']['Packing'] = 36;
$config['purchase_ledger']['Incidental'] = 37;
$config['purchase_ledger']['Inclusive'] = 38;
$config['purchase_ledger']['Exclusive'] = 33;
$config['purchase_ledger']['Discount'] = 32;
$config['purchase_ledger']['RoundOff_Given'] = 11;
$config['purchase_ledger']['RoundOff_Received'] = 19;

/* Advance Ledgers */
$config['advance_ledger']['CUSTOMER'] = 1;
$config['advance_ledger']['CGST@X'] = 14;
$config['advance_ledger']['SGST@X'] = 15;
$config['advance_ledger']['UTGST@X'] = 23;
$config['advance_ledger']['IGST@X'] = 16;
$config['advance_ledger']['CESS@X'] = 18;
$config['advance_ledger']['RoundOff_Given'] = 11;
$config['advance_ledger']['RoundOff_Received'] = 19;
$config['advance_ledger']['Cash_Payment'] = 24;
$config['advance_ledger']['Other_Payment'] = 29; 
$config['advance_ledger']['bank'] = 30;
$config['advance_ledger']['EXCESS'] = 25;
$config['advance_ledger']['CGST_paid_on_advance'] = 95;
$config['advance_ledger']['SGST_paid_on_advance'] = 96;
$config['advance_ledger']['IGST_paid_on_advance'] = 97;
$config['advance_ledger']['UTGST_paid_on_advance'] = 98;
$config['advance_ledger']['Cess_paid_on_advance'] = 99;

/* Receipt Ledgers */
$config['receipt_ledger']['CUSTOMER'] = 1;
$config['receipt_ledger']['Cash_Payment'] = 24;
$config['receipt_ledger']['Other_Payment'] = 29;
$config['receipt_ledger']['Bank'] = 30;
$config['receipt_ledger']['Discount'] = 61;
$config['receipt_ledger']['ExcessGain'] = 27;
$config['receipt_ledger']['ExcessLoss'] = 26;
$config['receipt_ledger']['Other_Charges'] = 28;
$config['receipt_ledger']['RoundOff_Given'] = 11;
$config['receipt_ledger']['RoundOff_Received'] = 19;

/* Bank Ledgers */
$config['bank_ledger']['bank'] = 30;

/* Expense Ledgers */
$config['expense_ledger']['CGST@X'] = 43;
$config['expense_ledger']['SGST@X'] = 44;
$config['expense_ledger']['UTGST@X'] = 44;
$config['expense_ledger']['IGST@X'] = 45;
$config['expense_ledger']['CESS@X'] = 46;
$config['expense_ledger']['IGST_PAY'] = 48;
$config['expense_ledger']['IGST_REV'] = 47;
$config['expense_ledger']['CESS_REV'] = 55;
$config['expense_ledger']['CESS_PAY'] = 56;
$config['expense_ledger']['CGST_PAY'] = 54;
$config['expense_ledger']['CGST_REV'] = 53;
$config['expense_ledger']['SGST_REV'] = 51;
$config['expense_ledger']['SGST_PAY'] = 49;
$config['expense_ledger']['UTGST_REV'] = 52;
$config['expense_ledger']['UTGST_PAY'] = 50;
$config['expense_ledger']['TCS_REV'] = 41;
$config['expense_ledger']['TDS_PAY'] = 42;
$config['expense_ledger']['EXPENSE'] = 58;
$config['expense_ledger']['EXPENSE_ITEM'] = 59;
$config['expense_ledger']['SUPPLIER'] = 31;
$config['expense_ledger']['Freight'] = 34;
$config['expense_ledger']['Insurance'] = 35;
$config['expense_ledger']['Packing'] = 36;
$config['expense_ledger']['Incidental'] = 37;
$config['expense_ledger']['Inclusive'] = 38;
$config['expense_ledger']['Exclusive'] = 33;
$config['expense_ledger']['Discount'] = 32;
$config['expense_ledger']['RoundOff_Given'] = 11;
$config['expense_ledger']['RoundOff_Received'] = 19;

/* Bank Ledgers */
$config['bank_ledger']['bank'] = 30;

/* Payment Ledger */
$config['payment_ledger']['SUPPLIER'] = 31;
$config['payment_ledger']['Cash_Payment'] = 24;
$config['payment_ledger']['Other_Payment'] = 29;
$config['payment_ledger']['Bank'] = 30;
$config['payment_ledger']['Discount'] = 62;
$config['payment_ledger']['ExcessPay'] = 60;
$config['payment_ledger']['ExcessGain'] = 27;
$config['payment_ledger']['ExcessLoss'] = 26;
$config['payment_ledger']['Other_Charges'] = 28;
$config['payment_ledger']['RoundOff_Given'] = 11;
$config['payment_ledger']['RoundOff_Received'] = 19;


/* Refund Ledger */
$config['refund_ledger']['CUSTOMER'] = 1;
$config['refund_ledger']['Cash_Payment'] = 24;
$config['refund_ledger']['Other_Payment'] = 29;
$config['refund_ledger']['Bank'] = 30;
$config['refund_ledger']['CGST@X'] = 14;
$config['refund_ledger']['SGST@X'] = 15;
$config['refund_ledger']['UTGST@X'] = 23;
$config['refund_ledger']['IGST@X'] = 16;
$config['refund_ledger']['CESS@X'] = 18;

/* BOE ledger */
$config['boe_ledger']['IGST@X'] = 16;
$config['boe_ledger']['CESS@X'] = 18;
$config['boe_ledger']['OtherImp'] = 64;
$config['boe_ledger']['customDuty'] = 63;
$config['boe_ledger']['bank'] = 30;

/* General Ledger */
$config['general_ledger']['SUPPLIER'] = 31;
$config['general_ledger']['Cash_Payment'] = 24;
$config['general_ledger']['Other_Payment'] = 29; 
$config['general_ledger']['Other_Charges'] = 28; 
$config['general_ledger']['bank'] = 30;
$config['general_ledger']['Advance_Tax_paid_to_Govt'] = 66;
$config['general_ledger']['Advance_Tax_Refund_by_Govt'] = 67;
$config['general_ledger']['Interest_Income'] = 68;
$config['general_ledger']['Capital_AC'] = 69;
$config['general_ledger']['Preference_Share_Capital_AC'] = 70;
$config['general_ledger']['Equity_Share_Capital_AC'] = 71;
$config['general_ledger']['Partner_AC'] = 72;
$config['general_ledger']['Cash_AC'] = 73;
$config['general_ledger']['Suspense_AC'] = 74;
$config['general_ledger']['Fixed_Deposit'] = 75;
$config['general_ledger']['Recurring_Deposit'] = 76;
$config['general_ledger']['Rent_Deposit'] = 77;
$config['general_ledger']['Electricity_Deposit'] = 78;
$config['general_ledger']['Water_Deposit'] = 79;
$config['general_ledger']['Other_Deposits'] = 80;
$config['general_ledger']['Interest_Receivable'] = 81;
$config['general_ledger']['Partner'] = 82;
$config['general_ledger']['Fixed_Assets'] = 83;
$config['general_ledger']['Other_Incomes'] = 84;
$config['general_ledger']['Input_CGST'] = 85;
$config['general_ledger']['Input_SGST'] = 86;
$config['general_ledger']['Input_UTGST'] = 87;
$config['general_ledger']['Input_IGST'] = 88;
$config['general_ledger']['Input_Cess'] = 89;
$config['general_ledger']['Output_CGST'] = 90;
$config['general_ledger']['Output_SGST'] = 91;
$config['general_ledger']['Output_UTGST'] = 92;
$config['general_ledger']['Output_IGST'] = 93;
$config['general_ledger']['Output_Cess'] = 94;
$config['general_ledger']['Income_tax_refund'] = 100;
$config['general_ledger']['Interest_on_Income_tax'] = 101;
$config['general_ledger']['Provision_Income_tax'] = 102;
$config['general_ledger']['Interest_Receivable_other'] = 103;
$config['general_ledger']['Investments'] = 104;
$config['general_ledger']['Loss_on_sale_investment'] = 105;
$config['general_ledger']['Profit_on_sale_investment'] = 106;
$config['general_ledger']['Others_income'] = 107;
$config['general_ledger']['Others_expense'] = 108;
$config['general_ledger']['Interest'] = 109;
$config['general_ledger']['Director'] = 110;
$config['general_ledger']['Interest_Payable'] = 111;
$config['general_ledger']['Tds_payable'] = 112;
$config['general_ledger']['Drawing_AC'] = 65;
$config['general_ledger']['Security_Premium'] = 113;
$config['general_ledger']['Bank_Charges'] = 114;
$config['general_ledger']['TDS_REV'] = 12;
?>