<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\TrainingMaterialRepository;
use Aws\S3\S3Client;

class DeleteTrainingMaterial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tm:delete {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a specific training material by ID in the database and object storage';

    private TrainingMaterialRepository $trainingMaterialRepository;
    public function __construct(TrainingMaterialRepository $trainingMaterialRepository)
    {
        parent::__construct();
        $this->trainingMaterialRepository = $trainingMaterialRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');

        $traininigMaterial = $this->trainingMaterialRepository->find($id);

        if (!$traininigMaterial) {
            $this->error("Training material with ID {$id} not found.");
            return;
        }

        // Delete from Cloudflare R2 Object Storage
        $key = $traininigMaterial->path;
        $s3 = new S3Client([
            'region' => 'auto',
            'version' => 'latest',
            'endpoint' => config('services.r2.endpoint'),
            'credentials' => [
                'key'    => config('services.r2.key'),
                'secret' => config('services.r2.secret'),
            ],
            'bucket_endpoint' => false,
            'use_path_style_endpoint' => true,
        ]);


        $s3->deleteObject([
            'Bucket' => config('services.r2.bucket'),
            'Key' => $key
        ]);

        // Delete from Database
        $this->trainingMaterialRepository->delete($traininigMaterial);

        $this->info("Training material with ID {$id} deleted.");
    }
}
