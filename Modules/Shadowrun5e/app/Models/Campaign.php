<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use App\Models\Campaign as BaseCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shadowrun5e\Database\Factories\CampaignFactory;

class Campaign extends BaseCampaign
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function contacts(): ContactArray
    {
        $contacts = new ContactArray();
        /** @var Character $character */
        foreach ($this->characters() as $character) {
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

    protected static function newFactory(): Factory
    {
        return CampaignFactory::new();
    }
}
