<x-app>
    <x-slot name="title">Create character</x-slot>
    @include('cyberpunkred.create-navigation')

    <div class="row">
        <div class="col">
            <h1>Generate statistics</h1>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <p>
                Statistics (also called STATs) are numbers that describe your
                Character's abilities in the game, as compared to everything
                else in the universe. All people and creatures can be described
                (or written up) using Statistics. This helps you compare
                Characters' abilities, which is often important in the game. For
                instance, a person with a STAT of 5 might be better off than a
                person with a STAT of 4, but not as good as a person with a STAT
                of 6. Statistics are generally rated from 1 to 8, but can go
                higher.
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <h2>Methods of generating statistics:</h2>

            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button aria-controls="streetrat" aria-disabled="false"
                        aria-selected="false" class="nav-link"
                        data-bs-target="#streetrat" data-bs-toggle="tab"
                        disabled id="streetrat-tab" role="tab" type="button">
                        Streetrat
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button aria-controls="edgerunner" aria-disabled="false"
                        aria-selected="false" class="nav-link"
                        data-bs-target="#edgerunner" data-bs-toggle="tab"
                        disabled id="edgerunner-tab" role="tab" type="button">
                        Edgerunner
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button aria-controls="complete" aria-selected="true"
                        class="nav-link active" data-bs-target="#complete"
                        data-bs-toggle="tab" id="complete-tab" role="tab"
                        type="button">
                        Complete
                    </button>
                </li>
            </ul>

            <div class="tab-content p-4">
                <div aria-labelledby="streetrat-tab" class="tab-pane fade"
                    id="streetrat" role="tabpanel">
                    <p>
                        When using The Streetrat Option, you'll roll 1d10 and
                        then copy the numbers adjacent to the result of that
                        roll onto your Character Sheet. You may not move your
                        STATs around; you must transfer them as written on the
                        table for that Roll. The good news is that these tables
                        have been computer-generated to give you an optimal
                        Character for that type of Role.
                    </p>
                </div>
                <div aria-labelledby="edgerunner-tab" class="tab-pane fade"
                    id="edgerunner" role="tabpanel">
                    <p>
                        Similar to the Streetrat option, when using the
                        Edgerunner option, you will once again move to the
                        Templates for your Character's Role (see pg. 74). This
                        time, you will roll 1d10 for each STAT individually,
                        comparing the roll for that STAT against the value on
                        the column for that STAT.
                    </p>
                </div>
                <div aria-labelledby="complete-tab"
                    class="tab-pane fade show active" id="complete"
                    role="tabpanel">
                    <p>
                        This method allows you to build the Character from the
                        ground up, using a pool of &ldquo;Character
                        points&rdquo; to &ldquo;buy&rdquo; the Character's
                        STATs. While it's the most flexible method, it's also
                        the most time-consuming and is not recommended for
                        novice roleplayers.
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('cyberpunkred-create-stats') }}" method="POST">
                    @csrf
                    <input name="type" type="hidden" value="complete">
                    <table class="table" id="complete-stats">
                        <tr>
                            <td colspan="5" nowrap>
                                <div class="row">
                                    <label class="col-sm-2 col-form-label"
                                        for="stat-points"
                                        id="stat-points-label">
                                        Stat points:
                                        <i class="bi bi-check-circle text-success" style="display:none"></i>
                                    </label>
                                    <div class="col-sm-1">
                                        <input class="form-control-plaintext"
                                            id="stat-points" readonly
                                            type="text" value="62">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><div class="form-floating">
                                <input class="form-control" id="complete-int"
                                    max="8" min="2" name="intelligence"
                                    placeholder="INT" required step="1"
                                    type="number"
                                    value="{{ $character->int ?? 2 }}">
                                <label for="complete-int">INT</label>
                            </div></td>
                            <td><div class="form-floating">
                                <input class="form-control" id="complete-ref"
                                    max="8" min="2" name="reflexes"
                                    placeholder="REF" required step="1"
                                    type="number"
                                    value="{{ $character->reflexes ?? 2 }}">
                                <label for="complete-ref">REF</label>
                            </div></td>
                            <td><div class="form-floating">
                                <input class="form-control" id="complete-dex"
                                    max="8" min="2" name="dexterity"
                                    placeholder="DEX" required step="1"
                                    type="number"
                                    value="{{ $character->dexterity ?? 2 }}">
                                <label for="complete-dex">DEX</label>
                            </div></td>
                            <td><div class="form-floating">
                                <input class="form-control" id="complete-tech"
                                    max="8" min="2" name="technique"
                                    placeholder="TECH" required step="1"
                                    type="number"
                                    value="{{ $character->technique ?? 2 }}">
                                <label for="complete-tech">TECH</label>
                            </div></td>
                            <td><div class="form-floating">
                                <input class="form-control" id="complete-cool"
                                    max="8" min="2" name="cool"
                                    placeholder="COOL" required step="1"
                                    type="number"
                                    value="{{ $character->cool ?? 2 }}">
                                <label for="complete-cool">COOL</label>
                            </div></td>
                        </tr>
                        <tr>
                            <td><div class="form-floating">
                                <input class="form-control" id="complete-will"
                                    max="8" min="2" name="willpower"
                                    placeholder="WILL" required step="1"
                                    type="number"
                                    value="{{ $character->willpower ?? 2 }}">
                                <label for="complete-will">WILL</label>
                            </div></td>
                            <td><div class="form-floating">
                                <input class="form-control" id="complete-luck"
                                    max="8" min="2" name="luck"
                                    placeholder="LUCK" required step="1"
                                    type="number"
                                    value="{{ $character->luck ?? 2 }}">
                                <label for="complete-luck">LUCK</label>
                            </div></td>
                            <td><div class="form-floating">
                                <input class="form-control" id="complete-move"
                                    max="8" min="2" name="movement"
                                    placeholder="MOVE" required step="1"
                                    type="number"
                                    value="{{ $character->movement ?? 2 }}">
                                <label for="complete-move">MOVE</label>
                            </div></td>
                            <td><div class="form-floating">
                                <input class="form-control" id="complete-body"
                                    max="8" min="2" name="body"
                                    placeholder="BODY" required step="1"
                                    type="number"
                                    value="{{ $character->body ?? 2 }}">
                                <label for="complete-body">BODY</label>
                            </div></td>
                            <td><div class="form-floating">
                                <input class="form-control" id="complete-emp"
                                    max="8" min="2" name="empathy"
                                    placeholder="EMP" required step="1"
                                    type="number"
                                    value="{{ $character->empathy_original ? $character->empathy_original : 2 }}">
                                <label for="complete-emp">EMP</label>
                            </div></td>
                        </tr>
                        <tr>
                            <td colspan=4"></td>
                            <td>
                                <button class="btn btn-outline-warning" type="submit">
                                    Set statistics
                                </button>
                            </td>
                        </tr>
                    </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-slot name="javascript">
    <script>
        $(function () {
            function updatePoints() {
                const inputs = $('#complete-stats input[type="number"]');
                let points = 62;
                $.each(inputs, function (index, el) {
                    points -= el.value;
                });
                $('#stat-points').val(points);
                if (points === 0) {
                    $('#stat-points-label i').show();
                    $('#complete-stats button')
                        .addClass('btn-success')
                        .removeClass('btn-outline-warning');
                } else {
                    $('#stat-points-label i').hide();
                    $('#complete-stats button')
                        .addClass('btn-outline-warning')
                        .removeClass('btn-success');
                }
            }
            $('#complete-stats input[type="number"]').on('change', updatePoints);
            updatePoints();
        });
    </script>
    </x-slot>
</x-app>
