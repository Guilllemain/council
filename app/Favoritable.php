<?php

namespace App;

trait Favoritable
{
    protected static function bootFavoritable()
    {
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    public function favorite()
    {
        if (! $this->favorites()->where('user_id', auth()->id())->exists()) {
            return $this->favorites()->create(['user_id' => auth()->id()]);
        }
        // because we use polymorphism, we don't need to specify favorited_id and type
    }

    public function unfavorite()
    {
        $this->favorites()->where('user_id', auth()->id())->get()->each(function ($item) {
            $item->delete();
        });
    }

    public function isFavorited()
    {
        return (bool) $this->favorites->where('user_id', auth()->id())->count();
    }

    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    public function getFavoritesCountAttribute()
    {
        return count($this->favorites);
    }
}
