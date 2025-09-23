<?php

declare(strict_types=1);

namespace Modules\Battletech\Enums;

enum EquipmentAffiliation: string
{
    case CapellanConfederation = 'CC';
    case CircinusFederation = 'CF';
    case Clan = 'Clan';
    case ComStar = 'CS';
    case DeepPeripheryState = 'DP';
    case DraconisCombine = 'DC';
    case FederatedSuns = 'FS';
    case FreeRasalhagueRepublic = 'FR';
    case FreeWorldsLeague = 'FW';
    case General = '';
    case HomeworldClan = 'HC';
    case IndependentWorld = 'IN';
    case InvadingClan = 'IC';
    case LyranAlliance = 'LA';
    case MagistracyOfCanopus = 'MC';
    case MarianHegemony = 'MH';
    case MinorPeripheryState = 'MP';
    case OutworldsAlliance = 'OA';
    case Periphery = 'Per';
    case TaurianConcodat = 'TC';
    case Terran = 'TR';
}
