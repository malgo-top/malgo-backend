<?php
// app/Models/BillType.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillType extends Model
{
    protected $fillable = [
        'name', 'rent_contract_id', 'water_bill_id',
        'gas_bill_id', 'electricity_bill_id'
    ];

    public function rentContract() {
        return $this->belongsTo(RentContract::class);
    }

    public function waterBill() {
        return $this->belongsTo(WaterBill::class);
    }

    public function gasBill() {
        return $this->belongsTo(GasBill::class);
    }

    public function electricityBill() {
        return $this->belongsTo(ElectricityBill::class);
    }
}
