<x-app>
    <x-slot name="title">
        Create campaign
    </x-slot>

    <h1>Create campaign</h1>

    @if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul>
        @foreach (array_unique($errors->all()) as $message)
            <li>{{ $message }}</li>
        @endforeach
        </ul>
    </div>
    @endif

    <form action="/campaigns/create" class="need-validation" method="POST"
        novalidate id="campaign-form">
        @csrf
        <div class="mb-3 row">
            <label class="col-2 col-form-label" for="name">
                Name <small>(required)</small>
            </label>
            <div class="col">
                <input aria-describedby="name-help" class="form-control"
                    id="name" maxlength="100" name="name" required type="text"
                    value="{{ old('name') }}">
                <div class="form-text invalid-feedback">
                    You must enter a name for your campaign.
                </div>
                <div class="form-text" id="name-help">
                    What you'll use to refer to the campaign, in case you have
                    more than one campaign.
                </div>
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-2 col-form-label" for="description">
                Description
            </label>
            <div class="col">
                <textarea aria-describedby="description-help"
                    class="form-control" id="description" maxlength="255"
                    name="description">
                    {{ old('description') }}
                </textarea>
                <div class="form-text" id="description-help">
                    Extra information you'd like to give to the players: what
                    nights you play, the tone of the table, or the themes of the
                    game.
                </div>
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-2 col-form-label" for="system">
                System <small>(required)</small>
            </label>
            <div class="col">
                <select class="form-control" id="system" name="system" required>
                    <option value="">Choose RPG system</option>
                    @foreach (config('app.systems') as $key => $system)
                        <option value="{{ $key }}"
                            @if ($key === old('system'))
                            selected
                            @endif
                        >{{ $system }}</option>
                    @endforeach
                </select>
                <div class="form-text invalid-feedback">
                    You must choose a system for your campaign.
                </div>
                <div class="form-text" id="system-help">
                    Name of the role playing system your table will be playing.
                </div>
            </div>
        </div>

        <div id="shadowrun5e-options" style="display:none;">
            <div class="mb-3 row">
                <label class="col-2 col-form-label" for="sr5e-start-date">
                    Start date
                </label>
                <div class="col">
                    <input class="form-control" id="sr5e-start-date"
                        name="sr5e-start-date" type="date"
                        value="{{ old('sr5e-start-date') }}">
                </div>
                <div class="form-text" id="sr5e-start-date-help">
                    Start date of your campaign: m/d/y
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-2 col-form-label">
                    Creation systems <small>(required)</small>
                </div>
                <div class="col">
                    <div class="form-check form-check-inline"
                         data-bs-toggle="tooltip"
                         title="Standard priority-based character generation from the Core Rulebook.">
                        <input aria-describedby="sr5e-creation-systems-help"
                            checked class="form-check-input"
                            id="sr5e-system-priority" name="sr5e-creation[]"
                            type="checkbox" value="priority">
                        <label class="form-check-label"
                            for="sr5e-system-priority">
                            Priority
                        </label>
                    </div>
                    <div class="form-check form-check-inline"
                        data-bs-toggle="tooltip"
                        title="Sum to Ten priority system from Run Faster.">
                        <input aria-describedby="sr5e-creation-systems-help"
                            checked class="form-check-input"
                            id="sr5e-system-ten" name="sr5e-creation[]"
                            type="checkbox" value="sum-to-ten">
                        <label class="form-check-label" for="sr5e-system-ten">
                            Sum to Ten
                        </label>
                    </div>
                    <div class="disabled form-check form-check-inline"
                        data-bs-toggle="tooltip"
                        title="Karma buy system from Run Faster page 64. Not yet implemented.">
                        <input aria-describedby="sr5e-creation-systems-help"
                            class="form-check-input" disabled
                            id="sr5e-system-karma" name="sr5e-creation[]"
                            type="checkbox" value="karma">
                        <label class="form-check-label" for="sr5e-system-karma">
                            Karma Buy
                        </label>
                    </div>
                    <div class="disabled form-check form-check-inline"
                         data-bs-toggle="tooltip"
                         title="Life module system from Run Faster page 65. Not yet implemented.">
                        <input aria-describedby="sr5e-creation-systems-help"
                            class="form-check-input" disabled
                            id="sr5e-system-life" name="sr5e-creation[]"
                            type="checkbox" value="life">
                        <label class="form-check-label" for="sr5e-system-life">
                            Life Modules
                        </label>
                    </div>
                    <div class="invalid-feedback"
                        id="sr5e-creation-systems-error">
                        You must choose at least one creation system.
                    </div>
                    <div class="form-text" id="sr5e-creation-systems-help">
                        Creation systems control the rules allowed when building
                        a new character.
                    </div>
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-2 col-form-label">
                    Level <small>(required)</small>
                </div>
                <div class="col">
                    <div class="form-check form-check-inline"
                        data-bs-toggle="tooltip"
                        title="Street-level runners: characters that have not yet had a chance to establish themselves as runners and are still in the process of earning their street cred. Obviously, these characters will not have the same gear or resources as the experienced shadowrunner.">
                        <input class="form-check-input" id="sr5e-level-street"
                            name="sr5e-gameplay" type="radio" value="street">
                        <label class="form-check-label" for="sr5e-level-street">
                            Street
                        </label>
                    </div>
                    <div class="form-check form-check-inline"
                        data-bs-toggle="tooltip"
                        title="Standard runners: characters have progressed in their careers long enough to not immediately be geeked at the first sign of trouble, but are still relatively unknown to the Johnsons of the world.">
                        <input checked class="form-check-input"
                            id="sr5e-level-established" name="sr5e-gameplay"
                            type="radio" value="established">
                        <label class="form-check-label"
                            for="sr5e-level-established">
                            Established
                        </label>
                    </div>
                    <div class="form-check form-check-inline"
                        data-bs-toggle="tooltip"
                        title="Prime runners: characters who have successfully been running the shadows long enough to have established their reputations as professionals in the eyes of Mr. Johnson. They possess the gear, the connections, and the talent to back up those reputations.">
                        <input class="form-check-input" id="sr5e-level-prime"
                            name="sr5e-gameplay" type="radio" value="prime">
                        <label class="form-check-label" for="sr5e-level-prime">
                            Prime
                        </label>
                    </div>
                    <div class="form-text" id="sr5e-creation-level-help">
                        The level controls the starting resources and gear
                        limits for new characters.
                    </div>
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-2 col-form-label">
                    Rulebooks <small>(required)</small>
                </div>
                <div class="col">
                    @foreach ($sr5eBooks as $key => $book)
                    <div class="col-3 form-check form-check-inline">
                        <input
                            class="form-check-input ebook"
                            @if ($book['required'] ?? false)
                            checked disabled
                            @elseif ($book['default'] ?? true)
                            checked
                            @endif
                            id="sr5e-rulebook-{{ $key }}" name="sr5e-rules[]"
                            type="checkbox" value="{{ $key }}">
                        <label class="form-check-label"
                            for="sr5e-rulebook-{{ $key }}">
                            {{ $book['name'] }}
                        </label>
                        @if ('' !== $book['description'])
                            <i class="bi bi-info-circle"
                                data-bs-toggle="modal" data-bs-target="#book-info"
                                data-bs-title="{{ $book['name'] }}"
                                data-bs-description="<p>{{ str_replace('||', '</p><p>', $book['description']) }}</p>"
                                ></i>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div>
            <button class="btn btn-primary" type="submit">
                Create campaign
            </button>
        </div>
    </form>

    <div class="modal" id="book-info" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Modal body text goes here.</p>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="javascript">
    <script>
        $(function () {
            var tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            $('#campaign-form').on('submit', function (e) {
                var form = $('#campaign-form');
                form.addClass('was-validated');
                if (form[0].checkValidity() === false) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                return true;
            });

            $('#system').on('change', function (e) {
                $('input[name="creation[]"]').off();
                switch ($(e.target).val()) {
                    case 'shadowrun5e':
                        $('input[name="creation[]"]').on('change', function() {
                            if (0 === $('input[name="creation[]"]:checked').length) {
                                $('input[name="creation[]"]').prop('required', true);
                                $('#creation-systems-error').show();
                            } else {
                                $('input[name="creation[]"]').prop('required', false);
                                $('#creation-systems-error').hide();
                            }
                        });
                        $('#shadowrun5e-options').show();
                        break;
                    default:
                        $('#shadowrun5e-options').hide();
                        break;
                }
            }).change();

            $('#book-info').on('show.bs.modal', function (e) {
                var modal = document.getElementById('book-info');
                var el = e.relatedTarget;
                var title = el.getAttribute('data-bs-title');
                var description = el.getAttribute('data-bs-description');
                modal.querySelector('.modal-title').textContent = title;
                modal.querySelector('.modal-body').innerHTML = description;
            });
        });
    </script>
    </x-slot>
</x-app>
