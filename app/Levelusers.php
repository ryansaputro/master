<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Levelusers extends Model
{
    //

    protected $table = 'ek_level_users';
    protected $primaryKey = 'id_level_user';
    public $incrementing = false;

}
