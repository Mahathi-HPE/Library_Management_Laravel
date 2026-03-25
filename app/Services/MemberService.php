<?php

namespace App\Services;

use App\Models\Member;

class MemberService
{
    public function findByUserId(int $uid): ?Member
    {
        return Member::findByUserId($uid);
    }

    public function adminManageUsersTable()
    {
        return Member::adminManageUsersTable();
    }
}
