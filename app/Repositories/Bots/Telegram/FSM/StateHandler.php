<?php


namespace App\Repositories\Bots\Telegram\FSM;


use App\Models\TelegramBotContext;
use App\Models\User;
use App\Repositories\Bots\Telegram\FSM\States\AddName;
use App\Repositories\Bots\Telegram\FSM\States\AddNameConfirm;
use App\Repositories\Bots\Telegram\FSM\States\Hello;
use App\Repositories\Bots\Telegram\FSM\States\MainMenu;
use App\Repositories\Bots\Telegram\FSM\States\Start;

use WeStacks\TeleBot\Interfaces\UpdateHandler;
use WeStacks\TeleBot\Objects\Update;
use WeStacks\TeleBot\TeleBot;


class StateHandler extends UpdateHandler
{
    protected $context;
    protected $states;

    public function okAndDelete(){
        try{
            $this->bot->answerCallbackQuery([
                'callback_query_id' => $this->update->callback_query->id,
                'text' => '✅'
            ]);
            // Удалим предыдущую менюшку
            $this->bot->deleteMessage([
                'chat_id' => $this->update->callback_query->from->id,
                'message_id' => $this->update->callback_query->message->message_id
            ]);
        }catch (\Exception $e){
        }
    }

/*    public function __construct(TeleBot $bot, Update $update)
    {

    }*/


    public function applyState($context)
    {
        TelegramBotContext::where('telegram_id', $this->update->user()->id)->update(["state" => $context]);
    }

    public function getState(){
        $paramContext = TelegramBotContext::where('telegram_id', $this->update->user()->id)->first();

        if (empty($paramContext->state) || $paramContext->state == null){
            TelegramBotContext::create([
                'telegram_id' => $this->update->user()->id,
                'state' => 'start',
            ]);

        }
        return $paramContext->state;
    }

    public static function trigger(Update $update, TeleBot $bot): bool
    {
        return true;
    }


    public function handle()
    {

        $users = User::where('telegram_id', $this->update->user()->id)->first();

        if (empty($users->telegram_id)){
            User::create([
                'name' => $this->update->user()->first_name,
                'last_name' => $this->update->user()->first_name,
                'username' => $this->update->user()->first_name,
                'telegram_id' => $this->update->user()->id,
            ]);
        }

        switch ($this->getState()) {
            case 'start':
                $handler = new  Start($this->bot, $this->update);
                break;
            case 'MainMenu':
                $handler = new MainMenu($this->bot, $this->update);
                break;
            case 'AddName':
                $handler = new AddName($this->bot, $this->update);
                break;
            case 'AddNameConfirm':
                $handler = new AddNameConfirm($this->bot, $this->update);
                break;

            default:
                $handler = new Hello($this->bot, $this->update);
        }

        try {
            if (isset($this->update->message)) $handler->run();
            if (isset($this->update->callback_query)) $handler->processCallback();
        } catch (\Exception $e) {

        }

    }

}
