<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LibraryInitialDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('Roles')->insertOrIgnore([
            ['Rid' => 1, 'RName' => 'User'],
            ['Rid' => 2, 'RName' => 'Admin'],
        ]);

        DB::table('Members')->insertOrIgnore([
            ['Mid' => 1, 'MemName' => 'Member One', 'MemEmail' => 'member1@library.com', 'MemLoc' => 'City A'],
        ]);

        DB::table('Users')->insertOrIgnore([
            ['Uid' => 1, 'Username' => 'member1@library.com', 'Password' => Hash::make('password123')],
            ['Uid' => 2, 'Username' => 'admin', 'Password' => Hash::make('password123')],
        ]);

        // Upgrade known legacy plain-text seeded passwords if they already exist.
        DB::table('Users')
            ->where('Uid', 1)
            ->where('Password', 'password123')
            ->update(['Password' => Hash::make('password123')]);

        DB::table('Users')
            ->where('Uid', 2)
            ->where('Password', 'password123')
            ->update(['Password' => Hash::make('password123')]);

        DB::table('UserRole')->insertOrIgnore([
            ['Rid' => 1, 'Uid' => 1],
            ['Rid' => 2, 'Uid' => 2],
        ]);

        DB::table('UserMember')->insertOrIgnore([
            ['Uid' => 1, 'Mid' => 1],
        ]);

        DB::table('Author')->insertOrIgnore([
            ['Aid' => 1, 'AuthLoc' => 'UK', 'AuthEmail' => 'orwell@example.com', 'AuthName' => 'George Orwell'],
        ]);

        DB::table('Books')->insertOrIgnore([
            ['Bid' => 1, 'Title' => '1984', 'PubDate' => '1949-06-08', 'Price' => 399.00],
        ]);

        DB::table('Copies')->upsert([
            ['Cid' => 1, 'Bid' => 1, 'Status' => 'Available'],
            ['Cid' => 2, 'Bid' => 1, 'Status' => 'Available'],
            ['Cid' => 3, 'Bid' => 1, 'Status' => 'Available'],
        ], ['Cid'], ['Bid', 'Status']);

        DB::table('BookAuthor')->insertOrIgnore([
            ['Bid' => 1, 'Aid' => 1],
        ]);

        // If this seeder was run multiple times before, trim duplicate extra copies
        // only when those extras are not referenced by any borrow records.
        DB::table('Copies')
            ->where('Bid', 1)
            ->whereNotIn('Cid', [1, 2, 3])
            ->whereNotExists(function ($query): void {
                $query->from('Borrows')
                    ->whereColumn('Borrows.Cid', 'Copies.Cid')
                    ->selectRaw('1');
            })
            ->delete();
    }
}