<x-app>
    <x-slot name="title">{{ $character->name }}</x-slot>
    <x-slot name="head">
        <style>
            h2 {
                background: #ed1a3f;
                color: #ffffff;
            }
            .attribute-full {
                width: 1em;
            }
            .attribute-full div {
                font-variant: small-caps;
                transform: rotate(-90deg);
            }
            .attribute-short,
            .attribute-value {
                font-size: 48px;
                text-align: center;
            }
            .attribute-title {
                border-bottom: 2px dashed #aaaaaa;
            }
            .company {
                border-bottom: 1px solid #000000;
                border-top: 2px dashed #000000;
                margin-left: 2em;
                margin-top: 2em;
            }
            .form-name {
                background: #000000;
                color: #ffffff;
                font-variant: small-caps;
                    width: 100%;
            }
            .scores {
                width: 100%;
            }
            .scores td {
                border: 1px solid #ed1a3f;
            }
        </style>
    </x-slot>

    <div class="row mt-4">
        <div class="col-1"></div>
        <div class="col">
            <div class="row">
                <div class="col-1"><div class="form-name">FORM WCOSF-VMAPP-QSR2A-V0001</div>
                </div>
                <div class="col-5 border border-start-0">
                    <div class="company">Worshipful Company<br>of Stillfleeters</div>
                </div>

                <div class="col-6 border p-0">
                    <h2>a-1. ID (name)</h2>
                    <div>{{ $character }}</div>
                    <h3>a-2. rank (title)</h3>

                    <h2 class="mb-0 mt-1">b. class(es)</h2>
                    <table class="border border-start-0" width="100%">
                    @foreach ($character->roles as $role)
                        <tr>
                            <td width="95%">{{ $role }}</td>
                            <td class="text-end">{{ $role->level }}</td>
                        </tr>
                    @endforeach
                    </table>
                    <h2>c. species</h2>
                    <p>&nbsp;</p>
                </div>
            </div>

            <div class="row">
                <div class="col border mt-4 p-0">
                    <h2>
                        d-1. life history (additional details on reverse)
                    </h2>
                    <p>&nbsp;</p>

                    <h3>
                        d-2. hustle (tachquake occupation; note advantage and disadvantage)
                    </h3>
                    <p>advantage:</p>
                    <p>disadvantage:</p>

                    <h3>d-3. kin (family, mentors, patrons</h3>
                    <p>&nbsp;</p>
                </div>
            </div>
        </div>

        <div class="col">
            <h2 class="mb-0">i. scores (checks)</h2>
            <table class="scores">
                <tr>
                    <td class="attribute-short" width="32%">COM</td>
                    <td class="attribute-full"><div>Combat</div></td>
                    <td class="attribute-actions" width="32%">
                        <ul>
                            <li>attack</li>
                            <li>grapple</li>
                        </ul>
                    </td>
                    <td class="attribute-value" width="32%">{{ $character->combat }}</td>
                </tr>
                <tr>
                    <td class="attribute-short">MOV</td>
                    <td class="attribute-full"><div>Movement</div></td>
                    <td class="attribute-actions">
                        <ul>
                            <li>drive/pilot</li>
                            <li>dodge</li>
                            <li>initiate</li>
                            <li>run</li>
                            <li>sneak</li>
                        </ul>
                    </td>
                    <td class="attribute-value">{{ $character->movement }}</td>
                </tr>
                <tr>
                    <td class="attribute-short">REA</td>
                    <td class="attribute-full"><div>Reason</div></td>
                    <td class="attribute-actions">
                        <ul>
                            <li>heal</li>
                            <li>know</li>
                            <li>make/repair</li>
                            <li>use tech</li>
                            <li>use weird power</li>
                        </ul>
                    </td>
                    <td class="attribute-value">{{ $character->reason }}</td>
                </tr>
                <tr>
                    <td class="attribute-short">WIL</td>
                    <td class="attribute-full"><div>Will</div></td>
                    <td class="attribute-actions">
                        <ul>
                            <li>empathize</li>
                            <li>perceive</li>
                            <li>resist</li>
                        </ul>
                    </td>
                    <td class="attribute-value">{{ $character->will }}</td>
                </tr>
                <tr>
                    <td class="attribute-short">CHA</td>
                    <td class="attribute-full"><div>Charm</div></td>
                    <td class="attribute-actions">
                        <ul>
                            <li>control</li>
                            <li>negotiate</li>
                            <li>seduce</li>
                        </ul>
                    </td>
                    <td class="attribute-value">{{ $character->charm }}</td>
                </tr>
            </table>

            <h2 class="mb-0 mt-2">j. pool</h2>
            <table class="scores">
                <tr>
                    <td class="attribute-short" width="32%">HEA</td>
                    <td class="attribute-full"><div>Health</div></td>
                    <td class="text-center" width="32%">
                        <div class="attribute-title fs-6 fw-lighter text-muted">total</div>
                        <div class="attribute-value">{{ $character->health }}</div>
                        <div class="fs-6 fw-lighter text-muted">(maxCOM + maxMOV)</div>
                    </td>
                    <td class="text-center" width="32%">
                        <div class="attribute-value">{{ $character->health_current }}</div>
                    </td>
                </tr>
                <tr>
                    <td class="attribute-short">GRT</td>
                    <td class="attribute-full"><div>Grit</div></td>
                    <td class="text-center">
                        <div class="attribute-title fs-6 fw-lighter text-muted">total</div>
                        <div class="attribute-value">{{ $character->grit }}</div>
                        <div class="fs-6 fw-lighter text-muted">(determined by class)</div>
                    </td>
                </tr>
            </table>

            <h2 class="mb-0 mt-2">k. damage reduction</h2>
        </div>
    </div>
    </div>
</x-app>
