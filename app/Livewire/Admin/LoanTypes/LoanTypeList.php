<?php

namespace App\Livewire\Admin\LoanTypes;

use Livewire\Component;
use App\Models\LoanType;

class LoanTypeList extends Component
{
    public $loanTypes;
    public $showEditModal = false;
    public $editId;
    public $editName;
    public $editCalendarDays;
    public $editPaymentsTotal;
    public $editPorcentage;

    public function mount()
    {
        $this->fetch();
    }

    public function fetch()
    {
        $this->loanTypes = LoanType::all();
    }

    public function edit($id)
    {
        $type = \App\Models\LoanType::findOrFail($id);
        $this->editId = $type->id;
        $this->editName = $type->name;
        $this->editCalendarDays = $type->calendar_days;
        $this->editPaymentsTotal = $type->payments_total;
        $this->editPorcentage = $type->porcentage;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'editName' => 'required|string',
            'editCalendarDays' => 'required|integer|min:1',
            'editPaymentsTotal' => 'required|integer|min:1',
            'editPorcentage' => 'required|numeric|min:0',
        ]);

        $type = \App\Models\LoanType::findOrFail($this->editId);
        $type->update([
            'name' => $this->editName,
            'calendar_days' => $this->editCalendarDays,
            'payments_total' => $this->editPaymentsTotal,
            'porcentage' => $this->editPorcentage,
        ]);

        $this->reset(['showEditModal', 'editId', 'editName', 'editCalendarDays', 'editPaymentsTotal', 'editPorcentage']);
        $this->fetch();
        session()->flash('success', 'Tipo de préstamo actualizado.');
    }

    public function delete($id)
    {
        LoanType::findOrFail($id)->delete();
        session()->flash('success', 'Tipo de préstamo eliminado.');
        $this->fetch();
    }
    public function render()
    {
        return view('livewire.admin.loan-types.loan-type-list');
    }
}
