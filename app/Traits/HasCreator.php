<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait HasCreator
{
    public static function bootHasCreator()
    {
        static::creating(function (Model $model) {
            if (! $model->user_id) {
                $model->user_id = auth()->id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name', 'email');
    }
}
