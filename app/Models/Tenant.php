<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $connection = 'cluster'; 
    protected $table = 'tenants';
    protected $fillable = ['name', 'domain', 'database'];
    protected $guarded = ['id'];
    public $timestamps = true;
}