<?php

namespace App\Repositories;

use App\Models\TrainingMaterial;

class TrainingMaterialRepository
{
  public function getAll()
  {
    return TrainingMaterial::all();
  }

  public function find($id)
  {
    return TrainingMaterial::find($id);
  }

  public function upload(array $data)
  {
    return TrainingMaterial::create($data);
  }

  public function update(array $data)
  {
    return TrainingMaterial::update($data);
  }

  public function delete(TrainingMaterial $trainingMaterial)
  {
    return $trainingMaterial->delete();
  }
}
