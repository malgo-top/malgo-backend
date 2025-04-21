<?php

// app/Models/GasBill.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ESolution\DBEncryption\Traits\EncryptedAttribute;
use Illuminate\Support\Facades\Storage;

class GasBill extends Model
{
    use EncryptedAttribute;

    protected $fillable = ['total', 'heads_201', 'heads_301', "bill_doc"];

    protected $encryptable = [
        "bill_doc"
    ];

    protected $appends = [
        'bill_doc_url',
    ];

    // Accessor for generating the temporary URL
    public function getBillDocUrlAttribute()
    {
        return $this->generateTemporaryUrl($this->bill_doc);
    }

    
    private function generateTemporaryUrl($path)
    {
        if (!$path) return null;

        $cleanPath = ltrim(preg_replace('/\/+/', '/', $path), '/');

        return Storage::disk('s3')->temporaryUrl(
            $path,
            now()->addHour()
        );
    }


}
