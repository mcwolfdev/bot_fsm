<?php


namespace App\Repositories\Bots\Telegram\FSM\States;


use App\Repositories\Bots\Telegram\FSM\StateHandler;


class Start extends StateHandler
{

    public function run()
    {

        $this->sendChatAction([
            'action' => 'typing'
        ]);

        $this->sendMessage([
            'text'=>"🤖 <b>Бот$</b> 🤖

<i>Это рабочий Бот состояний!</i>
",
            'parse_mode'=>'html'
            ]);

            $this->applyState('AddName');
            $this->handle();
    }

    public function processCallback()
    {
        return true;
    }

}
