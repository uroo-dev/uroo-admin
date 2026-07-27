<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Credential extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'type', 'label', 'username', 'password_encrypted', 'is_favorite',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setPasswordAttribute(?string $value): void
    {
        $this->attributes['password_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getPasswordAttribute(): ?string
    {
        return $this->password_encrypted ? Crypt::decryptString($this->password_encrypted) : null;
    }

    public static function types(): array
    {
        return [
            'hosting', 'vps', 'ssh', 'database', 'cpanel',
            'cloud', 'ftp', 'api_key', 'email',
        ];
    }
}