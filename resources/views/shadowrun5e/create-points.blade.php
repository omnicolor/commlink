<button aria-controls="points" class="btn btn-primary"
    data-bs-toggle="offcanvas" data-bs-target="#points" id="points-button"
    type="button">
    Points
</button>

<div aria-labelledby="points-label" class="offcanvas offcanvas-end
    @if (!isset($hide) || $hide === false)
        show
    @endif
    "
    data-bs-scroll="true" data-bs-backdrop="false" id="points" tabindex="-1">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="points-label">Points</h5>
        <button aria-label="Close" class="btn-close text-reset"
            data-bs-dismiss="offcanvas" type="button"></button>
    </div>
    <div class="offcanvas-body">
        <table class="table">
            <tr class="alert" style="display: none;" id="priority-point-row">
                <th class="tooltip-anchor" data-bs-placement="left"
                    data-bs-toggle="tooltip" scope="row"
                    title="Points you must use for sum-to-ten priority builds.">
                    Priority
                </th>
                <td class="text-end" id="priority-points">10</td>
            </tr>
            <tr class="alert">
                <th data-bs-placement="left" data-bs-toggle="tooltip" scope="row"
                    title="Points you use for your regular attributes (body, strength, logic, etc) on the vitals page.">
                    Attributes
                </th>
                <td class="text-end" id="attribute-points"></td>
            </tr>
            <tr class="alert">
                <th data-bs-placement="left" data-bs-toggle="tooltip" scope="row"
                    title="Points you use for special attributes (edge, magic, or resonance) on the vitals page.">
                    Special
                </th>
                <td class="text-end" id="special-points"></td>
            </tr>
            <tr class="alert">
                <th data-bs-placement="left" data-bs-toggle="tooltip" scope="row"
                    title="Karma that you can spend on positive qualities or raising skills or attributes. You may only carry 7 past character generation.">
                    Karma
                </th>
                <td class="text-end" id="karma"></td>
            </tr>
            <tr class="alert">
                <th data-bs-placement="left" data-bs-toggle="tooltip" scope="row"
                    title="Points you can spend on the skills page on active skills.">
                    Active skills
                </th>
                <td class="text-end" id="active-skills"></td>
            </tr>
            <tr class="alert">
                <th data-bs-placement="left" data-bs-toggle="tooltip" scope="row"
                    title="Points you can spend on skill groups on the skills page.">
                    Skill groups
                </th>
                <td class="text-end" id="skill-groups"></td>
            </tr>
            <tr class="alert">
                <th class="text-nowrap" data-bs-placement="left"
                    data-bs-toggle="tooltip" scope="row"
                    title="Points you can spend on knowledge skills and languages on the skills page.">
                    Knowledge skills
                </th>
                <td class="text-end" id="knowledge-skills"></td>
            </tr>
            <tr class="alert magic-show adept-show" style="display: none;">
                <th data-bs-placement="left" data-bs-toggle="tooltip" scope="row"
                    title="Points you can spend on spells or abilities on the magic page.">
                    Magics
                </th>
                <td class="text-end" id="magics"></td>
            </tr>
            <tr class="alert magic-show" style="display: none;">
                <th data-bs-placement="left" data-bs-toggle="tooltip" scope="row"
                    title="Free magical skills you gain from your magic rating.">
                    Magic skills
                </th>
                <td class="text-end" id="magic-skills"></td>
            </tr>
            <tr class="alert resonance-show" style="display: none;">
                <th data-bs-placement="left" data-bs-toggle="tooltip" scope="row"
                    title="Points you can spend on Complex Forms.">
                    Complex Forms
                </th>
                <td class="text-end" id="forms"></td>
            </tr>
            <tr class="alert resonance-show" style="display: none;">
                <th data-bs-placement="left" data-bs-toggle="tooltip" scope="row"
                    title="Free resonance, cracking, or electronics skills you gain from your resonance rating.">
                    Technical skills
                </th>
                <td class="text-end" id="resonance-skills"></td>
            </tr>
            <tr class="alert adept-show" style="display: none;">
                <th data-bs-placement="left" data-bs-toggle="tooltip" scope="row"
                    title="Points you can spend on Adept Powers on the magic page.">
                    Power Points
                </th>
                <td class="text-end" id="magic-points"></td>
            </tr>
            <tr class="alert">
                <th data-bs-placement="left" data-bs-toggle="tooltip" scope="row"
                    title="Nuyen you have to spend on gear, weapons, armor, vehicles, etc. You may only carry &yen;5,000 into play.">
                    Resources
                </th>
                <td class="text-end" id="resources"></td>
            </tr>
            <tr class="alert">
                <th data-bs-placement="left" data-bs-toggle="tooltip" scope="row"
                    title="Points you have to spend on contacts.">Contacts</th>
                <td class="text-end" id="contacts"></td>
            </tr>
        </table>
    </div>
</div>
