<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanCuti extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'tanggal_cuti', 'jenis_cuti', 'keterangan', 'file', 'status', 'approved_by', 'approved_at', 'rejected_by', 'rejected_at', 'created_by'];

    /**
     * Get the user that owns the PengajuanCuti
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the user that owns the PengajuanCuti
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the rejectedBy that owns the PengajuanCuti
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id');
    }
}
