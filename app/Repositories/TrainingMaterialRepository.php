<?php

namespace App\Repositories;

use App\Models\TrainingMaterial;
use Illuminate\Database\Eloquent\Builder;

class TrainingMaterialRepository
{
    private function baseQuery(): Builder
    {
        return TrainingMaterial::with(['category', 'language']);
    }

    private function executeQuery(Builder $query, $perPage = null)
    {
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getAll($perPage = null)
    {
        return $this->executeQuery($this->baseQuery(), $perPage);
    }

    public function getAllPdf($perPage = null)
    {
        $query = $this->baseQuery()->where('file_type', 'document');
        return $this->executeQuery($query, $perPage);
    }

    public function getAllVideos($perPage = null)
    {
        $query = $this->baseQuery()->where('file_type', 'video');
        return $this->executeQuery($query, $perPage);
    }

    public function getAllImage($perPage = null)
    {
        $query = $this->baseQuery()->where('file_type', 'image');
        return $this->executeQuery($query, $perPage);
    }

    public function getAllAudio($perPage = null)
    {
        $query = $this->baseQuery()->where('file_type', 'audio');
        return $this->executeQuery($query, $perPage);
    }

    public function getAllEnglish($perPage = null)
    {
        $query = $this->baseQuery()->whereHas('language', function ($q) {
            $q->where('name', 'english');
        });
        return $this->executeQuery($query, $perPage);
    }

    public function getAllTagalog($perPage = null)
    {
        $query = $this->baseQuery()->whereHas('language', function ($q) {
            $q->where('name', 'tagalog');
        });
        return $this->executeQuery($query, $perPage);
    }

    public function getVideosByPopularity($perPage = null)
    {
        $query = $this->baseQuery()->orderBy('views', 'desc');
        return $this->executeQuery($query, $perPage);
    }

    public function getVideosByDateUploaded($perPage = null)
    {
        $query = $this->baseQuery()->orderBy('created_at', 'desc');
        return $this->executeQuery($query, $perPage);
    }

    public function find($id)
    {
        return $this->baseQuery()->where("id", $id)->first();
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
