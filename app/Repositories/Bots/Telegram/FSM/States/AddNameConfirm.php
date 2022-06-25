<?php


namespace App\Repositories\Bots\Telegram\FSM\States;


use App\Models\User;
use WeStacks\TeleBot\Objects\InlineKeyboardButton;
use WeStacks\TeleBot\Objects\Keyboard;
use App\Repositories\Bots\Telegram\FSM\StateHandler;

class AddNameConfirm extends StateHandler
{

    public function run()
    {

        $this->sendChatAction([
            'action' => 'typing'
        ]);

            $name = $this->update->message->text;

            User::where('telegram_id', $this->update->user()->id)->update(['last_name' => $name]);
/*        User::where('telegram_id', $this->update->user()->id)->update([
            'name' => $this->update->user()->first_name,
            'last_name' => $name,
            'username' => $this->update->user()->first_name,
            'telegram_id' => $this->update->user()->id,
        ]);*/

        $replyOptions['inline_keyboard'][] = [
            new InlineKeyboardButton([
                'text' => "❌ Исправляем!",
                'callback_data' => "again_name",
            ]),
            new InlineKeyboardButton([
                'text' => "✅ Все верно!",
                'callback_data' => "confirm_name",
            ]),
        ];


        $this->sendMessage([
            'text' => "<b>$name</b> - все верно? Без ошибок?",
            'parse_mode' => 'html',
            'reply_markup' => Keyboard::create($replyOptions)
        ]);


    }

    public function processCallback()
    {
        $choise = $this->update->callback_query->data;
        switch ($choise){
            case 'again_name':
                // Удалим предыдущую менюшку

                $this->bot->answerCallbackQuery([
                    'callback_query_id' => $this->update->callback_query->id,
                    'text' => '❌'
                ]);

                $this->bot->deleteMessage([
                    'chat_id' => $this->update->callback_query->from->id,
                    'message_id' => $this->update->callback_query->message->message_id
                ]);

                //$this->applyContext($this->context);
                //$this->stateMachine->apply('to_AddName');


                //TelegramBotContext::where(['telegram_id ', $this->update->user()->id])->update(['state' => 'AddName::Pause']);
                $this->applyState("AddName::Pause");
                $handler = new AddName($this->bot,$this->update);
                $handler->run();

                break;

            case 'confirm_name':

                $this->okAndDelete(); // Галочка и удаление

                $names = User::where('telegram_id', $this->update->user()->id)->first();

                $this->bot->sendMessage([
                    'chat_id' => $this->update->callback_query->from->id,
                    'text' => "✅ Вы ввели имя: <b>$names->last_name</b>",
                    'parse_mode' => 'html'
                ]);



                //$this->applyState("MainMenu");
/*
                User::where('telegram_id', $this->update->user()->id)->update([
                    'name' => $this->update->user()->first_name,
                    'last_name' => $persona->last_name,
                    'username' => $this->update->user()->first_name,
                    'telegram_id' => $this->update->user()->id,
                ]);*/

                $handler = new MainMenu($this->bot,$this->update);
                $handler->run();

                $this->applyState("MainMenu");

                break;
            default:

                break;
        }

        return true;
    }

}
