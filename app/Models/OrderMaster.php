<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderMaster extends Model
{   	
	protected $table = 'order_master';
	use HasFactory;
	protected $fillable = ['is_fulfilled', 'user_id', 'order_type', 'ship_method', 'ship_tracking_number', 'pay_method', 'shipping_cost', 
		'packaging_fee', 'tax_label', 'tax_percentage', 'fuelcharge_percentage', 'fuelcharges', 'handlingfee', 'tax_collected', 
		'payable_amount', 'discount_amount', 'oauth_token', 'giftcertificate_amt', 'giftcert_id', 'discount_id', 'handling_msg', 
		'callback_number', 'order_status', 'approved_dt', 'shipment_appdt', 'cancel_dt', 'if_items_unavailabel', 'delivery_instructions', 
		'bill_fname', 'bill_lname', 'bill_email', 'bill_mobile', 'bill_fax', 'bill_landline', 'group_admin_approval', 'weight', 'size', 
		'pickup_instruction', 'delivery_instructions', 'shipping_by', 'delivery_times', 'quotation_status', 'order_from',
		'bill_compname', 'bill_name_oncard', 'dwn_link_status', 'dwn_count', 'bill_ads1', 'bill_ads2', 'bill_city', 'bill_state', 
		'bill_zip', 'bill_country', 'ship_fname', 'ship_lname', 'company', 'invoice_content', 'email_send', 'order_updated_times','order_alt_text',
		'ship_email', 'trans_id', 'error_reason', 'cc_type', 'cc_number', 'cc_cvv2', 'cc_expiry', 'pay_approved_by', 'bill_ipaddress',
		'ship_mobile', 'ship_fax', 'ship_landline', 'ship_ads1', 'ship_ads2', 'ship_country', 'ship_city', 'ship_state', 'ship_zip', 'date_entered'
	];
}