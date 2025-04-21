<?php

// app/Models/FinancialResponsible.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ESolution\DBEncryption\Traits\EncryptedAttribute;
use Illuminate\Support\Facades\Storage;



class FinancialResponsible extends Model
{
    use EncryptedAttribute;

    protected $fillable = [
        'tenant_application_id', 'full_name', 'document_type', 'document_number',
        "document_id", 'phone_number', 'email', 'document_type',"principal",
        'document_certf', 'document_pay_1', 'document_pay_2', 'document_pay_3',
        'employment_status', 'monthly_salary', 'birthdate', 'nationality',
        'start_current_job_date', 'guarantor_full_name', 'guarantor_document_type',
        'guarantor_document_number', 'guarantor_property_cert'
    ];

    protected $encryptable = [
        'full_name', 'document_type', 'document_number',
        "document_id", 'phone_number', 'email', 'document_type',
        'document_certf', 'document_pay_1', 'document_pay_2', 'document_pay_3',
        'employment_status', 'nationality','monthly_salary', 
         'guarantor_full_name', 'guarantor_document_type',
        'guarantor_document_number', 'guarantor_property_cert'
    ];

    // Relationships
    public function tenantApplication() {
        return $this->belongsTo(TenantApplication::class);
    }

    public function additionalIncomes() {
        return $this->hasMany(AdditionalIncome::class);
    }

    protected $appends = [
        'document_id_url',
        'document_certf_url',
        'document_pay_1_url',
        'document_pay_2_url',
        'document_pay_3_url',
        'guarantor_property_cert_url',
    ];


    // Virtual accessors for temporary URLs
    public function getDocumentIdUrlAttribute()
    {
        return $this->generateTemporaryUrl($this->document_id);
    }

    public function getDocumentCertfUrlAttribute()
    {
        return $this->generateTemporaryUrl($this->document_certf);
    }

    public function getDocumentPay1UrlAttribute()
    {
        return $this->generateTemporaryUrl($this->document_pay_1);
    }

    public function getDocumentPay2UrlAttribute()
    {
        return $this->generateTemporaryUrl($this->document_pay_2);
    }

    public function getDocumentPay3UrlAttribute()
    {
        return $this->generateTemporaryUrl($this->document_pay_3);
    }

    public function getGuarantorPropertyCertUrlAttribute()
    {
        return $this->generateTemporaryUrl($this->guarantor_property_cert);
    }

    // Shared helper method
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
