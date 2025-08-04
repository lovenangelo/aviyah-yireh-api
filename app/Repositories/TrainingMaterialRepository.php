<?php

namespace App\Repositories;

use App\Models\TrainingMaterial;

class TrainingMaterialRepository
{
    public function getAll()
    {
        return TrainingMaterial::with(['category', 'language',])->get();
    }

    public function getAllPdf()
    {
        return TrainingMaterial::with(['category', 'language',])
            ->where('file_type', 'document')
            ->get();
    }
    public function getAllVideos()
    {
        return TrainingMaterial::with(['category', 'language',])
            ->where('file_type', 'video')
            ->get();
    }

    public function getAllImage()
    {
        return TrainingMaterial::with(['category', 'language',])
            ->where('file_type', 'image')
            ->get();
    }

    public function getAllAudio()
    {
        return TrainingMaterial::with(['category', 'language',])
            ->where('file_type', 'audio')
            ->get();
    }

    public function getAllEnglish()
    {
        return TrainingMaterial::with(['category', 'language',])
            ->whereHas('language', function ($q) {
                $q->where('name', 'english');
            })
            ->get();
    }

    public function getAllTagalog()
    {
        return TrainingMaterial::with(['category', 'language',])
            ->whereHas('language', function ($q) {
                $q->where('name', 'tagalog');
            })
            ->get();
    }

    public function getVideosByPopularity()
    {
        return TrainingMaterial::with(['category', 'language',])
            ->orderBy('views', 'desc')
            ->get();
    }

    public function getVideosByDateUploaded()
    {
        return TrainingMaterial::with(['category', 'language',])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find($id)
    {
        return TrainingMaterial::with(['category', 'language',])->where("id", $id)->first();
    }

    public function upload(array $data)
    {
        return TrainingMaterial::create($data);
    }

    public function update(array $data, $id)
    {
        TrainingMaterial::find($id)->update($data);
    }

    public function delete(TrainingMaterial $trainingMaterial)
    {
        return $trainingMaterial->delete();
    }


    public function bulkDestroy(array $ids): array
    {
        $result = [
            'deleted' => 0,
            'failed' => 0,
            'attempted' => count($ids),
            'has_users' => []
        ];

        foreach ($ids as $id) {
            $trainingMaterial = $this->find($id);

            if ($trainingMaterial) {
                try {
                    $this->delete($trainingMaterial);
                    $result['deleted']++;
                } catch (\Exception $e) {
                    $result['failed']++;
                }
            }
        }
        return $result;
    }
}
