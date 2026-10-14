<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
{
    $user = $this->user();

    if (! $user || ! $user->active) {
        return false;
    }

    if ($user->role !== 'agent') {
        return false;
    }

    $application = $this->route('application');

    if (! $application instanceof \App\Models\Application) {
        return false;
    }

    $application->loadMissing('procedure');

    return $application->assigned_to !== null
        && (int) $application->assigned_to === (int) $user->id
        && $application->procedure !== null
        && $user->belongsToMinistry(
            (int) $application->procedure->ministry_id
        );
}

    public function messages(): array
    {
        return [
            'status' => [
            'required',
            'in:en_traitement',
            'comment.max' =>
                'Le commentaire ne doit pas dépasser 2 000 caractères.',
        ];
    }
}