<div class="my-3 row">
    <label class="col-2 col-form-label" for="subversion-community-type">
        Community type
    </label>
    <div class="col">
        <input aria-describedby="subversion-community-type-help"
            class="form-control" id="subversion-community-type"
            name="subversion-community-type" type="text"
            value="{{ old('subversion-community-type') }}">
        <div class="form-text" id="subversion-community-type-help">
            Type defines the broad kind of community the PCs belong to.
            Communities have considerable influence, for good or ill, on all
            their members. Though many communities are defined by a location,
            others are defined by groups of people connected by an ideology. See
            core p76 for more information.
        </div>
    </div>
</div>

<div class="mb-3 row">
    <label class="col-2 col-form-label" for="subversion-community-description">
        Community description
    </label>
    <div class="col">
        <input aria-describedby="subversion-community-description-help"
            class="form-control" id="subversion-community-description"
            name="subversion-community-description" type="text"
            value="{{ old('subversion-community-description') }}">
        <div class="form-text" id="subversion-community-description-help">
            Additionally, groups are encouraged to come up with some aspects of
            the community that reflect its background, culture, and economy.
        </div>
    </div>
</div>
