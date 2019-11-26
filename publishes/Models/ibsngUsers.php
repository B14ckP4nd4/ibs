<?php

namespace App;

use App\EncryptorTraits\Encryptable;
use Illuminate\Database\Eloquent\Model;

class ibsngUsers extends Model
{
    use Encryptable;

    protected $encryptable = [
        'password',
    ];

    protected $guarded = ['id'];
    protected $table = 'ibsngUsers';
}
