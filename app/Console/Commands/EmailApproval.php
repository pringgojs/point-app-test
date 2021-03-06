<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Point\Core\Models\User;
use Point\Framework\Models\Formulir;

class EmailApproval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:approval';

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
        $list_formulir = Formulir::where('form_status', 0)->where('approval_status', 0)->groupBy('formulirable_type')->get();
        foreach ($list_formulir as $formulir) {
            $this->sendEmail($formulir->formulirable_type);
        }
    }

    private function sendEmail($class)
    {
        $formulir_open = [];
        $list_formulir = Formulir::where('form_status', 0)->where('approval_status', 0)->where('formulirable_type', $class)->get();
        foreach ($list_formulir as $formulir) {
            array_push($formulir_open, $formulir->id);
        }

        $list_approver = $class::selectApproverList($formulir_open);
        $token = md5(date('ymdhis'));
        foreach ($list_approver as $data_approver) {
            $list_data = $class::selectApproverRequest($formulir_open, $data_approver->approval_to);
            
            $array_formulir_id = [];
            foreach ($list_data as $data) {
                array_push($array_formulir_id, $data->formulir_id);
            }

            $array_formulir_id = implode(',', $array_formulir_id);
            $approver = User::find($data_approver->approval_to);
            $data = [
                'list_data' => $list_data,
                'token' => $token,
                'username' => 'this email by System',
                'url' => '//' . env('SERVER_DOMAIN'),
                'approver' => $approver,
                'array_formulir_id' => $array_formulir_id
                ];

            \Mail::send($class::bladeEmail(), $data, function ($message) use ($approver) {
                $message->to($approver->email)->subject('request approval #' . date('ymdHi'));
            });
            
            foreach ($list_data as $data) {
                formulir_update_token($data->formulir, $token);
            }
        }
    }
}
