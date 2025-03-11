<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryInvSerials extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_invoice_serials';
	protected $fillable = [
       'clinic_num',
	   'user_num',
	   'p_serial_code',
	   'p_sequence_num',
	   's_serial_code',
	   's_sequence_num',
	   'rp_serial_code',
	   'rp_sequence_num',
	   'rs_serial_code',
	   'rs_sequence_num',
	   'rm_serial_code',
	   'rm_sequence_num',
	   'adj_serial_code',
	   'adj_sequence_num',
	   'qst_num',
	   'gst_num',
	   'inv_note',
	   'cn_serial_code',
	   'cn_sequence_num',
	   'cns_serial_code',
	   'cns_sequence_num',
	   'cmd_serial_code',
	   'cmd_sequence_num',
	   'acc_serial_code',
	   'acc_sequence_num',
	   'med_note'
    ];    
}
