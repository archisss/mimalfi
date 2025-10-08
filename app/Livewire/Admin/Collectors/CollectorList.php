<?php

namespace App\Livewire\Admin\Collectors;

use Livewire\Component;
use App\Models\User;
use App\Models\Loan;
use App\Models\SelfBank;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class CollectorList extends Component
{
    public $collectors;  // lista de cobradores activos
    public $showCajaModal = false;
    public $showChangeRouteModal = false;
    public $newCollectorId;
    public $selectedCollectorId;
    public $cajaDelDia = 0;
    public $amountToCaja;
    public $collectorOptions = []; // para “cambiar ruta”

    public function mount()
    {
        $this->loadCollectors();
    }

    public function loadCollectors()
    {
        $today = Carbon::today();

        $this->collectors = User::where('user_type', 1)
            ->where('status', 'activo')
            ->get()
            ->map(function ($collector) use ($today) {
                $collector->payments_today = Payment::whereDate('payment_due', $today)
                    ->where('collector', $collector->id)
                    ->sum('amount');

                $collector->expenses_today = Expense::whereDate('expense_date', $today)
                    ->where('user_id', $collector->id)
                    ->sum('amount');

                $collector->loans_today = Expense::whereDate('expense_date', $today)
                    ->where('user_id', $collector->id)
                    ->where('type', 'Loan')
                    ->sum('amount');

                $collector->self_bank_today = SelfBank::whereDate('bank_date', $today)
                    ->where('user_id', $collector->id)
                    ->sum('amount');

                return $collector;
            });
            //dd($this->collectors);
    }

    public function openCajaModal($collectorId)
    {
        $this->selectedCollectorId = $collectorId;
        $this->cajaDelDia = $this->calculateCajaDelDia($collectorId);
        $this->showCajaModal = true;
    }

    protected function calculateCajaDelDia($collectorId)
    {        
        $selfBank = SelfBank::where('user_id' , $collectorId)->sum('amount');
        return $selfBank;
    }

    public function leaveInCaja()
    {
        $this->validate([
            'amountToCaja' => 'required|numeric|min:0|max:2500',
        ]);

        SelfBank::create([
            'user_id' => $this->selectedCollectorId,
            'description' => 'Inicio Caja ' . now()->format('d/m/Y') . ' de ' . $this->amountToCaja . ' pesos',
            'amount' => $this->amountToCaja,
            'bank_date' => Carbon::now()
        ]);

        $this->closeCajaModal();
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
    public function closeCajaModal()
    {
        $this->reset(['showCajaModal', 'selectedCollectorId', 'cajaDelDia', 'amountToCaja', 'showChangeRouteModal', 'newCollectorId']);
        $this->loadCollectors(); // Recarga los datos
    }

    public function updatedShowCajaModal($value)
    {
        if (!$value) {
            $this->reset(['selectedCollectorId', 'cajaDelDia', 'amountToCaja']);
            $this->loadCollectors(); // Recargar la lista actualizada
        }
    }

    public function updatedShowChangeRouteModal($value)
    {
        if (!$value) {
            $this->reset(['selectedCollectorId', 'newCollectorId']);
            $this->loadCollectors();
        }
    }

    public function render()
    {
        return view('livewire.admin.collectors.collector-list');
    }
}
