<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Crear Tipo de Préstamo</h2>

    @if(session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="grid gap-4 max-w-md">
        <x-input label="Nombre" wire:model="name" />
        <x-input label="Días entre pagos" wire:model="calendar_days" type="number" />
        <x-input label="Total de pagos" wire:model="payments_total" type="number" />
        <x-input label="Porcentaje de interés" wire:model="porcentage" type="number" step="0.01" />

        <x-button type="submit">Guardar</x-button>
    </form>
</div>
