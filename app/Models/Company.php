<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the users for the company.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the item categories for the company.
     */
    public function item_categories()
    {
        return $this->hasMany(ItemCategory::class);
    }

    /**
     * Get the items for the company.
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get the service categories for the company
     */
    public function service_categories()
    {
        return $this->hasMay(ServiceCategory::class);
    }

    /**
     * Get the company vehicles for the company
     */
    public function company_vehicles()
    {
        return $this->hasMany(CompanyVehicle::class);
    }

    /**
     * Get the job types for the company
     */
    public function job_types()
    {
        return $this->hasMany(JobType::class);
    }

    /**
     * Get the branches for the company
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
