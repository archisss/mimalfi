<?php

namespace App\Livewire\Admin;

use App\Models\Loan;
use App\Models\Payment;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public $loansPerDay;
    public $pendingLoans;
    public $paymentsToday;

    public function mount()
    {
        $this->loansPerDay = Loan::selectRaw('DAYNAME(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day');

        $this->pendingLoans = Loan::where('status', 'pendiente')->count();

        $this->paymentsToday = Payment::whereDate('payment_due', Carbon::today())->sum('amount');//Payment::whereDate('created_at', Carbon::today())->sum('payment_due');
    }

    // public function render()
    // {
    //     return view('livewire.dashboard');
    // }
    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
