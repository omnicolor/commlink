<li class="list-group-item">
    {{ ucfirst($attribute) }}
    <div class="value" id="{{ $attribute }}">
        {{ $character->$attribute }}
    </div>
    @if (count($character->getFocuses($attribute)) > 0)
        <br>
        <small>Focuses:
            @foreach ($character->getFocuses($attribute) as $focus)
                @if ($focus->level === 2)
                    <u>
                @endif
                {{ $focus }}
                @if ($focus->level === 2)
                    </u>
                @endif
            @endforeach
        </small>
    @endif
</li>
