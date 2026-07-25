<?php

namespace Modules\Credential\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Credential extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'client_id', 'type', 'label', 'provider', 'domain',
        'host_ip', 'username', 'password_encrypted', 'database_name',
        'database_user', 'database_password_encrypted', 'ssh_key_encrypted',
        'auth_url', 'notes', 'expires_at', 'is_favorite', 'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_favorite' => 'boolean',
            'expires_at' => 'date',
            'last_used_at' => 'datetime',
        ];
    }

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

    public function setDatabasePasswordAttribute(?string $value): void
    {
        $this->attributes['database_password_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getDatabasePasswordAttribute(): ?string
    {
        return $this->database_password_encrypted ? Crypt::decryptString($this->database_password_encrypted) : null;
    }

    public function setSshKeyAttribute(?string $value): void
    {
        $this->attributes['ssh_key_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getSshKeyAttribute(): ?string
    {
        return $this->ssh_key_encrypted ? Crypt::decryptString($this->ssh_key_encrypted) : null;
    }

    public static function types(): array
    {
        return [
            'hosting', 'vps', 'ssh', 'database', 'cpanel',
            'cloud', 'ftp', 'api_key', 'email',
        ];
    }
}