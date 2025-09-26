<?php

namespace App\Observers;

use App\Models\CrmCard;
use App\Events\CardStageChanged;

class CrmCardObserver
{
    public function updating(CrmCard $card)
    {
        if ($card->isDirty('stage_id')) {
            event(new CardStageChanged($card->getOriginal('stage_id'), $card->stage_id, $card->id));
        }
    }

    public function created(CrmCard $card)
    {
        // on enter do estágio inicial
        event(new CardStageChanged(null, $card->stage_id, $card->id));
    }
}
