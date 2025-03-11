<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryInvoicesRequest extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_invoices_request';
	protected $fillable = [
       'fournisseur_id',
	   'patient_id',
	   'cmd_id',
	   'type',
	   'date_cmd',
	   'datein',
	   'date_invoice',
	   'date_due',	
	   'reference',	
	   'notes',
	   'total',
	   'discount',
	   'qst',
	   'gst',
	   'inv_balance',
	   'remark',
	   'clinic_num',
	   'clinic_inv_num',
	   'user_id',	
	   'active',
	   'visit_num',
	   'doctor_num',
	   'right_rx_data',
	   'left_rx_data',
	   'cmd_email_pat',
	   'cmd_sms_pat',
	   'cmd_email_supp',
	   'cmd_fax_supp',
	   'inv_email_pat',
	   'cmd_comment',
	   'cmd_cl',
	   'livrer',
	   'livrer_date',
	   'livrer_user',
	   'rxcl_id',
	   'typeadjacement',
	   'cr_note',
	   'external_monture',
	   'is_estimation',
	   'is_warranty',
	   'cmd_cabaret_num',
	   'supplier_invoice_ref',
	   'gstock',
	   'fromstock',
	   'nbaccount',
	   'is_valid1',
	   'is_valid2',
	   'email_sent',
	   'cpaid',
	   'cdone',
	   'invoice_sup',
	   'free',
	   'date_delivery',
	   'typein',
	   'newitem',
	   'fournisseur_fid',
	   'quote',
	   'client_id'
	   ];    
}
