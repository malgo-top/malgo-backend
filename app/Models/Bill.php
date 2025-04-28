<?php
// app/Models/Bill.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ESolution\DBEncryption\Traits\EncryptedAttribute;

class Bill extends Model
{
    use EncryptedAttribute;

    protected $fillable = [
        'bill_type_id', 'start_date', "property_id",
        'end_date', 'due_date', 'amount', 'status', "user_id", "late_notice_sent"
    ];

    protected $encryptable = [
        'start_date', 'end_date', 'amount'
    ];

    public function billType() {
        return $this->belongsTo(BillType::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
