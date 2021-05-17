<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Log;

class CreateBulkUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $filePath;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filePath = storage_path('app\/'.$this->filePath);
        $fileData = array_map('str_getcsv',file($filePath));
        foreach ($fileData as $key => $data)
        {
            if($key == 0)
            {
                $headers = $data;
            }
            else
            {
                $user = User::create([
                    'name' => $data[0],
                    'email' => $data[1],
                    'password' => Hash::make($data[2]),
                ]);
            }

        }
    }
}
