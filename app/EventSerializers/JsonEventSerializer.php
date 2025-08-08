<?php

declare(strict_types=1);

namespace App\EventSerializers;

use JsonException;
use Modules\Dnd5e\Enums\CoinType;
use Modules\Dnd5e\Events\MoneyConverted;
use Modules\Dnd5e\Events\MoneyGained;
use Modules\Dnd5e\Events\MoneySpent;
use Modules\Dnd5e\Models\Character;
use Override;
use Spatie\EventSourcing\EventSerializers\JsonEventSerializer as BaseJsonEventSerializer;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class JsonEventSerializer extends BaseJsonEventSerializer
{
    /**
     * @throws JsonException
     */
    #[Override]
    public function deserialize(
        string $event_class,
        string $json,
        int $version,
        ?string $metadata = null,
    ): ShouldBeStored {
        if (MoneyGained::class === $event_class) {
            $data = json_decode(json: $json, flags: JSON_THROW_ON_ERROR);
            /** @var Character */
            $character = Character::findOrFail($data->character->id);
            return new MoneyGained(
                $character,
                CoinType::from($data->currency->value),
                $data->amount,
            );
        }

        if (MoneySpent::class === $event_class) {
            $data = json_decode(json: $json, flags: JSON_THROW_ON_ERROR);
            /** @var Character */
            $character = Character::findOrFail($data->character->id);
            return new MoneySpent(
                $character,
                CoinType::from($data->currency->value),
                $data->amount,
            );
        }

        if (MoneyConverted::class === $event_class) {
            $data = json_decode(json: $json, flags: JSON_THROW_ON_ERROR);
            /** @var Character */
            $character = Character::findOrFail($data->character->id);
            return new MoneyConverted(
                $character,
                CoinType::from($data->from_currency->value),
                $data->from_amount,
                CoinType::from($data->to_currency->value),
                $data->to_amount,
            );
        }

        return parent::deserialize($event_class, $json, $version, $metadata);
    }
}
