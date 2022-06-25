<?php


namespace App\Repositories\Bots\Telegram\FSM\States;

use App\Repositories\Bots\Telegram\FSM\StateHandler;

class AddName extends StateHandler
{

    public function run()
    {

        $this->sendChatAction([
            'action' => 'typing'
        ]);

            $this->sendMessage([
                'text' => "📝 Введите <b>Имя</b>",
                'parse_mode' => 'html',
            ]);

            $this->applyState('AddNameConfirm');
            return;
    }

    public function processCallback()
    {
        return true;
    }

}
