<?php

namespace App\Observers;

use App\Avatar;

class AvatarObserver
{
    /**
     * Handle the avatar "created" event.
     *
     * @param  \App\Avatar  $avatar
     * @return void
     */
    public function created(Avatar $avatar)
    {
        //
    }

    /**
     * Handle the avatar "updated" event.
     *
     * @param  \App\Avatar  $avatar
     * @return void
     */
    public function updated(Avatar $avatar)
    {
        //
    }

    /**
     * Handle the avatar "deleted" event.
     *
     * @param  \App\Avatar  $avatar
     * @return void
     */
    public function deleted(Avatar $avatar)
    {
        //
    }

    /**
     * Handle the avatar "deleting" event.
     *
     * @param  \App\Avatar  $avatar
     * @return void
     */
    public function deleting(Avatar $avatar)
    {
        $path = storage_path('app/'.$avatar->media_path);
        @unlink($path);
    }

    /**
     * Handle the avatar "restored" event.
     *
     * @param  \App\Avatar  $avatar
     * @return void
     */
    public function restored(Avatar $avatar)
    {
        //
    }

    /**
     * Handle the avatar "force deleted" event.
     *
     * @param  \App\Avatar  $avatar
     * @return void
     */
    public function forceDeleted(Avatar $avatar)
    {
        //
    }
}
