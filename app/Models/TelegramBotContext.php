<?php

namespace App\Models;

use Finite\StatefulInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramBotContext extends Model  implements StatefulInterface
{
    use HasFactory;

    protected $fillable = [
        'telegram_id',
        'state',
        'last_message'
    ];


    public function getFiniteState()
    {
        // TODO: Implement getFiniteState() method.
        return $this->state;
    }

    public function setFiniteState($state)
    {
        // TODO: Implement setFiniteState() method.
        $this->state = $state;
        $this->save();
    }

    public function getUser(){
        return User::where('telegram_id',$this->telegram_id)->first();
    }


}
