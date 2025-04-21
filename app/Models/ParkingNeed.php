<?php
// app/Models/ParkingNeed.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingNeed extends Model
{
    protected $fillable = ['tenant_application_id', 'vehicle_type'];

    public function tenantApplication() {
        return $this->belongsTo(TenantApplication::class);
    }
}
