<?php

namespace App\Livewire\Admin\Loans;

use App\Models\LoanType;
use Livewire\Component;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;

//use GuzzleHttp\Psr7\Request;

class Create extends Component
{
    public $amount, $loan_type, $payment_date,$use_lender, $term;
    public $collector = '';
    public $user_id = '' ;
    public $loan_type_id = '';
    public $use_bank = false;
    
    public function mount(Request $request)
    {
        if($request->query('user_id') !== null){
            $this->user_id = $request->query('user_id');
        }   
    }
    public function save()
    {
        $this->validate(rules: [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'loan_type_id' => 'required|exists:loan_types,id',
            'payment_date' => 'required|string',
            'term' => 'required|date',
            'collector' => 'nullable|exists:users,id'
        ]);

        $loanType = LoanType::find($this->loan_type_id);
        $interest = ($this->amount * ($loanType->porcentage ?? 0)) / 100;
        $totalToPay = $this->amount + $interest;

        Loan::create([
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'loan_type_id' => $this->loan_type_id,
            'interest' => $interest,
            'total_to_pay' => $totalToPay,
            'payment_date' => $this->payment_date,
            'term' => $this->term,
            'use_bank' => $this->use_bank,
            'use_lender' => $this->use_lender,
            'collector' => $this->collector,
            'status' => 'pendiente'
        ]);

        session()->flash('success', 'Préstamo creado exitosamente.');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.admin.loans.create', [
            'clients' => User::where('user_type', 2)->get(),
            'collectors' => User::where('user_type', 1)->get(),
            'loanTypes' => LoanType::all(),
        ]);
    }
}
