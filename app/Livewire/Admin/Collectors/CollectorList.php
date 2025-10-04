<?php

namespace App\Livewire\Admin\Collectors;

use Livewire\Component;
use App\Models\User;
use App\Models\Loan;
use App\Models\SelfBank;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class CollectorList extends Component
{
    public $collectors;  // lista de cobradores activos
    public $showCajaModal = false;
    public $showChangeRouteModal = false;
    public $newCollectorId;

    public $selectedCollectorId;
    public $cobrosDelDia = 0;
    public $amountToCaja;

    public $collectorOptions = []; // para “cambiar ruta”

    public function mount()
    {
        $this->loadCollectors();
    }

    public function loadCollectors()
    {
        // Supongamos que “activo” significa `user_type = 1` y algún flag como `active = true`
        $this->collectors = User::where('user_type', 1)
            ->where('status', 'activo')
            ->get();
    }

    public function openCajaModal($collectorId)
    {
        $this->selectedCollectorId = $collectorId;
        $this->cobrosDelDia = $this->calculateCobrosDelDia($collectorId);
        $this->showCajaModal = true;
    }

    protected function calculateCobrosDelDia($collectorId)
    {        
        $selfBank = SelfBank::where('user_id' , $collectorId)->sum('amount');
        return $selfBank;
        
        // = Loan::where('loans.collector', $collectorId)
        //     ->join('payments', 'loans.id', '=', 'payments.loan_id')
        //     ->whereDate('payments.payment_date', now())
        //     ->sum('payments.amount');
        
        // $totalExpenses = Expense::where('user_id', $collectorId)
        //     ->whereDate('expense_date', now())
        //     ->sum('amount');

        // return ($totalPayments ?? 0) - ($totalExpenses ?? 0);
    }

    public function leaveInCaja()
    {
        // Validar input
        $this->validate([
            'amountToCaja' => 'required|numeric|min:0|max:2500',
        ]);

         SelfBank::create([
                'user_id' => $this->selectedCollectorId,
                'description' => 'Inicio Caja ' . now()->format('d/m/Y') . ' de ' . $this->amountToCaja . ' pesos',
                'amount' => $this->amountToCaja,
                'bank_date' => now()
            ]);

        $this->showCajaModal = false;
        session()->flash('success', 'Monto guardado en caja.');
    }

    public function openChangeRouteModal($collectorId)
    {
        $this->selectedCollectorId = $collectorId;
        $this->collectorOptions = User::where('user_type', 1)->where('status', 'activo')->get();
        $this->showChangeRouteModal = true;
    }

    public function changeRouteTo()
    {
        if ($this->selectedCollectorId == $this->newCollectorId) {
            $this->addError('newCollectorId', 'Seleccione un cobrador distinto.');
            return;
        }

        Loan::where('collector', $this->selectedCollectorId)
            ->where('status', '!=', 'finalizado')
            ->update(['collector' => $this->newCollectorId]);

        $this->showChangeRouteModal = false;
        session()->flash('success', 'Ruta cambiada correctamente.');
    }

    public function render()
    {
        return view('livewire.admin.collectors.collector-list');
    }
}
