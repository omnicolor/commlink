<x-app>
    <x-slot name="title">Role-based lifepath</x-slot>
    @include('Cyberpunkred.create-navigation')

    <h1>Role-based lifepath</h1>

    <p>
        Some things about life are universal. Other things are pretty specific.
        One of these is how your day job (or night job or side job or
        whatever—we won't judge you) affects your life. The things that a
        hard-bitten Lawman on The Street has to face are way different from the
        glittering club life of a Rockerboy, and they both deal with stuff no
        pampered and privileged Exec could even imagine. To that end, we've
        constructed a series of Role-based Lifepaths that supplement the regular
        Lifepath. Have fun!
    </p>

    <p>
        Different Nomad groups include the Aldecaldos, who are helping rebuild
        Night City; the Jodes, originally farmers from the American Midwest; the
        Blood Nation, who specialize in traveling entertainment; and the Meta,
        made up from military personnel abandoned during the SouthAm Wars.
    </p>

    <div class="accordion" id="lifepath">
        <div class="accordion-item">
            <h2 class="accordion-header" id="pack-size-heading">
                <button aria-controls="pack-size" aria-expanded="true"
                    class="accordion-button" data-bs-target="#pack-size"
                    data-bs-toggle="collapse" type="button">
                    How big is your pack?
                </button>
            </h2>
            <div aria-labelledby="pack-size-heading"
                class="accordion-collapse collapse show"
                data-bs-parent="#lifepath" id="pack-size">
                <div class="accordion-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Pack size</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>A single extended tribe or family</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="pack-based-heading">
                <button aria-controls="pack-based" aria-expanded="false"
                    class="accordion-button collapsed"
                    data-bs-target="#pack-based" data-bs-toggle="collapse"
                    type="button">
                    Where does your pack operate?
                </button>
            </h2>
            <div aria-labelledby="back-based-heading"
                class="accordion-collapse collapse"
                data-bs-parent="#lifepath" id="pack-based">
                <div class="accordion-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Operating area</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Land nomads</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Air nomads</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Sea nomads</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="air-heading">
                <button aria-controls="air" aria-expanded="false"
                    class="accordion-button collapsed"
                    data-bs-target="#air" data-bs-toggle="collapse"
                    type="button">
                    Air nomads
                </button>
            </h2>
            <div aria-labelledby="air-heading"
                class="accordion-collapse collapse"
                data-bs-parent="#lifepath" id="air">
                <div class="accordion-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Air piracy</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Cargo transport</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Passenger transport</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Aircraft protection</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Smuggling</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>Combat support</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app>
