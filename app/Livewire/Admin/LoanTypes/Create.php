<?php

namespace App\Livewire\Admin\LoanTypes;

use Livewire\Component;
use App\Models\LoanType;

class Create extends Component
{
    public $name, $calendar_days, $payments_total, $porcentage;

    public function save()
    {
        $this->validate([
            'name' => 'required|string',
            'calendar_days' => 'required|integer|min:1',
            'payments_total' => 'required|integer|min:1',
            'porcentage' => 'required|numeric|min:0',
        ]);

        LoanType::create([
            'name' => $this->name,
            'calendar_days' => $this->calendar_days,
            'payments_total' => $this->payments_total,
            'porcentage' => $this->porcentage,
        ]);

        session()->flash('success', 'Tipo de préstamo creado.');
        $this->reset();
    }
    
    public function render()
    {
        return view('livewire.admin.loan-types.create');
    }
}
