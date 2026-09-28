<div class="enterprise-form-grid">

    <div class="form-group">
        <label for="name">Nom du ministère</label>

        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $ministry->name ?? '') }}"
            required
        >

        @error('name')
            <small class="form-error">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="code">Code administratif</label>

        <input
            id="code"
            name="code"
            type="text"
            value="{{ old('code', $ministry->code ?? '') }}"
            placeholder="Ex. MIN-SANTE"
        >

        <small>
            Laissez vide pour une génération automatique.
        </small>

        @error('code')
            <small class="form-error">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Adresse électronique</label>

        <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email', $ministry->email ?? '') }}"
        >
    </div>

    <div class="form-group">
        <label for="phone">Téléphone</label>

        <input
            id="phone"
            name="phone"
            type="text"
            value="{{ old('phone', $ministry->phone ?? '') }}"
        >
    </div>

    <div class="form-group form-group-full">
        <label for="description">Description</label>

        <textarea
            id="description"
            name="description"
            rows="5"
        >{{ old('description', $ministry->description ?? '') }}</textarea>
    </div>

    <div class="form-group form-checkbox">
        <input
            id="active"
            name="active"
            type="checkbox"
            value="1"
            @checked(old('active', $ministry->active ?? true))
        >

        <label for="active">Ministère actif</label>
    </div>

</div>