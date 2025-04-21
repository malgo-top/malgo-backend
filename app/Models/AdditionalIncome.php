<?php
// app/Models/AdditionalIncome.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ESolution\DBEncryption\Traits\EncryptedAttribute;
use Illuminate\Support\Facades\Storage;

class AdditionalIncome extends Model
{
    use EncryptedAttribute;

    protected $fillable = [
        'financial_responsible_id', 'monthly_amount', 'description', 'income_cert'
    ];

    protected $encryptable = [
        'monthly_amount', 'description', 'income_cert'
    ];

    public function financialResponsible() {
        return $this->belongsTo(FinancialResponsible::class);
    }

    protected $appends = [
        'income_cert_url',
    ];
    
    public function getIncomeCertUrlAttribute()
    {
        return $this->generateTemporaryUrl($this->income_cert);
    }

    private function generateTemporaryUrl($path)
    {
        if (!$path) return null;

        $cleanPath = ltrim(preg_replace('/\/+/', '/', $path), '/');

        return Storage::disk('s3')->temporaryUrl(
            $path,
            now()->addMinutes(30)
        );
    }

    //  // Accessor for 'income_cert' to append the temporary URL
    //  public function getIncomeCertUrlAttribute($value)
    //  {
    //      // If the value is not null, generate a temporary URL
    //      if ($value) {
    //          return Storage::disk('s3')->temporaryUrl(
    //              $value, 
    //              now()->addHour() // URL valid for 1 hour
    //          );
    //      }
    //      return null; // Return null if no file path exists
    //  }

}
