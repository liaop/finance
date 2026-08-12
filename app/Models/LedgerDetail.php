<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LedgerDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ledger_details';

    protected $fillable = [
        'ledger_id',
        'type',
        'category',
        'amount',
        'currency',
        'occurred_at',
        'description',
        'merchant',
        'attachment_url',
    ];

    protected $casts = [
        'amount' => 'float',
        'occurred_at' => 'datetime',
    ];

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }
}
