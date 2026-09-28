@extends('layouts.admin')

@section('title','Paramètres Nationaux')

@section('content')

<section class="page-section">

<div class="page-heading">

<h1>Paramètres Nationaux</h1>

<p>Configuration générale de la Plateforme Nationale d'Administration Électronique.</p>

</div>

<div class="form-card">

@if(session('success'))

<div class="alert-success">

{{ session('success') }}

</div>

@endif

<form method="POST"
      action="{{ route('admin.settings.update') }}">

@csrf

<div class="form-grid">

<div class="form-group">

<label>Nom complet du portail</label>

<input
type="text"
name="portal_name"
value="{{ $settings['portal_name'] ?? '' }}">

</div>

<div class="form-group">

<label>Nom court</label>

<input
type="text"
name="portal_short_name"
value="{{ $settings['portal_short_name'] ?? '' }}">

</div>

<div class="form-group">

<label>Pays</label>

<input
type="text"
name="country"
value="{{ $settings['country'] ?? '' }}">

</div>

<div class="form-group">

<label>Email</label>

<input
type="email"
name="contact_email"
value="{{ $settings['contact_email'] ?? '' }}">

</div>

<div class="form-group">

<label>Téléphone</label>

<input
type="text"
name="contact_phone"
value="{{ $settings['contact_phone'] ?? '' }}">

</div>

<div class="form-group">

<label>Adresse</label>

<input
type="text"
name="address"
value="{{ $settings['address'] ?? '' }}">

</div>

<div class="form-group">

<label>Facebook</label>

<input
type="text"
name="facebook"
value="{{ $settings['facebook'] ?? '' }}">

</div>

<div class="form-group">

<label>Twitter</label>

<input
type="text"
name="twitter"
value="{{ $settings['twitter'] ?? '' }}">

</div>

<div class="form-group">

<label>LinkedIn</label>

<input
type="text"
name="linkedin"
value="{{ $settings['linkedin'] ?? '' }}">

</div>

</div>

<div class="form-group mt-4">

<label>Texte du pied de page</label>

<textarea
name="footer_text"
rows="4">{{ $settings['footer_text'] ?? '' }}</textarea>

</div>

<div class="form-group mt-3">

<label>

<input
type="checkbox"
name="maintenance_mode"

{{ ($settings['maintenance_mode'] ?? 0) ? 'checked' : '' }}>

Mode maintenance

</label>

</div>

<div class="form-actions mt-4">

<button class="btn-rca-primary">

Enregistrer les paramètres

</button>

</div>

</form>

</div>

</section>

@endsection