<?php

namespace App\Livewire\Admin\Collect;

use App\Livewire\Admin\Loans;
use Livewire\Component;
use App\Models\Loan;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CollectList extends Component
{
    public $today;
    public $dayName;
    public $loans;
    public $showRescheduleModal = false;
    public $selectedLoanId;
    public $newPaymentDay;
    public $showFinalizeModal = false;
    public $selectedLoan;
    public $payFull = false;
    public $amount = 0;
    public $pagomensual = 0;
    public $deudaaldia = 0;
    public $newPaymentTime = '08:00';
    public $search = '';

    public function mount()
    {
        $this->today = Carbon::now();
        $this->dayName = 'jueves';//$this->today->locale('es')->dayName;

        $this->fetchLoans();
    }

    public function fetchLoans()
    {
        $query = Loan::with(['user', 'loanType', 'payments'])
        ->where('status', '!=', 'finalizado')
        ->where(function ($q) {
            $q->where('payment_date', $this->dayName)
              ->orWhere('payment_reschedule_for', $this->dayName);
        });

    if ($this->search) {
        $query->whereHas('user', function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%');
        });
    }

    $this->loans = $query
        ->orderBy('payment_type')
        ->get()
        ->map(function($loan) {
            $totalAbonado = $loan->payments->sum('amount') ?? 1;
            $pagosTotales = $loan->loanType->payments_total ?? 1;
            $pagoMensual = $loan->total_to_pay / $pagosTotales;
            $pagosRealizados = floor($totalAbonado / $pagoMensual);
            $deudaAlDia = $loan->total_to_pay - ($pagosRealizados * $pagoMensual);

            $loan->pagos_realizados = $pagosRealizados;
            $loan->pago_mensual = $pagoMensual;
            $loan->deuda_al_dia = $deudaAlDia;
            return $loan;
        });
        // $this->loans = Loan::with('user')->where('status', '!=', 'finalizado')
        //     ->where(function ($query) {
        //         $query->where('payment_date', $this->dayName)
        //               ->orWhere('payment_reschedule_for', $this->dayName);
        //     })      
        //     ->orderBy('payment_type')
        //     ->get()
        //     ->map(function ($loan) {
        //     $totalAbonado = $loan->payments->sum('amount') ?? 1;
        //     $pagosTotales = $loan->loanType->payments_total ?? 1;
        //     $pagoMensual = $loan->total_to_pay / $pagosTotales;
        //     $pagosRealizados = floor($totalAbonado / $pagoMensual);
        //     $deudaAlDia = $loan->total_to_pay - ($pagosRealizados * $pagoMensual);

        //     $loan->pagos_realizados = $pagosRealizados;
        //     $loan->pago_mensual = $pagoMensual;
        //     $loan->deuda_al_dia = $deudaAlDia;

        //     return $loan;
            
        // });
    }

    public function openRescheduleModal($loanId)
    {
        $this->selectedLoanId = $loanId;
        $loan = Loan::find($loanId);
        $this->newPaymentDay = $loan->payment_reschedule_for ?? $loan->payment_date;
        $this->newPaymentTime = $loan->payment_type ?? '08:00';
        $this->showRescheduleModal = true;
    }

    public function reschedulePayment()
    {
        $loan = Loan::find($this->selectedLoanId);
        $loan->payment_reschedule_for = ucwords($this->newPaymentDay);
        $loan->payment_type = $this->newPaymentTime;
        $loan->save();

        $this->showRescheduleModal = false;
        $this->fetchLoans(); // Actualiza la lista después del cambio
    }

    public function confirmFinalize($loanId)
    {
        
        $loan = Loan::with('loanType', 'payments')->findOrFail($loanId);

        $pagosRealizados = $loan->payments->count();
        //dd($pagosRealizados);
        $pagosTotales = $loan->loanType->payments_total ?? 1;

        $this->pagomensual = $loan->total_to_pay / $pagosTotales;
        $this->deudaaldia = $loan->total_to_pay - ($pagosRealizados * $this->pagomensual);

        $this->selectedLoan = $loanId;
        $this->payFull = false;
        $this->amount = round($this->pagomensual, 2); // precargar con pago mensual
        $this->showFinalizeModal = true;
    }
    public function updatedPayFull($value)
    {
        if ($value) {
            $this->amount = round($this->deudaaldia, 2);
        } else {
            $this->amount = round($this->pagomensual, 2);
        }
    }

    public function finalizeLoan()
    {
        $loan = Loan::with('loanType', 'payments')->findOrFail($this->selectedLoan);

        if ($this->amount <= 0) {
            $this->addError('amount', 'Debe ingresar un monto válido para el pago.');
            return;
        }

        // 1. Cálculos base
        $totalToPay = $loan->total_to_pay;
        $paymentsTotal = $loan->loanType->payments_total ?? 1;
        $installment = $totalToPay / $paymentsTotal;

        // 2. Cuántas cuotas enteras cubre el monto ingresado
        $numPayments = floor($this->amount / $installment);

        if ($numPayments < 1) {
            $this->addError('amount', 'El monto no alcanza para cubrir ni una cuota completa.');
            return;
        }

        // 3. Registar cada cuota como un pago individual
        for ($i = 0; $i < $numPayments; $i++) {
            Payment::create([
                'loan_id' => $loan->id,
                'payment_due' => now(),
                'collector' => Auth::id(),
                'amount' => $installment,
                'pay_full' => $this->payFull ? 1 : 0,
                'payment_date' => now(),
            ]);
        }

        // 4. Si el monto cubre todas las cuotas, marcar préstamo como finalizado
        if ($numPayments >= $paymentsTotal) {
            $loan->status = 'finalizado';
        }

        $loan->save();

        // 5. Resetar estado y re-cargar lista
        $this->reset(['showFinalizeModal', 'selectedLoan', 'payFull', 'amount']);
        $this->fetchLoans();

        session()->flash('success', "{$numPayments} pagos registrados exitosamente.");
    }

    
    public function updatedSearch($value)
    {
        $this->fetchLoans();
    }

    public function cleansearch(){
        $this->search='';
         $this->fetchLoans();
        $this->reset(['showFinalizeModal', 'selectedLoan', 'payFull', 'amount']);
    }
    public function render()
    {
        return view('livewire.admin.collect.collect-list');
    }
}
