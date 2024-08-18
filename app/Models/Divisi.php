<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'divisis';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'divisi_id');
    }
}
