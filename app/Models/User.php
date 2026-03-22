<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'Users';

    protected $primaryKey = 'Uid';

    public $timestamps = false;

    protected $fillable = [
        'Username',
        'Password',
    ];

    protected $hidden = [
        'Password',
    ];

    protected function casts(): array
    {
        return [
            'Password' => 'hashed',
        ];
    }

    public function getAuthPassword(): string
    {
        return (string) $this->getAttribute('Password');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'UserRole', 'Uid', 'Rid', 'Uid', 'Rid');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'UserMember', 'Uid', 'Mid', 'Uid', 'Mid');
    }

    public static function findByUsernameWithRole(string $username): ?self
    {
        return self::query()
            ->select('Users.*', 'Roles.RName')
            ->leftJoin('UserRole', 'UserRole.Uid', '=', 'Users.Uid')
            ->leftJoin('Roles', 'Roles.Rid', '=', 'UserRole.Rid')
            ->where('Users.Username', $username)
            ->first();
    }

    public static function createMemberUser(array $data): void
    {
        DB::transaction(function () use ($data): void {
            $user = self::query()->create([
                'Username' => $data['username'],
                'Password' => Hash::make($data['password']),
            ]);

            $member = Member::query()->create([
                'MemName' => $data['name'],
                'MemEmail' => $data['email'],
                'MemLoc' => $data['location'],
            ]);

            $role = Role::query()->firstOrCreate(['RName' => 'User']);

            DB::table('UserRole')->insert([
                'Rid' => $role->Rid,
                'Uid' => $user->Uid,
            ]);

            DB::table('UserMember')->insert([
                'Uid' => $user->Uid,
                'Mid' => $member->Mid,
            ]);
        });
    }
}
