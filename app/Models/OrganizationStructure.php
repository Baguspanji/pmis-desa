<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationStructure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'position',
        'organization_type',
        'level',
        'parent_id',
        'order',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the parent organization structure.
     */
    public function parent()
    {
        return $this->belongsTo(OrganizationStructure::class, 'parent_id');
    }

    /**
     * Get the children organization structures.
     */
    public function children()
    {
        return $this->hasMany(OrganizationStructure::class, 'parent_id')->orderBy('order');
    }

    /**
     * Scope to get government structure (Pemerintah Desa).
     */
    public function scopeGovernment($query)
    {
        return $query->where('organization_type', 'Pemerintah Desa');
    }

    /**
     * Scope to get consultative body structure (Badan Permasyarakatan Desa).
     */
    public function scopeConsultativeBody($query)
    {
        return $query->where('organization_type', 'Badan Permasyarakatan Desa');
    }

    /**
     * Get root level heads only.
     */
    public function scopeHeads($query)
    {
        return $query->where('level', 'head');
    }

    /**
     * Get all ancestors.
     */
    public function getAncestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors->reverse();
    }
}
