<?php


namespace App\Repositories\Bots\Telegram\FSM\States;


use App\Models\User;
use WeStacks\TeleBot\Objects\InlineKeyboardButton;
use WeStacks\TeleBot\Objects\Keyboard;
use App\Repositories\Bots\Telegram\FSM\StateHandler;

class MainMenu extends StateHandler
{

    public function run()
    {

        $this->sendChatAction([
            'action' => 'typing'
        ]);

        $replyOptions['inline_keyboard'][] = [
            new InlineKeyboardButton([
                'text' => "📝 Тест ✴️",
                'callback_data' => "test",
            ]),
            new InlineKeyboardButton([
                'text' => "📖  Посмотреть данные",
                'callback_data' => "open",
            ]),
            new InlineKeyboardButton([
                'text' => "🗑 Удалить",
                'callback_data' => "delete",
            ]),
        ];

        $this->sendMessage([
            'text' => "ℹ️ <b>Cтатус:</b>
📊 <b>Статистика:</b>
➡️ <b>Выберите профиль,</b>. Вы можете посмотреть данные, либо же удалить данные из системы.",
            'parse_mode' => 'html',
            'reply_markup' => Keyboard::create($replyOptions),
            'disable_web_page_preview'=>'true'
        ]);

    }

    public function processCallback()
    {
        $choise = $this->update->callback_query->data;
        switch ($choise) {
            case 'new':
                $this->sendMessage([
                    'text' => "callback_query <b>new</b>",
                    'parse_mode' => 'html',
                ]);
                break;
            case 'delete':
                $delUser = User::where('telegram_id', $this->update->user()->id)->first();
                $delUser->delete();

                $this->applyState("start");

                $this->sendMessage([
                    'text' => "✅<b> Удалено</b>",
                    'parse_mode' => 'html',
                ]);

                $this->applyState("start");
                $handler = new Start($this->bot,$this->update);
                $handler->run();
                //$this->applyState("start");
                break;
            default:
                $this->sendMessage([
                    'text' => $choise,
                    'parse_mode' => 'html',
                ]);
                break;
            case 'open':
                $this->sendMessage([
                    'text' => "callback_query <b>open</b>",
                    'parse_mode' => 'html',
                ]);
                break;
            case 'test':
                $this->sendMessage([
                    'text' => "callback_query <b>test</b>)",
                    'parse_mode' => 'html',
                ]);
                break;
        }

        return true;


    }

}
