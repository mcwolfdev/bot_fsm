<?php


namespace App\Repositories\Bots\Telegram\FSM\States;

use App\Models\TelegramBotContext;
use App\Repositories\Bots\Telegram\FSM\StateHandler;

class AddName extends StateHandler
{

    public function run()
    {

        $this->sendChatAction([
            'action' => 'typing'
        ]);


        if ($this->getState() == 'AddName::Pause'){
            $this->sendMessage([
                'text' => "📝 Введите <b>Имя</b>",
                'parse_mode' => 'html',
            ]);

            $this->applyState('AddName::Run');
            return;
        }

        if ($this->getState() == 'AddName::Run'){

            $handler = new AddNameConfirm($this->bot,$this->update);
            $handler->run();
            $this->applyState('AddNameConfirm');

        }
    }

    public function processCallback()
    {
        return true;
    }

}
