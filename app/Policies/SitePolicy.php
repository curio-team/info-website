<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SitePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the site can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the site can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Site  $model
     * @return mixed
     */
    public function view(?User $user, Site $model)
    {
        return true;
    }

    /**
     * Determine whether the site can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the site can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Site  $model
     * @return mixed
     */
    public function update(User $user, Site $model)
    {
        return true;
    }

    /**
     * Determine whether the site can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Site  $model
     * @return mixed
     */
    public function delete(User $user, Site $model)
    {
        return true;
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Site  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the site can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Site  $model
     * @return mixed
     */
    public function restore(User $user, Site $model)
    {
        return false;
    }

    /**
     * Determine whether the site can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Site  $model
     * @return mixed
     */
    public function forceDelete(User $user, Site $model)
    {
        return false;
    }
}
