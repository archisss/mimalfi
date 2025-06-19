<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Gestión de Gastos</h2>         
    

    @if(session()->has('success'))
        <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
<div class="flex gap-8 items-end">
    <flux:badge class="mb-4" size="lg" color="red">Gastos del Dia: $ {{ number_format($bank, 2) }}</flux:badge>
    <flux:badge class="mb-4" size="lg" color="green">Cobros del Dia: $ {{ number_format($bank2, 2) }}</flux:badge>
    <!-- Selección de fecha -->
    <div class="mb-4">
        <!-- <label for="selectedDate" class="block text-sm font-medium text-gray-700">Seleccionar Fecha:</label> -->
        <flux:input type="date" wire:model.live="selectedDate" label="Seleccionar Fecha:" />
        <!-- <input type="date" id="selectedDate" wire:model.live="selectedDate" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"> -->
    </div>
</div>
    <!-- Lista de gastos -->
    <div class="mb-4">
        <h3 class="text-lg font-semibold mb-2">Gastos del {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</h3>

        <table class="w-full text-sm border">
            <thead>
                <tr>
                    <th class="p-2">Descripción</th>
                    <th class="p-2">Monto</th>
                    <th class="p-2">Imagen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr class="border-b">
                        <td class="p-2">{{ $expense->description }}</td>
                        <td class="p-2">$ {{ number_format($expense->amount, 2) }}</td>
                        <td class="p-2">
                            @if($expense->picture)
                                <a href="{{ Storage::url($expense->picture) }}" target="_blank">
                                    <img src="{{ Storage::url($expense->picture) }}"
                                        alt="Imagen"
                                        class="h-10 w-10 object-cover"
                                    />
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-2 text-center">No hay gastos registrados para esta fecha.</td>
                    </tr>
                @endforelse
                <tr class="border-b">
                        <td class="p-2">Total :</td>
                        <td class="p-2"><b>$ {{ number_format($bank, 2) }}</b></td>
                        <td class="p-2"></td>
                        <td class="p-2"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Formulario para nuevo gasto -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Registrar Nuevo Gasto</h3>
        <form wire:submit.prevent="saveExpense">
            <div class="mb-4">
                <flux:input type="text" wire:model="description" label="Descripcion" />
                <!-- <label for="description" class="block text-sm font-medium text-gray-700">Descripción:</label>
                <input type="text" id="description" wire:model="description" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"> -->
                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <flux:input type="text" wire:model="amount" label="Cantidad" />
                <!-- <label for="amount" class="block text-sm font-medium text-gray-700">Monto:</label>
                <input type="number" id="amount" wire:model="amount" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"> -->
                @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="picture" class="block text-sm font-medium text-white-700 mb-2">Imagen (opcional):</label>
                <input type="file" id="picture" wire:model="picture" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                @error('picture') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar Gasto</button>
        </form>
    </div>
</div>
