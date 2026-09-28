<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }

    public function rules(): array
    {
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
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'password' => [
                'required',
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
                        'Vous pouvez créer un agent uniquement dans votre ministère.'
                    );
                }
            },
        ];
    }
}