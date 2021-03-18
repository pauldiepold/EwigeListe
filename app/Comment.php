<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model {

    use SoftDeletes, HasFactory;

    protected $dates = ['deleted_at'];

    protected $fillable = ['created_by', 'parent_id', 'body'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Player', 'created_by');
    }
}
