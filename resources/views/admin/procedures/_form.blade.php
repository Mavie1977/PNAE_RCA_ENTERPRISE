@php
    $procedure = $procedure ?? null;
@endphp

<div class="enterprise-form-grid">

    <div class="form-group">
        <label for="ministry_id">Ministère</label>

        <select
            id="ministry_id"
            name="ministry_id"
            required
        >
            <option value="">Choisir un ministère</option>

            @foreach($ministries as $ministry)
                <option
                    value="{{ $ministry->id }}"
                    @selected(
                        (string) old(
                            'ministry_id',
                            $procedure?->ministry_id
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
        <label for="title">Nom de la démarche</label>

        <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $procedure?->title) }}"
            required
        >

        @error('title')
            <small class="form-error">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="processing_days">Délai officiel en jours</label>

        <input
            id="processing_days"
            name="processing_days"
            type="number"
            min="1"
            value="{{ old(
                'processing_days',
                $procedure?->processing_days ?? 7
            ) }}"
        >

        @error('processing_days')
            <small class="form-error">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="fee">Frais administratifs</label>

        <input
            id="fee"
            name="fee"
            type="number"
            min="0"
            step="1"
            value="{{ old('fee', $procedure?->fee ?? 0) }}"
            required
        >

        @error('fee')
            <small class="form-error">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group form-group-full">
        <label for="description">Description</label>

        <textarea
            id="description"
            name="description"
            rows="5"
        >{{ old('description', $procedure?->description) }}</textarea>

        @error('description')
            <small class="form-error">{{ $message }}</small>
        @enderror
    </div>

    @if(
        \Illuminate\Support\Facades\Schema::hasColumn(
            'procedures',
            'required_documents'
        )
    )
        <div class="form-group form-group-full">
            <label for="required_documents">
                Documents obligatoires
            </label>

            <textarea
                id="required_documents"
                name="required_documents"
                rows="4"
                placeholder="Ex. Acte de naissance, photo d’identité..."
            >{{ old(
                'required_documents',
                $procedure?->required_documents
            ) }}</textarea>

            @error('required_documents')
                <small class="form-error">{{ $message }}</small>
            @enderror
        </div>
    @endif

    <div class="form-group form-checkbox">
        <input
            id="payment_required"
            name="payment_required"
            type="checkbox"
            value="1"
            @checked(
                old(
                    'payment_required',
                    $procedure?->payment_required ?? false
                )
            )
        >

        <label for="payment_required">
            Paiement obligatoire
        </label>
    </div>

    <div class="form-group form-checkbox">
        <input
            id="official_document_required"
            name="official_document_required"
            type="checkbox"
            value="1"
            @checked(
                old(
                    'official_document_required',
                    $procedure?->official_document_required ?? true
                )
            )
        >

        <label for="official_document_required">
            Document officiel requis
        </label>
    </div>

    <div class="form-group form-checkbox">
        <input
            id="active"
            name="active"
            type="checkbox"
            value="1"
            @checked(
                old(
                    'active',
                    $procedure?->active ?? true
                )
            )
        >

        <label for="active">
            Démarche active
        </label>
    </div>

</div>