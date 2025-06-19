<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExpenseList extends Component
{
    use WithFileUploads;

    public $expenses;
    public $selectedDate;
    public $description;
    public $amount;
    public $picture;
    public $bank, $bank2;

    public function mount()
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
        $this->loadExpenses();
    }

    public function loadExpenses()
    {
        $this->expenses = Expense::whereDate('expense_date', $this->selectedDate)->get();
        $this->bank = Expense::whereDate('expense_date', $this->selectedDate)->sum('amount');
        $this->bank2 = Payment::whereDate('payment_due', $this->selectedDate)
        ->where('collector', Auth::id())
        ->sum('amount');
    }

    public function updatedSelectedDate()
    {
        $this->loadExpenses();
    }

    public function saveExpense()
    {
        $this->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'picture' => 'nullable|image|max:1024',
        ]);

        $path = null;
        if ($this->picture) {
            $userId = Auth::id();
            $date = Carbon::parse($this->selectedDate)->format('Y-m-d');
            $descriptionSlug = Str::slug($this->description);
            $extension = $this->picture->getClientOriginalExtension();
            $filename = "{$userId}_{$descriptionSlug}.{$extension}";

            // Ruta: expenses/{user_id}/{YYYY-MM-DD}/filename.ext
            $storagePath = "expenses/{$userId}/{$date}";
            $path = $this->picture->storeAs($storagePath, $filename, 'public');
        }

        Expense::create([
            'user_id'     => Auth::id(),
            'description' => $this->description,
            'amount'      => $this->amount,
            'picture'     => $path,
            'expense_date'=> Carbon::parse($this->selectedDate)->format('Y-m-d'),
        ]);

        $this->reset(['description', 'amount', 'picture']);
        $this->loadExpenses();
        session()->flash('success', 'Gasto registrado exitosamente.');
    }

    public function render()
    {
        return view('livewire.expense-list');
    }
}
