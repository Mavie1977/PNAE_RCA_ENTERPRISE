<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Citizen\InitiatePaymentRequest;
use App\Models\Application;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Application $application)
    {
        abort_unless(
            (int) $application->user_id === (int) auth()->id(),
            403
        );

        $application->load([
            'procedure.ministry',
            'latestPayment',
        ]);

        return view(
            'citizen.payments.create',
            compact('application')
        );
    }

    public function store(
        InitiatePaymentRequest $request,
        Application $application,
        PaymentService $paymentService
    ) {
        $application->load('procedure');

        $payment = $paymentService->initiate(
            $application,
            $request->user(),
            $request->validated()
        );

        return redirect()
            ->route('citizen.payments.show', $payment)
            ->with(
                'success',
                'Le paiement a été initialisé avec succès.'
            );
    }

    public function show(Payment $payment)
    {
        abort_unless(
            (int) $payment->user_id === (int) auth()->id(),
            403
        );

        $payment->load([
            'application.procedure.ministry',
            'user',
        ]);

        return view(
            'citizen.payments.show',
            compact('payment')
        );
    }

    public function confirm(
        Request $request,
        Payment $payment,
        PaymentService $paymentService
    ) {
        $paymentService->confirmSandboxPayment(
            $payment,
            $request->user()
        );

        return redirect()
            ->route('citizen.payments.show', $payment)
            ->with(
                'success',
                'Paiement confirmé avec succès en environnement de démonstration.'
            );
    }

    public function cancel(
        Request $request,
        Payment $payment,
        PaymentService $paymentService
    ) {
        $paymentService->cancel(
            $payment,
            $request->user()
        );

        return redirect()
            ->route('citizen.payments.show', $payment)
            ->with(
                'warning',
                'Le paiement a été annulé.'
            );
    }

    public function index()
    {
        $payments = Payment::with([
            'application.procedure',
        ])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view(
            'citizen.payments.index',
            compact('payments')
        );
    }
}