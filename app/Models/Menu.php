<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    protected $fillable = ['name', 'route', 'icon', 'order'];

    public function hasRole($role)
    {
        return DB::table('menu_role')->where('menu_id', $this->id)->where('role', $role)->exists();
    }

    public function assignRole($role)
    {
        DB::table('menu_role')->insertOrIgnore(['menu_id' => $this->id, 'role' => $role]);
    }

    public function removeRole($role)
    {
        DB::table('menu_role')->where('menu_id', $this->id)->where('role', $role)->delete();
    }

    public function getRoles()
    {
        return DB::table('menu_role')->where('menu_id', $this->id)->pluck('role');
    }
}
