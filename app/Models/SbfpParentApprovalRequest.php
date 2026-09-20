<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SbfpParentApprovalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'sbfp_participant_id',
        'email',
        'token_hash',
        'weight',
        'height',
        'bmi',
        'bmi_category',
        'status',
        'expires_at',
        'sent_at',
        'responded_at',
        'decision_reason',
        'closed_reason',
        'closed_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'sent_at' => 'datetime',
            'responded_at' => 'datetime',
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
            'bmi' => 'decimal:2',
        ];
    }

    public function participant()
    {
        return $this->belongsTo(SbfpParticipant::class, 'sbfp_participant_id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }
}
