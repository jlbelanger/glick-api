<?php

namespace App\Observers;

use App\Models\Action;

class ActionObserver
{
	public function creating(Action $record)
	{
		$actionId = $record->actionType->inProgress;
		if (!$actionId) {
			return;
		}

		$action = Action::find($actionId);
		$action->end_date = $record->start_date;
		$action->save();
	}
}
