<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PunchoutSession extends Model
{
    use HasFactory;

    // "cxml" was missing, so Eloquent silently dropped it and punchout checkout
    // (which parses the stored setup request) could not work.
    protected $fillable = ['buyer_cookie', 'payload_id', 'deployment', 'browser_form_post_url', 'supplier_setup_url', 'cxml'];
}
