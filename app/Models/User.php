<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;

    // Mass assignment için izin verilen alanlar
    protected $fillable = [
        'company_id',
        'name',
        'surname',
        'email',
        'phone',
    ];

    // Şifreli alanlar (eğer kullanıyorsan)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Date casting işlemi
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Şirketle ilişkisini tanımlıyoruz
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
