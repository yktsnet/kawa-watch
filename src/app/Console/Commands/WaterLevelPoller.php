<?php

namespace App\Console\Commands;

use App\Models\Station;
use App\Services\RiverApiService;
use App\Services\SqsQueueService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WaterLevelPoller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:poll-water-level';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll real water level data and send to SQS';

    protected SqsQueueService $sqsService;

    protected RiverApiService $riverApiService;

    public function __construct(SqsQueueService $sqsService, RiverApiService $riverApiService)
    {
        parent::__construct();
        $this->sqsService = $sqsService;
        $this->riverApiService = $riverApiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting real water level polling...');

        // Fetch valid station codes from the database
        $stations = Station::all();

        if ($stations->isEmpty()) {
            $this->warn('No stations found.');

            return;
        }

        $queueUrl = config('services.sqs.water_level_queue', env('AWS_SQS_WATER_LEVEL_QUEUE_URL', ''));

        if (empty($queueUrl)) {
            $this->error('Water level SQS queue URL is not configured.');

            return;
        }

        foreach ($stations as $station) {
            $this->info("Fetching water level data for station {$station->code} via API...");

            $apiData = $this->riverApiService->getLatestWaterLevel($station->code);

            if ($apiData) {
                $eventData = [
                    'station_code' => $station->code,
                    'observed_at' => $apiData['observed_at'],
                    'level_m' => $apiData['level_m'],
                ];

                $success = $this->sqsService->sendMessage($queueUrl, $eventData);

                if ($success) {
                    Log::info("Successfully polled and sent real water level data for {$station->code}");
                } else {
                    Log::error("Failed to send water level data for {$station->code} to SQS");
                }
            } else {
                $this->warn("No water level data returned for {$station->code}");
                Log::warning("No water level data returned from RiverApiService for station {$station->code}");
            }
        }

        $this->info('Finished water level polling.');
    }
}
