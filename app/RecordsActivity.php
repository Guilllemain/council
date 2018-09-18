<?php

namespace App;

trait RecordsActivity
{
    protected static function bootRecordsActivity()
    {
        if (auth()->guest()) {
            return;
        }

        static::created(function ($event) {
            $event->recordActivity('created');
        });

        static::deleting(function ($model) {
            $model->activity()->delete();
        });
    }

    protected function recordActivity($event)
    {
        $this->activity()->create([
            'type' => $event.'_'.strtolower((new \ReflectionClass($this))->getShortName()),
            'user_id' => auth()->id()
        ]);

        // Activity::create([
        //     'type' => $event . '_' . strtolower((new \ReflectionClass($this))->getShortName()),
        //     'user_id' => auth()->id(),
        //     'subject_id' => $this->id,
        //     'subject_type' => get_class($this)
        // ]);
    }

    public function activity()
    {
        return $this->morphMany('App\Activity', 'subject');
    }
}
