<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class CardStageChanged
{
    use SerializesModels;
    public function __construct(public ?int $fromStageId, public int $toStageId, public int $cardId) {}
}