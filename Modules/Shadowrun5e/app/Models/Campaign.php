<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use App\Models\Campaign as BaseCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shadowrun5e\Database\Factories\CampaignFactory;
use Override;

use function assert;

final class Campaign extends BaseCampaign
{
    public function contacts(): ContactArray
    {
        $contacts = new ContactArray();
        /** @var Character $character */
        foreach ($this->characters() as $character) {
            foreach ($character->getContacts() as $contact) {
                if (!isset($contacts[(string)$contact])) {
                    $contacts[(string)$contact] = $contact;
                }
                assert($contacts[(string)$contact] instanceof Contact);
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

    #[Override]
    protected static function newFactory(): Factory
    {
        return CampaignFactory::new();
    }
}
