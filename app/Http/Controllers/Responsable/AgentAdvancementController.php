<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use App\Models\AgentAdvancement;
use App\Models\HrCategory;
use App\Models\HrFunction;
use App\Models\HrGrade;
use App\Models\User;
use App\Services\HumanResources\AgentCareerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AgentAdvancementController extends Controller
{
    public function __construct(
        private readonly AgentCareerService $careerService
    ) {
    }

    public function create(User $agent): View
    {
        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404
        );

        $agent->load([
            'ministry',
            'agentProfile.categoryEntity',
            'agentProfile.gradeEntity',
            'agentProfile.hrFunction',
        ]);

        $categories = HrCategory::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->get();

        $grades = HrGrade::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->get();

        $functions = HrFunction::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->get();

        return view(
            'responsable.recruitment.agents.advancement',
            compact(
                'agent',
                'categories',
                'grades',
                'functions'
            )
        );
    }

    public function store(
        Request $request,
        User $agent
    ): RedirectResponse {

        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404
        );

        $validated = $request->validate([
            'advancement_type' => [
                'required',
                Rule::in([
                    AgentAdvancement::TYPE_ADVANCEMENT,
                    AgentAdvancement::TYPE_PROMOTION,
                    AgentAdvancement::TYPE_RECLASSIFICATION,
                ]),
            ],

            'new_category_id' => [
                'required',
                Rule::exists('hr_categories', 'id')
                    ->where('active', true),
            ],

            'new_grade_id' => [
                'required',
                Rule::exists('hr_grades', 'id')
                    ->where('active', true),
            ],

            'new_function_id' => [
                'nullable',
                Rule::exists('hr_functions', 'id')
                    ->where('active', true),
            ],

            'reference' => [
                'required',
                'string',
                'max:150',
            ],

            'reason' => [
                'required',
                'string',
                'max:2000',
            ],

            'effective_at' => [
                'required',
                'date',
            ],
        ]);

        /*
         * Cohérence catégorie / grade
         */
        $grade = HrGrade::findOrFail(
            $validated['new_grade_id']
        );

        if (
            (int) $grade->category_id !==
            (int) $validated['new_category_id']
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'new_grade_id' =>
                        'Le grade sélectionné ne correspond pas à la catégorie choisie.',
                ]);
        }

        $this->careerService->advance(
            $agent,
            $validated,
            $request->user()
        );

        return redirect()
            ->route(
                'responsable.agents.show',
                $agent
            )
            ->with(
                'success',
                'L’opération de carrière a été enregistrée et historisée.'
            );
    }
}