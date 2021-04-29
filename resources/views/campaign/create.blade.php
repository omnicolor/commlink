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
            <x-shadowrun5e.campaign-options />
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
