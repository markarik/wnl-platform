<?php

namespace App\Models\Concerns;


use App\Models\Permission;

trait Restrictable
{
	/**
	 * @param \App\Models\Permission|string $permission
	 *
	 * @return bool
	 */
	public function requires($permission)
	{
		if (!$permission instanceof Permission) {
			$permissionModel = Permission::slug($permission);
		} else {
			$permissionModel = $permission;
		}

		return $this->permissions->contains($permissionModel);
	}
}
