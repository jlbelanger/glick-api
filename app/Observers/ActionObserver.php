<?php

namespace App\Observers;

use App\Models\Action;

class ActionObserver
{
	/**
	 * @param  Action $record
	 * @return void
	 */
	public function creating(Action $action)
	{
		if (!$action->actionType) {
			return;
		}

		$inProgressAction = $action->actionType->inProgress;
		if (!$inProgressAction) {
			return;
		}

		$inProgressAction = Action::find($inProgressAction['id']);
		$inProgressAction->end_date = $action->start_date;
		$inProgressAction->save();
	}
}
