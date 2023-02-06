<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use App\Models\Campaign as BaseCampaign;

class Campaign extends BaseCampaign
{
    public function contacts(): ContactArray
    {
        $contacts = new ContactArray();
        foreach ($this->characters() as $character) {
            foreach ($character->getContacts() as $contact) {
                if (!isset($contacts[(string)$contact])) {
                    $contact->characters = [];
                    $contacts[(string)$contact] = $contact;
                }
                $contacts[(string)$contact]->characters[] = [
                    'character' => (string)$character,
                    'loyalty' => $contact->loyalty,
                    'connection' => $contact->connection,
                    'notes' => $contact->notes,
                ];
            }
        }
        return $contacts;
    }
}
