<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use App\Events\Shadowrun5e\DamageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shadowrun5e\AttributesRequest;
use App\Http\Requests\Shadowrun5e\BackgroundRequest;
use App\Http\Requests\Shadowrun5e\KnowledgeSkillsRequest;
use App\Http\Requests\Shadowrun5e\MartialArtsRequest;
use App\Http\Requests\Shadowrun5e\QualitiesRequest;
use App\Http\Requests\Shadowrun5e\RulesRequest;
use App\Http\Requests\Shadowrun5e\SkillsRequest;
use App\Http\Requests\Shadowrun5e\SocialRequest;
use App\Http\Requests\Shadowrun5e\StandardPriorityRequest;
use App\Http\Requests\Shadowrun5e\VitalsRequest;
use App\Http\Resources\Shadowrun5e\CharacterResource;
use App\Models\Shadowrun5e\ActiveSkill;
use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\Models\Shadowrun5e\Quality;
use App\Models\Shadowrun5e\Rulebook;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use Rs\Json\Patch;
use Rs\Json\Patch\InvalidOperationException;
use Rs\Json\Patch\InvalidPatchDocumentJsonException;
use Rs\Json\Pointer\InvalidPointerException;
use RuntimeException;

/**
 * Controller for interacting with Shadowrun 5E characters.
 */
class CharactersController extends Controller
{
    /**
     * Return the next step for the character.
     * @param string $step Current step
     * @param PartialCharacter $character
     * @return ?string
     */
    protected function nextStep(
        string $step,
        PartialCharacter $character
    ): ?string {
        switch ($step) {
            case 'armor':
                return 'gear';
            case 'attributes':
                return 'qualities';
            case 'augmentations':
                return 'weapons';
            case 'background':
                return 'review';
            case 'gear':
                return 'vehicles';
            case 'knowledge':
                if ($character->isMagicallyActive()) {
                    return 'magic';
                }
                if ($character->isTechnomancer()) {
                    return 'resonance';
                }
                return 'augmentations';
            case 'magic':
                return 'augmentations';
            case 'martial-arts':
                return 'skills';
            case 'priorities':
                return 'vitals';
            case 'qualities':
                $selectedBooks = [];
                if (isset($character->priorities, $character->priorities['rulebooks'])) {
                    $selectedBooks = explode(
                        ',',
                        (string)$character->priorities['rulebooks']
                    );
                }
                if (!in_array('run-and-gun', $selectedBooks, true)) {
                    return 'skills';
                }
                return 'martial-arts';
            case 'resonance':
                return 'augmentations';
            case 'review':
                return null;
            case 'rules':
                return 'priorities';
            case 'skills':
                return 'knowledge';
            case 'social':
                return 'background';
            case 'vehicles':
                return 'social';
            case 'vitals':
                return 'attributes';
            case 'weapons':
                return 'armor';
            default:
                return 'rules'; // @codeCoverageIgnore
        }
    }

    /**
     * Based on the current step, return the previous step.
     * @param string $step
     * @param PartialCharacter $character
     * @return ?string
     */
    protected function previousStep(
        string $step,
        PartialCharacter $character
    ): ?string {
        switch ($step) {
            case 'armor':
                return 'weapons';
            case 'attributes':
                return 'vitals';
            case 'augmentations':
                if ($character->isMagicallyActive()) {
                    return 'magic';
                }
                if ($character->isTechnomancer()) {
                    return 'resonance';
                }
                return 'knowledge';
            case 'background':
                return 'social';
            case 'gear':
                return 'armor';
            case 'knowledge':
                return 'skills';
            case 'martial-arts':
                return 'qualities';
            case 'magic':
                return 'knowledge';
            case 'priorities':
                return 'rules';
            case 'qualities':
                return 'attributes';
            case 'resonance':
                return 'knowledge';
            case 'rules':
                return null;
            case 'review':
                return 'background';
            case 'skills':
                $selectedBooks = [];
                if (isset($character->priorities, $character->priorities['rulebooks'])) {
                    $selectedBooks = explode(
                        ',',
                        (string)$character->priorities['rulebooks']
                    );
                }
                if (!in_array('run-and-gun', $selectedBooks, true)) {
                    return 'qualities';
                }
                return 'martial-arts';
            case 'social':
                return 'vehicles';
            case 'vehicles':
                return 'gear';
            case 'vitals':
                return 'priorities';
            case 'weapons':
                return 'augmentations';
            default:
                return 'rules'; // @codeCoverageIgnore
        }
    }

    /**
     * Redirect the user where they need to go based on which save button they
     * clicked.
     * @param string $direction Next or prev
     * @param string $step Current step
     * @param PartialCharacter $character
     * @return RedirectResponse
     */
    protected function redirect(
        string $direction,
        string $step,
        PartialCharacter $character
    ): RedirectResponse {
        if ('prev' === $direction) {
            return new RedirectResponse(\sprintf(
                '/characters/shadowrun5e/create/%s',
                $this->previousStep($step, $character),
            ));
        }

        return new RedirectResponse(\sprintf(
            '/characters/shadowrun5e/create/%s',
            $this->nextStep($step, $character),
        ));
    }

    /**
     * Show the selected step of character creation, or the first step if none
     * is explicitly chosen.
     * @psalm-suppress InvalidArgument
     * @psalm-suppress NoValue
     */
    public function create(
        Request $request,
        ?string $step = null
    ): RedirectResponse | Redirector | View {
        /** @var User */
        $user = Auth::user();

        if ('new' === $step) {
            $character = PartialCharacter::create(['owner' => $user->email]);
            $request->session()->put('shadowrun5epartial', $character->id);
            $step = 'rules';
        } else {
            $character = $this->findPartialCharacter($request, $step);
            if (null !== $character && $step === $character->id) {
                return redirect('/characters/shadowrun5e/create/rules');
            }
            if (null === $character) {
                // No current character, see if they already have a character they
                // might want to continue.
                $characters = PartialCharacter::where('owner', $user->email)->get();

                if (0 !== count($characters)) {
                    return view(
                        'Shadowrun5e.choose-character',
                        [
                            'characters' => $characters,
                            'user' => $user,
                        ],
                    );
                }

                // No in-progress characters, create a new one.
                $character = PartialCharacter::create(['owner' => $user->email]);
                $request->session()->put('shadowrun5epartial', $character->id);
            }

            if (null === $step || $step === $character->id) {
                $step = 'rules';
            }
        }
        $books = collect(Rulebook::all());
        $selectedBooks = false;
        if (isset($character->priorities, $character->priorities['rulebooks'])) {
            $selectedBooks = explode(',', (string)$character->priorities['rulebooks']);
            $books = $books->filter(
                function (Rulebook $_value, string $key) use ($selectedBooks): bool {
                    return in_array($key, $selectedBooks, true);
                }
            );
        }
        switch ($step) {
            case 'armor':
                return view(
                    'Shadowrun5e.create-armor',
                    [
                        'books' => $books,
                        'character' => $character,
                        'currentStep' => 'armor',
                        'nextStep' => $this->nextStep('armor', $character),
                        'previousStep' => $this->previousStep('armor', $character),
                    ]
                );
            case 'attributes':
                $selected = [
                    'agility' => $request->old('agility') ?? $character->agility ?? null,
                    'body' => $request->old('body') ?? $character->body ?? null,
                    'charisma' => $request->old('charisma') ?? $character->charisma ?? null,
                    'edge' => $request->old('edge') ?? $character->edge ?? null,
                    'intuition' => $request->old('intuition') ?? $character->intuition ?? null,
                    'logic' => $request->old('logic') ?? $character->logic ?? null,
                    'magic' => $request->old('magic') ?? $character->magic ?? null,
                    'reaction' => $request->old('reaction') ?? $character->reaction ?? null,
                    'resonance' => $request->old('resonance') ?? $character->resonance ?? null,
                    'strength' => $request->old('strength') ?? $character->strength ?? null,
                    'willpower' => $request->old('willpower') ?? $character->willpower ?? null,
                ];
                $max = [
                    'agility' => $character->getStartingMaximumAttribute('agility'),
                    'body' => $character->getStartingMaximumAttribute('body'),
                    'charisma' => $character->getStartingMaximumAttribute('charisma'),
                    'edge' => $character->getStartingMaximumAttribute('edge'),
                    'intuition' => $character->getStartingMaximumAttribute('intuition'),
                    'logic' => $character->getStartingMaximumAttribute('logic'),
                    'reaction' => $character->getStartingMaximumAttribute('reaction'),
                    'strength' => $character->getStartingMaximumAttribute('strength'),
                    'willpower' => $character->getStartingMaximumAttribute('willpower'),
                ];
                return view(
                    'Shadowrun5e.create-attributes',
                    [
                        'character' => $character,
                        'currentStep' => 'attributes',
                        'max' => $max,
                        'nextStep' => $this->nextStep('attributes', $character),
                        'previousStep' => $this->previousStep('attributes', $character),
                        'selected' => $selected,
                    ]
                );
            case 'augmentations':
                return view(
                    'Shadowrun5e.create-augmentations',
                    [
                        'books' => $books,
                        'character' => $character,
                        'currentStep' => 'augmentations',
                        'nextStep' => $this->nextStep('augmentations', $character),
                        'previousStep' => $this->previousStep('augmentations', $character),
                    ]
                );
            case 'background':
                $background = [
                    'age' => $request->old('age')
                        ?? $character->background['age'] ?? null,
                    'appearance' => $request->old('appearance')
                        ?? $character->background['appearance'] ?? null,
                    'born' => $request->old('born')
                        ?? $character->background['born'] ?? null,
                    'description' => $request->old('description')
                        ?? $character->background['description'] ?? null,
                    'education' => $request->old('education')
                        ?? $character->background['education'] ?? null,
                    'family' => $request->old('family')
                        ?? $character->background['family'] ?? null,
                    'gender-identity' => $request->old('gender-identity')
                        ?? $character->background['gender-identity'] ?? null,
                    'goals' => $request->old('goals')
                        ?? $character->background['goals'] ?? null,
                    'hate' => $request->old('hate')
                        ?? $character->background['hate'] ?? null,
                    'limitations' => $request->old('limitations')
                        ?? $character->background['limitations'] ?? null,
                    'living' => $request->old('living')
                        ?? $character->background['living'] ?? null,
                    'love' => $request->old('love')
                        ?? $character->background['love'] ?? null,
                    'married' => $request->old('married')
                        ?? $character->background['married'] ?? null,
                    'moral' => $request->old('moral')
                        ?? $character->background['moral'] ?? null,
                    'motivation' => $request->old('motivation')
                        ?? $character->background['motivation'] ?? null,
                    'name' => $request->old('name')
                        ?? $character->background['name'] ?? null,
                    'personality' => $request->old('personality')
                        ?? $character->background['personality'] ?? null,
                    'qualities' => $request->old('qualities')
                        ?? $character->background['qualities'] ?? null,
                    'religion' => $request->old('religion')
                        ?? $character->background['religion'] ?? null,
                    'size' => $request->old('size')
                        ?? $character->background['size'] ?? null,
                    'why' => $request->old('why')
                        ?? $character->background['why'] ?? null,
                ];
                return view(
                    'Shadowrun5e.create-background',
                    [
                        'background' => $background,
                        'character' => $character,
                        'currentStep' => 'background',
                        'nextStep' => $this->nextStep('background', $character),
                        'previousStep' => $this->previousStep('background', $character),
                    ]
                );
            case 'gear':
                return view(
                    'Shadowrun5e.create-gear',
                    [
                        'books' => $books,
                        'character' => $character,
                        'currentStep' => 'gear',
                        'nextStep' => $this->nextStep('gear', $character),
                        'previousStep' => $this->previousStep('gear', $character),
                    ]
                );
            case 'knowledge':
                return view(
                    'Shadowrun5e.create-knowledge',
                    [
                        'character' => $character,
                        'currentStep' => 'knowledge',
                        'nextStep' => $this->nextStep('knowledge', $character),
                        'previousStep' => $this->previousStep('knowledge', $character),
                    ]
                );
            case 'magic':
                if (!$character->isMagicallyActive()) {
                    return redirect('/characters/shadowrun5e/create/priority')
                        ->withErrors([
                            'error' => 'Only awakened characters can choose spells, powers, and spirits.',
                        ]);
                }
                return view(
                    'Shadowrun5e.create-magic',
                    [
                        'books' => $books,
                        'character' => $character,
                        'currentStep' => 'magic',
                        'nextStep' => $this->nextStep('magic', $character),
                        'previousStep' => $this->previousStep('magic', $character),
                    ]
                );
            case 'martial-arts':
                if (false === $selectedBooks || !in_array('run-and-gun', $selectedBooks, true)) {
                    return redirect('/characters/shadowrun5e/create/rules')
                        ->withErrors([
                            'error' => 'Martial arts are only available with Run and Gun enabled',
                        ]);
                }
                return view(
                    'Shadowrun5e.create-martial-arts',
                    [
                        'books' => $books,
                        'character' => $character,
                        'currentStep' => 'martial-arts',
                        'nextStep' => $this->nextStep('martial-arts', $character),
                        'previousStep' => $this->previousStep('martial-arts', $character),
                        'styles' => $character->getMartialArtsStyles(),
                        'techniques' => $character->getMartialArtsTechniques(),
                    ]
                );
            case 'priorities':
                if (
                    !isset(
                        $character->priorities,
                        $character->priorities['system'],
                        $character->priorities['gameplay'],
                    )
                ) {
                    return redirect('/characters/shadowrun5e/create/rules')
                        ->withErrors([
                            'error' => 'You must choose a creation system and gameplay level',
                        ]);
                }

                if ('priority' !== $character->priorities['system']) {
                    return redirect('/characters/shadowrun5e/create/rules')
                        ->withErrors([
                            'error' => \sprintf(
                                'Priority scheme "%s" not (yet) supported',
                                $character->priorities['system']
                            ),
                        ]);
                }
                $commonOptions = [
                    'a' => [
                        [
                            'name' => 'Metatype',
                            'title' => 'All races available, highest special points',
                            'value' => 'metatype',
                        ],
                        [
                            'name' => 'Attributes (24)',
                            'title' => '24 attribute points to spend',
                            'value' => 'attributes',
                        ],
                        [
                            'name' => 'Magic or resonance',
                            'title' => 'All types of magicians and technomancers available',
                            'value' => 'magic',
                        ],
                        [
                            'name' => 'Skills (46/10)',
                            'title' => '46 points for individual skills and 10 points for skill groups',
                            'value' => 'skills',
                        ],
                    ],
                    'b' => [
                        [
                            'name' => 'Metatype',
                            'title' => 'All races available, high special points',
                            'value' => 'metatype',
                        ],
                        [
                            'name' => 'Attributes (20)',
                            'title' => '20 attribute points to spend',
                            'value' => 'attributes',
                        ],
                        [
                            'name' => 'Magic or resonance',
                            'title' => 'Magic or resonance 6, 1 rating 4 active skill',
                            'value' => 'magic',
                        ],
                        [
                            'name' => 'Skills (36/5)',
                            'title' => '36 points for individual skills and 5 points for skill groups',
                            'value' => 'skills',
                        ],
                    ],
                    'c' => [
                        [
                            'name' => 'Metatype',
                            'title' => 'Human, elf, dwarf, or ork',
                            'value' => 'metatype',
                        ],
                        [
                            'name' => 'Attributes (16)',
                            'title' => '16 attribute points to spend',
                            'value' => 'attributes',
                        ],
                        [
                            'name' => 'Magic or resonance',
                            'title' => 'Magic 4, 1 rating 2 active skill',
                            'value' => 'magic',
                        ],
                        [
                            'name' => 'Skills (28/2)',
                            'title' => '28 points for individual skills and 2 points for skill groups',
                            'value' => 'skills',
                        ],
                    ],
                    'd' => [
                        [
                            'name' => 'Metatype',
                            'title' => 'Human or elf',
                            'value' => 'metatype',
                        ],
                        [
                            'name' => 'Attributes (14)',
                            'title' => '14 attribute points to spend',
                            'value' => 'attributes',
                        ],
                        [
                            'name' => 'Magic or resonance',
                            'title' => 'Weak mage',
                            'value' => 'magic',
                        ],
                        [
                            'name' => 'Skills (22/0)',
                            'title' => '22 points for individual skills',
                            'value' => 'skills',
                        ],
                    ],
                    'e' => [
                        [
                            'name' => 'Metatype',
                            'title' => 'Human only',
                            'value' => 'metatype',
                        ],
                        [
                            'name' => 'Attributes (12)',
                            'title' => '12 attribute points to spend',
                            'value' => 'attributes',
                        ],
                        [
                            'name' => 'Mundane',
                            'title' => 'No magic or resonance',
                            'value' => 'magic',
                        ],
                        [
                            'name' => 'Skills (18/0)',
                            'title' => '18 points for individual skills',
                            'value' => 'skills',
                        ],
                    ],
                ];

                $options = [
                    'established' => $commonOptions,
                    'street' => $commonOptions,
                    'prime' => $commonOptions,
                ];

                $options['established']['a'][] = [
                    'name' => 'Resources (450,000&yen;)',
                    'title' => '450,000&yen; to spend',
                    'value' => 'resources',
                ];
                $options['established']['b'][] = [
                    'name' => 'Resources (275,000&yen;)',
                    'title' => '275,000&yen; to spend',
                    'value' => 'resources',
                ];
                $options['established']['c'][] = [
                    'name' => 'Resources (140,000&yen;)',
                    'title' => '140,000&yen; to spend',
                    'value' => 'resources',
                ];
                $options['established']['d'][] = [
                    'name' => 'Resources (50,000&yen;)',
                    'title' => '50,000&yen; to spend',
                    'value' => 'resources',
                ];
                $options['established']['e'][] = [
                    'name' => 'Resources (6,000&yen;)',
                    'title' => '6,000&yen; to spend',
                    'value' => 'resources',
                ];

                $options['prime']['a'][] = [
                    'name' => 'Resources (500,000&yen;)',
                    'title' => '500,000&yen; to spend',
                    'value' => 'resources',
                ];
                $options['prime']['b'][] = [
                    'name' => 'Resources (325,000&yen;)',
                    'title' => '325,000&yen; to spend',
                    'value' => 'resources',
                ];
                $options['prime']['c'][] = [
                    'name' => 'Resources (210,000&yen;)',
                    'title' => '210,000&yen; to spend',
                    'value' => 'resources',
                ];
                $options['prime']['d'][] = [
                    'name' => 'Resources (150,000&yen;)',
                    'title' => '150,000&yen; to spend',
                    'value' => 'resources',
                ];
                $options['prime']['e'][] = [
                    'name' => 'Resources (100,000&yen;)',
                    'title' => '100,000&yen; to spend',
                    'value' => 'resources',
                ];

                $options['street']['a'][] = [
                    'name' => 'Resources (75,000&yen;)',
                    'title' => '75,000&yen; to spend',
                    'value' => 'resources',
                ];
                $options['street']['b'][] = [
                    'name' => 'Resources (50,000&yen;)',
                    'title' => '50,000&yen; to spend',
                    'value' => 'resources',
                ];
                $options['street']['c'][] = [
                    'name' => 'Resources (25,000&yen;)',
                    'title' => '25,000&yen; to spend',
                    'value' => 'resources',
                ];
                $options['street']['d'][] = [
                    'name' => 'Resources (15,000&yen;)',
                    'title' => '15,000&yen; to spend',
                    'value' => 'resources',
                ];
                $options['street']['e'][] = [
                    'name' => 'Resources (6,000&yen;)',
                    'title' => '6,000&yen; to spend',
                    'value' => 'resources',
                ];

                $magic = [
                    'a' => [
                        'magician' => [
                            'name' => 'Magician',
                            'description' => 'Magic 6, two rating 5 magical skills, ten spells',
                        ],
                        'mystic' => [
                            'name' => 'Mystic adept',
                            'description' => 'Magic 6, two rating 5 magical skills, ten spells',
                        ],
                        'technomancer' => [
                            'name' => 'Technomancer',
                            'description' => 'Resonance 6, two rating 5 resonance skills, five complex forms',
                        ],
                    ],
                    'b' => [
                        'magician' => [
                            'name' => 'Magician',
                            'description' => 'Magic 4, two rating 4 magical skills, seven spells',
                        ],
                        'mystic' => [
                            'name' => 'Mystic adept',
                            'description' => 'Magic 4, two rating 4 magical skills, seven spells',
                        ],
                        'technomancer' => [
                            'name' => 'Technomancer',
                            'description' => 'Resonance 4, two rating 4 resonance skills, two complex forms',
                        ],
                        'adept' => [
                            'name' => 'Adept',
                            'description' => 'Magic 6, one rating 4 active skill',
                        ],
                        'aspected' => [
                            'name' => 'Aspected magician',
                            'description' => 'Magic 5, one rating 4 magical skill group',
                        ],
                    ],
                    'c' => [
                        'magician' => [
                            'name' => 'Magician',
                            'description' => 'Magic 3, five spells',
                        ],
                        'mystic' => [
                            'name' => 'Mystic adept',
                            'description' => 'Magic 3, five spells',
                        ],
                        'technomancer' => [
                            'name' => 'Technomancer',
                            'description' => 'Resonance 3, one complex form',
                        ],
                        'adept' => [
                            'name' => 'Adept',
                            'description' => 'Magic 4, one rating 2 active skill',
                        ],
                        'aspected' => [
                            'name' => 'Aspected magician',
                            'description' => 'Magic 3, one rating 2 magical skill group',
                        ],
                    ],
                    'd' => [
                        'adept' => [
                            'name' => 'Adept',
                            'description' => 'Magic 2',
                        ],
                        'aspected' => [
                            'name' => 'Aspected magician',
                            'description' => 'Magic 2',
                        ],
                    ],
                ];

                $races = [
                    'a' => [
                        'human' => 'Human (9)',
                        'elf' => 'Elf (8)',
                        'dwarf' => 'Dwarf (7)',
                        'ork' => 'Ork (7)',
                        'troll' => 'Troll (5)',
                    ],
                    'b' => [
                        'human' => 'Human (7)',
                        'elf' => 'Elf (6)',
                        'dwarf' => 'Dwarf (4)',
                        'ork' => 'Ork (4)',
                        'troll' => 'Troll (0)',
                    ],
                    'c' => [
                        'human' => 'Human (5)',
                        'elf' => 'Elf (3)',
                        'dwarf' => 'Dwarf (1)',
                        'ork' => 'Ork (0)',
                    ],
                    'd' => [
                        'human' => 'Human (3)',
                        'elf' => 'Elf (0)',
                    ],
                    'e' => [
                        'human' => 'Human (1)',
                    ],
                ];

                $selected = [
                    'a' => $character->priorities['a'] ?? $request->old('priority-a'),
                    'b' => $character->priorities['b'] ?? $request->old('priority-b'),
                    'c' => $character->priorities['c'] ?? $request->old('priority-c'),
                    'd' => $character->priorities['d'] ?? $request->old('priority-d'),
                    'e' => $character->priorities['e'] ?? $request->old('priority-e'),
                    'magic' => $character->priorities['magic'] ?? $request->old('magic'),
                    'metatype' => $character->priorities['metatype'] ?? $request->old('metatype'),
                ];

                return view(
                    'Shadowrun5e.create-standard',
                    [
                        'character' => $character,
                        'currentStep' => 'priorities',
                        'gameplay' => $character->priorities['gameplay'],
                        'magic' => $magic,
                        'nextStep' => $this->nextStep('priorities', $character),
                        'previousStep' => $this->previousStep('priorities', $character),
                        'priorities' => $options[$character->priorities['gameplay']],
                        'races' => $races,
                        'selected' => $selected,
                    ]
                );
            case 'qualities':
                return view(
                    'Shadowrun5e.create-qualities',
                    [
                        'books' => $books,
                        'character' => $character,
                        'currentStep' => 'qualities',
                        'nextStep' => $this->nextStep('qualities', $character),
                        'previousStep' => $this->previousStep('qualities', $character),
                    ]
                );
            case 'resonance':
                if (!$character->isTechnomancer()) {
                    return redirect('/characters/shadowrun5e/create/priority')
                        ->withErrors([
                            'error' => 'Only technomncers can choose sprites and forms.',
                        ]);
                }
                return view(
                    'Shadowrun5e.create-resonance',
                    [
                        'books' => $books,
                        'character' => $character,
                        'currentStep' => 'resonance',
                        'nextStep' => $this->nextStep('resonance', $character),
                        'previousStep' => $this->previousStep('resonance', $character),
                    ]
                );
            case 'rules':
                $books = collect(Rulebook::all())->values();
                // @phpstan-ignore-next-line
                $books = $books->mapToGroups(function (Rulebook $book, string $key): array {
                    if (0 === $key % 2) {
                        return ['even' => $book];
                    }
                    return ['odd' => $book];
                });
                return view(
                    'Shadowrun5e.create-rules',
                    [
                        'books' => $books,
                        'character' => $character,
                        'currentStep' => 'rules',
                        'nextStep' => $this->nextStep('rules', $character),
                        'previousStep' => $this->previousStep('rules', $character),
                        'selectedBooks' => $selectedBooks,
                    ]
                );
            case 'review':
                return view(
                    'Shadowrun5e.character',
                    [
                        'character' => $character,
                        'currentStep' => 'review',
                        'errors' => new MessageBag($character->errors ?? []),
                        'nextStep' => $this->nextStep('review', $character),
                        'previousStep' => $this->previousStep('review', $character),
                        'user' => $user,
                    ]
                );
            case 'skills':
                // Update the character's skills to include the skill's name so
                // we can show it if the user overspends on skills.
                $skills = $character->skills ?? [];
                foreach ($skills as $key => $rawSkill) {
                    try {
                        // Level doesn't matter, we're just getting the name.
                        $skill = new ActiveSkill($rawSkill['id'], 1);
                    } catch (RuntimeException) {
                        continue;
                    }
                    $skills[$key]['name'] = (string)$skill;
                }
                $character->skills = $skills;

                return view(
                    'Shadowrun5e.create-skills',
                    [
                        'character' => $character,
                        'currentStep' => 'skills',
                        'nextStep' => $this->nextStep('skills', $character),
                        'previousStep' => $this->previousStep('skills', $character),
                    ]
                );
            case 'social':
                return view(
                    'Shadowrun5e.create-social',
                    [
                        'books' => $books,
                        'character' => $character,
                        'currentStep' => 'social',
                        'nextStep' => $this->nextStep('social', $character),
                        'previousStep' => $this->previousStep('social', $character),
                    ]
                );
            case 'vehicles':
                return view(
                    'Shadowrun5e.create-vehicles',
                    [
                        'books' => $books,
                        'character' => $character,
                        'currentStep' => 'vehicles',
                        'nextStep' => $this->nextStep('vehicles', $character),
                        'previousStep' => $this->previousStep('vehicles', $character),
                    ]
                );
            case 'vitals':
                $selected = [
                    'birthdate' => $request->old('birthdate') ?? $character->background['birthdate'] ?? null,
                    'birthplace' => $request->old('birthplace') ?? $character->background['birthplace'] ?? null,
                    'eyes' => $request->old('eyes') ?? $character->background['eyes'] ?? null,
                    'gender' => $request->old('gender') ?? $character->background['gender'] ?? null,
                    'handle' => $request->old('handle') ?? $character->handle,
                    'hair' => $request->old('hair') ?? $character->background['hair'] ?? null,
                    'height' => $request->old('height') ?? $character->background['height'] ?? null,
                    'real-name' => $request->old('real-name') ?? $character->realName,
                    'weight' => $request->old('weight') ?? $character->background['weight'] ?? null,
                ];
                return view(
                    'Shadowrun5e.create-vitals',
                    [
                        'character' => $character,
                        'currentStep' => 'vitals',
                        'nextStep' => $this->nextStep('vitals', $character),
                        'previousStep' => $this->previousStep('vitals', $character),
                        'selected' => $selected,
                        'startDate' => $character->priorities['startDate'] ?? null,
                    ]
                );
            case 'weapons':
                return view(
                    'Shadowrun5e.create-weapons',
                    [
                        'books' => $books,
                        'character' => $character,
                        'currentStep' => 'weapons',
                        'nextStep' => $this->nextStep('weapons', $character),
                        'previousStep' => $this->previousStep('weapons', $character),
                    ]
                );
            default:
                return abort(
                    Response::HTTP_NOT_FOUND,
                    'That step of character creation was not found.',
                );
        }
    }

    public function storeAttributes(
        AttributesRequest $request
    ): RedirectResponse {
        /** @var User */
        $user = Auth::user();

        $characterId = $request->session()->get('shadowrun5epartial');
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $attributes = $request->only([
            'agility',
            'body',
            'charisma',
            'edge',
            'intuition',
            'logic',
            'magic',
            'reaction',
            'resonance',
            'strength',
            'willpower',
        ]);
        array_walk($attributes, function (&$value): void {
            $value = (int)$value;
        });
        $character->fill($attributes);
        if (
            !in_array(
                $character->priorities['magic'] ?? '',
                ['adept', 'aspected', 'magician', 'mystic'],
                true
            )
        ) {
            $character->magic = null;
        }
        if (($character->priorities['magic'] ?? '') !== 'technomancer') {
            $character->resonance = null;
        }
        $character->update();

        return $this->redirect($request->input('nav'), 'attributes', $character);
    }

    public function storeBackground(BackgroundRequest $request): RedirectResponse
    {
        /** @var User */
        $user = Auth::user();

        $characterId = $request->session()->get('shadowrun5epartial');
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $character->background = array_filter(array_merge(
            $character->background ?? [],
            $request->only([
                'age',
                'appearance',
                'born',
                'description',
                'education',
                'family',
                'gender-identity',
                'goals',
                'hate',
                'limitations',
                'living',
                'love',
                'married',
                'moral',
                'motivation',
                'name',
                'personality',
                'qualities',
                'religion',
                'size',
                'why',
            ]),
        ));
        $character->update();

        return $this->redirect(
            $request->input('nav'),
            'background',
            $character,
        );
    }

    public function storeKnowledgeSkills(
        KnowledgeSkillsRequest $request
    ): RedirectResponse {
        /** @var User */
        $user = Auth::user();

        $characterId = $request->session()->get('shadowrun5epartial');
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $categories = $request->input('skill-categories', []);
        $names = $request->input('skill-names', []);
        $levels = $request->input('skill-levels');
        $specializations = $request->input('skill-specializations');
        $skills = [];
        /** @var string */
        foreach (\array_keys($names) as $key) {
            $skill = [
                'category' => $categories[$key],
                'name' => $names[$key],
                'level' => $levels[$key],
            ];
            if ('N' !== $skill['level']) {
                $skill['level'] = (int)$skill['level'];
            }
            if (array_key_exists($key, $specializations) && null !== $specializations[$key]) {
                $skill['specialization'] = $specializations[$key];
            }
            $skills[] = $skill;
        }

        $character->knowledgeSkills = $skills;
        $character->update();
        return $this->redirect($request->input('nav'), 'knowledge', $character);
    }

    public function storeMartialArts(
        MartialArtsRequest $request
    ): RedirectResponse {
        /** @var User */
        $user = Auth::user();

        $characterId = $request->session()->get('shadowrun5epartial');
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $style = $request->input('style');
        if (null === $style) {
            $character->martialArts = null;
        } else {
            $character->martialArts = [
                'styles' => [
                    $style,
                ],
                'techniques' => $request->input('techniques', []),
            ];
        }
        $character->update();

        return $this->redirect($request->input('nav'), 'martial-arts', $character);
    }

    public function storeQualities(QualitiesRequest $request): RedirectResponse
    {
        /** @var User */
        $user = Auth::user();

        $characterId = $request->session()->get('shadowrun5epartial');
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $qualities = [];
        $rawQualities = $request->input('quality', []);
        foreach ($rawQualities as $id) {
            $extra = null;
            $id = explode('_', $id);
            if (1 !== count($id)) {
                $extra = implode(' ', array_slice($id, 1));
            }
            $id = $id[0];

            // The validator already validated that this is a valid quality ID.
            $quality = new Quality($id);
            // @phpstan-ignore-next-line
            if ('Addiction' === Quality::$qualities[$id]['name']) {
                $qualities[] = [
                    'id' => $id,
                    'addiction' => $extra ?? '',
                    'karma' => $quality->karma,
                ];
                continue;
            }
            // @phpstan-ignore-next-line
            if ('Allergy' === Quality::$qualities[$id]['name']) {
                $qualities[] = [
                    'id' => $id,
                    'allergy' => $extra ?? '',
                    'karma' => $quality->karma,
                ];
                continue;
            }
            $qualities[] = [
                'id' => $id,
                'karma' => $quality->karma,
            ];
        }

        $character->qualities = $qualities;
        $character->update();

        return $this->redirect($request->input('nav'), 'qualities', $character);
    }

    public function storeRules(RulesRequest $request): RedirectResponse
    {
        /** @var User */
        $user = Auth::user();

        $characterId = $request->session()->get('shadowrun5epartial');
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $rulebooks = $request->input('rulebook');
        sort($rulebooks);
        /** @var array<string, string> */
        $priorities = [
            'gameplay' => $request->input('gameplay'),
            'rulebooks' => implode(',', $rulebooks),
            'startDate' => $request->input('start-date'),
            'system' => $request->input('system'),
        ];

        $character->priorities = array_merge(
            $character->priorities ?? [],
            $priorities,
        );
        $character->update();

        return $this->redirect($request->input('nav'), 'rules', $character);
    }

    public function storeSkills(SkillsRequest $request): RedirectResponse
    {
        /** @var User */
        $user = Auth::user();

        $characterId = $request->session()->get('shadowrun5epartial');
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $groupNames = $request->input('group-names', []);
        $groupLevels = $request->input('group-levels', []);
        $skillGroups = [];
        foreach ($groupNames as $key => $group) {
            $skillGroups[(string)$group] = (int)$groupLevels[$key];
        }
        $character->skillGroups = $skillGroups;

        $skillNames = $request->input('skill-names', []);
        $skillLevels = $request->input('skill-levels');
        $specializations = $request->input('skill-specializations');
        $skills = [];
        foreach ($skillNames as $key => $id) {
            $skill = [
                'id' => $id,
                'level' => (int)$skillLevels[$key],
            ];
            if (array_key_exists($key, $specializations) && null !== $specializations[$key]) {
                $skill['specialization'] = $specializations[$key];
            }
            $skills[] = $skill;
        }
        $character->skills = $skills;

        $character->update();

        return $this->redirect($request->input('nav'), 'skills', $character);
    }

    public function storeSocial(SocialRequest $request): RedirectResponse
    {
        /** @var User */
        $user = Auth::user();

        $characterId = $request->session()->get('shadowrun5epartial');
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $contactNames = $request->input('contact-names', []);
        $contactArchetypes = $request->input('contact-archetypes');
        $contactConnections = $request->input('contact-connections');
        $contactLoyalties = $request->input('contact-loyalties');
        $contactNotes = $request->input('contact-notes');

        $contacts = [];
        foreach ($contactNames as $key => $name) {
            $contacts[] = [
                'name' => $name,
                'archetype' => $contactArchetypes[$key],
                'connection' => (int)$contactConnections[$key],
                'loyalty' => (int)$contactLoyalties[$key],
                'notes' => $contactNotes[$key] ?? null,
            ];
        }
        $character->contacts = $contacts;

        $character->update();

        return $this->redirect($request->input('nav'), 'social', $character);
    }

    public function storeStandard(
        StandardPriorityRequest $request
    ): RedirectResponse {
        /** @var User */
        $user = Auth::user();

        $characterId = $request->session()->get('shadowrun5epartial');
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $priorities = [
            'a' => $request->input('priority-a'),
            'b' => $request->input('priority-b'),
            'c' => $request->input('priority-c'),
            'd' => $request->input('priority-d'),
            'e' => $request->input('priority-e'),
            'magic' => null,
            'metatype' => $request->input('metatype'),
        ];
        if ($request->has('magic')) {
            $priorities['magic'] = $request->input('magic');
        }
        $character->priorities = array_merge(
            $character->priorities ?? [],
            $priorities,
        );
        $character->update();

        return $this->redirect($request->input('nav'), 'priorities', $character);
    }

    public function storeVitals(VitalsRequest $request): RedirectResponse
    {
        /** @var User */
        $user = Auth::user();

        $characterId = $request->session()->get('shadowrun5epartial');
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $character->handle = $request->input('handle');
        $background = $request->only([
            'birthdate',
            'birthplace',
            'eyes',
            'gender',
            'hair',
            'height',
            'weight',
        ]);
        if ($request->has('real-name')) {
            $character->realName = $request->input('real-name');
        }
        $character->background = array_merge(
            $character->background ?? [],
            $background,
        );
        $character->update();

        return $this->redirect($request->input('nav'), 'vitals', $character);
    }

    /**
     * Find a partial character if the user has already chosen one.
     * @param Request $request
     * @param ?string $step "Step" the user is working on, which could be an ID
     * @return ?PartialCharacter
     */
    protected function findPartialCharacter(
        Request $request,
        ?string $step,
    ): ?PartialCharacter {
        /** @var User */
        $user = Auth::user();

        // See if the user has already chosen to continue a character.
        $characterId = $request->session()->get('shadowrun5epartial');

        if (null !== $characterId) {
            // Return the character they're working on.
            return PartialCharacter::where('owner', $user->email)
                ->where('_id', $characterId)
                ->firstOrFail();
        }
        if (null === $step) {
            return null;
        }

        // Maybe they're chosing to continue a character right now.
        $character = PartialCharacter::where('owner', $user->email)
            ->find($step);
        if (null !== $character) {
            $request->session()->put('shadowrun5epartial', $character->id);
        }
        return $character;
    }

    /**
     * Return a collection of characters for the logged in user.
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        return CharacterResource::collection(
            // @phpstan-ignore-next-line
            Character::where('owner', Auth::user()->email)->get()
        );
    }

    /**
     * View all of the logged in user's characters.
     * @return View
     */
    public function list(): View
    {
        return view('Shadowrun5e.characters');
    }

    /**
     * Return a single Shadowrun 5E character.
     */
    public function show(Character $character): JsonResource
    {
        /** @var User */
        $user = Auth::user();

        $campaign = $character->campaign();
        abort_if(
            $user->email !== $character->owner
            && (null === $campaign || $user->isNot($campaign->gamemaster)),
            Response::HTTP_NOT_FOUND
        );
        return new CharacterResource($character);
    }

    public function update(Request $request, Character $character): JsonResource
    {
        /** @var ?User */
        $user = Auth::user();

        abort_if(
            null === $user,
            Response::HTTP_UNAUTHORIZED,
            'You must be logged in to update a character',
        );
        /*
        abort_if(
            $user->email === $character->owner,
            Response::HTTP_FORBIDDEN,
            'You can not update your own character (yet)',
        );
         */

        $campaign = $character->campaign();
        abort_if(
            null === $campaign,
            Response::HTTP_FORBIDDEN,
            'Only characters in campaigns can be updated this way',
        );
        abort_unless(
            $user->is($campaign->gamemaster),
            Response::HTTP_FORBIDDEN,
            'You can not update another user\'s character',
        );

        $document = [
            'damageOverflow' => $character->damageOverflow ?? 0,
            'damagePhysical' => $character->damagePhysical ?? 0,
            'damageStun' => $character->damageStun ?? 0,
            'edgeCurrent' => $character->edgeCurrent ?? $character->edge ?? 0,
        ];

        try {
            $change = json_decode(
                (new Patch(
                    (string)json_encode($document),
                    (string)json_encode($request->input('patch')),
                ))->apply()
            );
        } catch (InvalidPatchDocumentJsonException $ex) {
            // Will be thrown when using invalid JSON in a patch document.
            abort(Response::HTTP_BAD_REQUEST, $ex->getMessage()); // @codeCoverageIgnore
        } catch (InvalidOperationException $ex) {
            // Will be thrown when using an invalid JSON Pointer operation (i.e.
            // missing property)
            abort(Response::HTTP_BAD_REQUEST, $ex->getMessage());
        } catch (InvalidPointerException $ex) {
            abort(Response::HTTP_BAD_REQUEST, $ex->getMessage());
        }

        // If we go past the max for stun, it becomes physical at half rate.
        if ($change->damageStun > $character->stun_monitor) {
            $overflow = floor(($change->damageStun - $character->stun_monitor) / 2);
            $change->damagePhysical += $overflow;
            $change->damageStun = $character->stun_monitor;
        }
        // If we go past the max for physical, it goes to overflow.
        if ($change->damagePhysical > $character->physical_monitor) {
            $overflow = $change->damagePhysical - $character->physical_monitor;
            $change->damageOverflow = $character->damageOverflow + $overflow;
            $change->damagePhysical = $character->physical_monitor;
        }

        // For some reason PHPunit thinks the middle condition is never tested.
        if (
            ($change->damageStun > $character->damageStun)
            || ($change->damagePhysical > $character->damagePhysical) // @codeCoverageIgnore
            || ($change->damageOverflow > $character->damageOverflow)
        ) {
            $damage = [
                'stun' => $change->damageStun - $character->damageStun,
                'physical' => $change->damagePhysical - $character->damagePhysical,
                'overflow' => $change->damageOverflow - $character->damageOverflow,
            ];
            DamageEvent::dispatch($character, $campaign, (object)$damage);
        }

        $character->update((array)$change);
        return new CharacterResource($character);
    }

    /**
     * View a character's sheet.
     * @param string $identifier
     * @return View
     */
    public function view(string $identifier): View
    {
        try {
            $character = Character::where('_id', $identifier)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            $character = PartialCharacter::where('_id', $identifier)
                ->firstOrFail();
        }

        $user = Auth::user();
        return view(
            'Shadowrun5e.character',
            ['character' => $character, 'user' => $user]
        );
    }
}
