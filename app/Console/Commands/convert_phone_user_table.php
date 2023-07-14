<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class convert_phone_user_table extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:convert_phone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = DB::table('user')->get();
        foreach($users as $user) {
            $phone = \App\Helpers\Helper::clearOnlyLettersFromString($user->phone);
            if(isset($phone[0]) && $phone[0] == 8) $phone[0] = 7;
            DB::table('user')
                ->where('user_id', $user->user_id)
                ->update(['phone'=>$phone]);
        }
    }
}
