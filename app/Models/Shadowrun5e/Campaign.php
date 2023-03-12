<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use App\Models\Campaign as BaseCampaign;

class Campaign extends BaseCampaign
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function contacts(): ContactArray
    {
        $contacts = new ContactArray();
        foreach ($this->characters() as $character) {
            /** @var Contact $contact */
            foreach ($character->getContacts() as $contact) {
                if (!isset($contacts[(string)$contact])) {
                    $contacts[(string)$contact] = $contact;
                }
                // @phpstan-ignore-next-line
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
