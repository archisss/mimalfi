<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;

class Loans extends Component
{
    public $loans;
    public $amount, $loan_type, $payment_date, $term, $use_bank = false;
    public $use_lender, $collector, $user_id;

    public function mount()
    {
        $this->fetchLoans();
    }

    public function fetchLoans()
    {
        $this->loans = Loan::with('user')->latest()->get();
    }

    public function save()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'loan_type' => 'required|string',
            'payment_date' => 'required|string',
            'term' => 'required|date',
            'collector' => 'nullable|exists:users,id'
        ]);

        Loan::create([
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'loan_type' => $this->loan_type,
            'payment_date' => $this->payment_date,
            'term' => $this->term,
            'use_bank' => $this->use_bank,
            'use_lender' => $this->use_lender,
            'collector' => $this->collector,
            'status' => 'activo'
        ]);

        session()->flash('success', 'Préstamo creado exitosamente.');
        $this->reset(['amount', 'loan_type', 'payment_date', 'term', 'use_bank', 'use_lender', 'collector', 'user_id']);
        $this->fetchLoans();
    }
    public function render()
    {
        return view('livewire.admin.loans', [
            'clients' => User::where('user_type', 2)->get(),
            'collectors' => User::where('user_type', 1)->get()
        ]);
    }
}
