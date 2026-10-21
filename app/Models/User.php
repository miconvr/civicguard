<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
        $disk = Storage::disk('report_photos');
        $directory = 'profile-photos/'.$this->id;

        foreach ($disk->files($directory) as $path) {
            if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $path)) {
                return $path;
            }
        }

        return null;
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
