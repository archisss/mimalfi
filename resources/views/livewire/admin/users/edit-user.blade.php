<div class="p-6 max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Editar Usuario</h2>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="update" class="space-y-4 gap-4">
        <flux:input label="Nombre" wire:model="name" />
        <flux:input label="Email" wire:model="email" type="email" />
        
        <flux:select wire:model="user_type" label="Tipo de Usuario">
            <flux:select.option value="1">Cobrador</flux:select.option>
            <flux:select.option value="2">Cliente</flux:select.option>
        </flux:select>

        <flux:input label="Celular" mask="9999999999" wire:model="cellphone" />

        <flux:input label="Teléfono" mask="9999999999" wire:model="phone" />

        <flux:input label="Dirección de Trabajo" wire:model="work_address" />
        <flux:input label="Dirección de Cobro" wire:model="payment_address" />

        <flux:button type="submit" variant="primary" icon="check-circle" tooltip="Guardar cambios" class="mt-4">
            Actualizar Usuario
        </flux:button>
    </form>
</div>

