<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function avatarPath(): ?string
    {
        $path = Cache::rememberForever($this->avatarPathCacheKey(), function () {
            $directory = 'profile-photos/'.$this->id;

            foreach (Storage::disk('report_photos')->files($directory) as $path) {
                if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $path)) {
                    return $path;
                }
            }

            // Cache missing avatars too, so pages with many users do not
            // repeatedly list the storage directory for every render.
            return '';
        });

        return $path !== '' ? $path : null;
    }

    public function forgetAvatarPathCache(): void
    {
        Cache::forget($this->avatarPathCacheKey());
    }

    private function avatarPathCacheKey(): string
    {
        return 'user-avatar-path:'.$this->getKey();
    }

    public function residentVerification(): HasOne
    {
        return $this->hasOne(ResidentVerification::class);
    }

    public function hasVerifiedResidence(): bool
    {
        return $this->residentVerification?->status === ResidentVerification::VERIFIED;
    }
}
