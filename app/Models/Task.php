<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'expiration_date',
        'tag_id',
        'status_id',
        'active'
    ];

    protected $hidden = ['created_at', 'updated_at'];
    protected $appends = ['priority_name', 'status_name'];

    protected $casts = [
        'expiration_date' => 'datetime',
        'active' => 'boolean'
    ];
    public function users(){
        return $this->belongsToMany(User::class, 'task_user');
    }
    public function tag(){
        return $this->belongsTo(TagType::class, 'tag_id');
    }
    public function status(){
        return $this->belongsTo(StatusType::class, 'status_id');
    }
    public function getPriorityNameAttribute()
    {
        return optional($this->tag)->name ?? null;
    }
    public function getStatusNameAttribute()
    {
        return optional($this->status)->name ?? null;
    }
}
