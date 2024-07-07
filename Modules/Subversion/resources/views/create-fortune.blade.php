<button aria-controls="fortune" class="btn btn-primary mt-4 position-fixed top-0 end-0"
    data-bs-toggle="offcanvas" data-bs-target="#fortune" id="fortune-button"
    type="button">
    Fortune
</button>

<div aria-labelledby="fortune-label" class="offcanvas offcanvas-end show"
    data-bs-scroll="true" data-bs-backdrop="false" id="fortune" tabindex="-1">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="fortune-label">Fortune</h5>
        <button aria-label="Close" class="btn-close text-reset"
            data-bs-dismiss="offcanvas" type="button"></button>
    </div>
    <div class="offcanvas-body">
        <p>
            When starting new characters, players have 320 Fortune to spend
            between attributes, skills, paradigms, gear, and petty cash. Players
            may increase or decrease their available Fortune through their
            choice of caste, start with debt, or if they choose to begin with a
            corrupted Value.
        </p>

        <div class="row">
            <div class="col">Starting fortune</div>
            <div class="col-3 text-end">320</div>
        </div>

        <div class="row">
            <div class="col" id="fortune-caste-name">
                Caste
                @if (null !== $character->caste)
                    ({{ $character->caste }})
                @endif
            </div>
            <div class="col-3 text-end" id="fortune-caste-amount">
                @if (null !== $character->caste && 0 <= $character->caste->fortune)
                    &plus;{{ $character->caste->fortune }}
                @elseif (null !== $character->caste)
                    {{ $character->caste->fortune }}
                @else
                +0
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col">Corrupted value</div>
            <div class="col-3 text-end" id="fortune-corrupted">
                @if ($character->corrupted_value ?? false)
                    +5
                @else
                    +0
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col">Debt</div>
            <div class="col-3 text-end">+0</div>
        </div>

        <div class="row">
            <div class="col">
                Relations
                <span class="badge rounded-pill text-bg-info" style="background-color: #0dcaf0 !important"
                    data-bs-toggle="tooltip"
                    data-bs-title="You get 30 fortune that can only be spent on relations, but may spend additional fortune.">30 free</span>
            </div>
            <div class="col-3 text-end" id="relation-fortune">+0</div>
        </div>

        <div class="row">
            <div class="col">Background paradigm</div>
            <div class="col-3 text-end">+10</div>
        </div>

        <div class="row">
            <div class="col">Remaining fortune</div>
            <div class="col-3 text-end" id="fortune-remaining">
                {{ $character->fortune }}
            </div>
        </div>

        <div class="mt-4 row">
            <div class="col"><h5>Suggested allocation</h5></div>
        </div>

        <div class="row">
            <div class="col">Attributes</div>
            <div class="col-3 text-end">80</div>
        </div>

        <div class="row">
            <div class="col">Skills</div>
            <div class="col-3 text-end">165</div>
        </div>

        <div class="row">
            <div class="col">Paradigms</div>
            <div class="col-3 text-end">60 (+10)</div>
        </div>

    </div>
</div>
