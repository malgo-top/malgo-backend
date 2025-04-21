<?php

// app/Models/WaterBill.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ESolution\DBEncryption\Traits\EncryptedAttribute;
use Illuminate\Support\Facades\Storage;

class WaterBill extends Model
{
    use EncryptedAttribute;

    protected $fillable = [
        'metros_cubicos_201', 'metros_cubicos_301',
        'acueducto_cargo_fijo', 'acueducto_consumo_residencial_basico_quantity',
        'acueducto_consumo_residencial_basico_subtotal', 'acueducto_consumo_residencial_superior_quantity',
        'acueducto_consumo_residencial_superior_subtotal', 'alcantarillado_cargo_fijo',
        'alcantarillado_consumo_residencial_basico_quantity', 'alcantarillado_consumo_residencial_basico_subtotal',
        'alcantarillado_consumo_residencial_superior_quantity', 'alcantarillado_consumo_residencial_superior_subtotal',
        'bill_doc'
    ];

    protected $encryptable = ['bill_doc'];
    
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
            now()->addMinutes(30)
        );
    }


}
