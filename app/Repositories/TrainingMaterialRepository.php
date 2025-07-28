<?php

namespace App\Repositories;

use App\Models\TrainingMaterial;

class TrainingMaterialRepository
{
  public function all()
  {
    return TrainingMaterial::all();
  }

  public function find($id)
  {
    return TrainingMaterial::find($id);
  }

  public function create(array $data)
  {
    return TrainingMaterial::create($data);
  }

  public function update(TrainingMaterial $trainingMaterial, array $data)
  {
    return $trainingMaterial->update($data);
  }

  public function delete(TrainingMaterial $trainingMaterial)
  {
    return $trainingMaterial->delete();
  }
}
