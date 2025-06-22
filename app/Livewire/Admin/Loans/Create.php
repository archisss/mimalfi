<?php

namespace App\Livewire\Admin\Loans;

use App\Models\Expense;
use App\Models\LoanType;
use Livewire\Component;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Payment;
use PHPUnit\Runner\DeprecationCollector\Collector;

class Create extends Component
{
    public $alias, $amount, $loan_type, $payment_date,$payment_type,$use_lender, $term;
    public $collector = null;
    public $user_id = '' ;
    public $loan_type_id = '';
    public bool $use_bank = false;
    public bool $disable_bank = true;
    public $payment_time = '08:00:00';
    //public $collectors;
    
    public function mount(Request $request)
    {
        if($request->query('user_id') !== null){
            $this->user_id = $request->query('user_id');
        }   
        $this->collector = '';
        //$this->collectors = User::where('user_type', 1)->get();
    }
    public function canUseBank()
    {
        //dd(Carbon::now()->format('Y-m-d'));
        $in_bank = User::withSum(['payments as total' => function ($query) {
            $query->where('payment_due', Carbon::now()->format('Y-m-d'));
        }], 'amount')->find($this->collector);
        $total_pagar = $this->calcularTotalPagar();

        $can_use_bank = $in_bank->total >= $total_pagar ? true : false;
        return $can_use_bank;
    }

    public function updatedCollector(){
        $this->disable_bank = $this->canUseBank() == true ? false : true;
    }

    public function updatedAmount()
    {
        $this->collector = '';
        $this->disable_bank = true;
    }

    public function updatedLoanTypeId($value){
        $LoanTypeInfo = LoanType::find($value);
         if ($LoanTypeInfo) {
            $totalDeDias = $LoanTypeInfo->calendar_days * $LoanTypeInfo->payments_total;
            $this->term = Carbon::now()->addDays($totalDeDias)->toDateString();
        }
    }

    public function calcularTotalPagar(): float
    {
        $loanType = LoanType::find($this->loan_type_id);
        $interest = ($this->amount * ($loanType->porcentage ?? 0)) / 100;
        return $this->amount + $interest;
    }


    public function save()
    {
        $this->validate(rules: [
            'user_id' => 'required|exists:users,id',
            'alias'  => 'string',
            'amount' => 'required|numeric|min:1',
            'loan_type_id' => 'required|exists:loan_types,id',
            'payment_date' => 'required|string',
            'payment_type' => 'required|string',
            'payment_time' => 'required',
            'term' => 'required|date',
            'collector' => 'nullable|exists:users,id'
        ]);

        // $loanType = LoanType::find($this->loan_type_id);
        // $interest = ($this->amount * ($loanType->porcentage ?? 0)) / 100;
        // $totalToPay = $this->amount + $interest;
        $totalToPay = $this->calcularTotalPagar();
        //$loanType = LoanType::find($this->loan_type_id);
        $interest = $totalToPay - $this->amount;


        $loan = Loan::create([
            'user_id' => $this->user_id,
            'alias'  => $this->alias,
            'amount' => $this->amount,
            'loan_type_id' => $this->loan_type_id,
            'interest' => $interest,
            'total_to_pay' => $totalToPay,
            'payment_date' => $this->payment_date,
            'payment_type' => 'Efectivo',
            'payment_time' => '08:00:00',
            'term' => $this->term,
            'use_bank' => $this->use_bank,
            'use_lender' => $this->use_lender,
            'collector' => $this->collector,
            'status' => 'pendiente'
        ]);

        $loanId = $loan->id;

        if($this->use_bank == true){
            Expense::create([
                'user_id'     => $this->collector,
                'type'        => 'Loan',
                'description' => 'Loan '.$loanId.' to user ' .$this->user_id. ' for $' .$totalToPay,
                'amount'      => $totalToPay,
                'expense_date'=> Carbon::now()->format('Y-m-d'),
            ]);
        }

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
