<x-app>
    <x-slot name="title">Create campaign</x-slot>
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard">Home</a>
        </li>
        <li class="nav-item">
            <span class="active nav-link">Create campaign</span>
        </li>
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
                    name="description">{{ old('description') }}</textarea>
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

        <div class="campaign-system" id="avatar-options" style="display:none;">
            <x-avatar.campaign-options />
        </div>

        <div class="campaign-system" id="cyberpunkred-options" style="display:none;">
            <x-cyberpunkred.campaign-options />
        </div>

        <div class="campaign-system" id="shadowrun5e-options" style="display:none;">
            <x-shadowrun5e.campaign-options />
        </div>

        <div class="mt-3 mb-4">
            <button class="btn btn-primary" type="submit">
                Create campaign
            </button>
        </div>
    </form>

    <div class="modal" id="book-info" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book title</h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <p>Filled by Javascript.</p>
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
                $('.campaign-system').hide();
                switch ($(e.target).val()) {
                    case 'avatar':
                        $('#avatar-options').show();
                        break;
                    case 'cyberpunkred':
                        $('#cyberpunkred-options').show();
                        break;
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

            $('.avatar-foci input[type="text"]').on('focus', function (e) {
                $(e.target).parents('.row').first().find('input[type="radio"]')
                    .prop('checked', true)
                    .change();
            });
            $('input[name="avatar-focus"]').on('change', function (e) {
                const selected = $(e.target)[0].id;
                const parent = $('.avatar-foci');
                parent.find('input[type="text"]').map(function () {
                    if (selected + '-object' === this.id) {
                        $(this).removeClass('form-control-plaintext')
                            .addClass('form-control')
                        if (this !== document.activeElement) {
                            $(this).focus();
                        }
                        return;
                    }
                    $(this).addClass('form-control-plaintext')
                        .removeClass('form-control')
                        .val('');
                });
            });
        });
    </script>
    </x-slot>
</x-app>
