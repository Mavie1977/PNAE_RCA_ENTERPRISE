<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $targetUser = $this->route('user');

        return $targetUser instanceof User
            && ($this->user()?->can('update', $targetUser) ?? false);
    }

    public function rules(): array
    {
        /** @var User $targetUser */
        $targetUser = $this->route('user');

        $allowedRoles = $this->user()?->isAdmin()
            ? array_keys(User::roles())
            : [User::ROLE_AGENT];

        return [
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'email' => [
                'required',
                'email',
                'max:190',
                Rule::unique('users', 'email')
                    ->ignore($targetUser->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                Rule::in($allowedRoles),
            ],

            'ministry_id' => [
                'nullable',
                'integer',
                'exists:ministries,id',
            ],

            'active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $role = $this->string('role')->toString();
                $ministryId = $this->integer('ministry_id') ?: null;

                if (
                    in_array(
                        $role,
                        [User::ROLE_AGENT, User::ROLE_RESPONSABLE],
                        true
                    )
                    && $ministryId === null
                ) {
                    $validator->errors()->add(
                        'ministry_id',
                        'Le ministère est obligatoire pour ce rôle.'
                    );
                }

                if (
                    $this->user()?->isResponsable()
                    && $ministryId !== (int) $this->user()->ministry_id
                ) {
                    $validator->errors()->add(
                        'ministry_id',
                        'Le changement de ministère est interdit.'
                    );
                }
            },
        ];
    }
}