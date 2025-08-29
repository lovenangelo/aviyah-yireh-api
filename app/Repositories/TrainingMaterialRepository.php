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

    private function executeQuery(Builder $query, $perPage = null, $published = null)
    {
        if (!is_null($published)) {
            $query->where('is_visible', $published);
        }

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getAll($perPage = null, $published = null)
    {
        return $this->executeQuery($this->baseQuery(), $perPage, $published);
    }

    public function getAllEnglish($perPage = null, $published = null)
    {
        $query = $this->baseQuery()->whereHas('language', function ($q) {
            $q->where('name', 'English');
        });
        return $this->executeQuery($query, $perPage, $published);
    }

    public function getAllTagalog($perPage = null, $published = null)
    {
        $query = $this->baseQuery()->whereHas('language', function ($q) {
            $q->where('name', 'Tagalog');
        });
        return $this->executeQuery($query, $perPage, $published);
    }

    public function getVideosByPopularity($perPage = null, $published = null)
    {
        $query = $this->baseQuery()->orderBy('views', 'desc');
        return $this->executeQuery($query, $perPage, $published);
    }

    public function getVideosByDateUploaded($perPage = null, $published = null)
    {
        $query = $this->baseQuery()->orderBy('created_at', 'desc');
        return $this->executeQuery($query, $perPage, $published);
    }

    public function find($id)
    {
        return $this->baseQuery()->where("id", $id)->first();
    }

    public function create(array $data)
    {
        return TrainingMaterial::create($data);
    }

    public function update(array $data, $id)
    {
        unset($data["path"]);
        $tm = TrainingMaterial::find($id);
        $tm->update($data);
        return $tm;
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


    /**
     * Get filtered training materials using the scope filter
     *
     * @param array $filters
     * @param int|null $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getFiltered(array $filters, $perPage = null)
    {
        $query = TrainingMaterial::with(['category', 'language', 'user'])
            ->filter($filters);

        if ($perPage) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }
}
