<div class="enterprise-form-grid">

    <div class="form-group">
        <label for="name">Nom complet</label>

        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $user->name ?? '') }}"
            required
        >

        @error('name')
            <small class="form-error">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Adresse électronique</label>

        <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email', $user->email ?? '') }}"
            required
        >

        @error('email')
            <small class="form-error">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="phone">Téléphone</label>

        <input
            id="phone"
            name="phone"
            type="text"
            value="{{ old('phone', $user->phone ?? '') }}"
        >
    </div>

    <div class="form-group">
        <label for="role">Rôle</label>

        <select id="role" name="role" required>
            <option value="">Sélectionner</option>

            @foreach($roles as $value => $label)
                <option
                    value="{{ $value }}"
                    @selected(
                        old('role', $user->role ?? '') === $value
                    )
                >
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('role')
            <small class="form-error">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group" id="ministryField">
        <label for="ministry_id">Ministère</label>

        <select id="ministry_id" name="ministry_id">
            <option value="">Sélectionner</option>

            @foreach($ministries as $ministry)
                <option
                    value="{{ $ministry->id }}"
                    @selected(
                        (string) old(
                            'ministry_id',
                            $user->ministry_id ?? ''
                        ) === (string) $ministry->id
                    )
                >
                    {{ $ministry->name }}
                </option>
            @endforeach
        </select>

        @error('ministry_id')
            <small class="form-error">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">
            {{ isset($user)
                ? 'Nouveau mot de passe (facultatif)'
                : 'Mot de passe initial' }}
        </label>

        <input
            id="password"
            name="password"
            type="password"
            {{ isset($user) ? '' : 'required' }}
        >

        @error('password')
            <small class="form-error">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">
            Confirmation du mot de passe
        </label>

        <input
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            {{ isset($user) ? '' : 'required' }}
        >
    </div>

    <div class="form-group form-checkbox">
        <input
            id="active"
            name="active"
            type="checkbox"
            value="1"
            @checked(old('active', $user->active ?? true))
        >

        <label for="active">Compte actif</label>
    </div>

</div>