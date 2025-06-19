<?php

namespace App\Livewire\Admin\Loans;

use Livewire\Component;
use Flux\Flux;
use App\Models\LoanType;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoanList extends Component
{
    public $loanTypes;
    public $showFinalizeModal = false;
    public $selectedLoan;
    public $payFull = false;
    public $amount = 0;
    public $showPaymentsModal = false;
    public $selectedPayments = [];
    public $search = '';
    public $pagomensual = 0;
    public $deudaaldia = 0;

    public function mount()
    {
        $this->loanTypes = LoanType::all();
    }

    public function confirmFinalize($loanId)
    {
        $loan = Loan::with('loanType', 'payments')->findOrFail($loanId);

        $pagosRealizados = $loan->payments->count();
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
        $loan = Loan::findOrFail($this->selectedLoan);

        if ($this->amount <= 0) {
            $this->addError('amount', 'Debe ingresar un monto válido para pago total.');
            return;
        }

        // Crear el pago
        Payment::create([
            'loan_id' => $loan->id,
            'payment_due' => now(),
            'collector' => Auth::id(),
            'amount' => $this->amount,
            'pay_full' => $this->payFull ? 1 : 0,
            'payment_date' => now(),
        ]);

        // Actualizar estado del préstamo
         $loan->status = $this->payFull == true ? 'finalizado' : 'activo';
        $loan->save();
        Flux::toast('Pago registrado con exito');
        $this->reset(['showFinalizeModal', 'selectedLoan', 'payFull', 'amount']);
        //$this->fetchLoans();
        //session()->flash('success', 'Préstamo finalizado y pago registrado.');
        
    }

    public function cancelModal()
    {
        $this->reset(['showFinalizeModal', 'selectedLoan', 'payFull', 'amount']);
    }
    public function delete($id)
    {
        Loan::findOrFail($id)->delete();
        session()->flash('success', 'Préstamo eliminado.');
        $this->reset(['showFinalizeModal', 'selectedLoan', 'payFull', 'amount']);
        //$this->mount();
    }

    public function updateStatus($id, $status)
    {
        $loan = Loan::findOrFail($id);
        $loan->status = $status;
        $loan->save();

        session()->flash('success', "Préstamo marcado como '$status'.");
        $this->reset(['showFinalizeModal', 'selectedLoan', 'payFull', 'amount']);
        //$this->mount();
    }

    public function showPayments($loanId)
    {
        $this->selectedPayments = Payment::where('loan_id', $loanId)->get();
        $this->showPaymentsModal = true;
    }

    public function cleansearch(){
        $this->search='';
        $this->reset(['showFinalizeModal', 'selectedLoan', 'payFull', 'amount']);
    }

    public function render()
    {
        $loans = Loan::with('user')
        ->when($this->search, function ($query) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        })
        ->latest()
        ->get();

        return view('livewire.admin.loans.loan-list', [
            'loans' => $loans,
        ]);
    }
}

