<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Cobradores Activos</h2>

    <table class="w-full text-sm border text-center">
        <thead>
            <tr>
                <th class="p-2">Nombre</th>
                <th class="p-2">Cantidad en Caja del Día</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($collectors as $collector)
                <tr class="border-b">
                    <td class="p-2">{{ $collector->name }}</td>
                    <td class="p-2">${{ number_format($this->calculateCobrosDelDia($collector->id), 2) }}</td>
                    <td class="p-2 space-x-2">
                        <flux:button wire:click="openCajaModal({{ $collector->id }})" icon="wallet" tooltip="Caja" />
                        <flux:button wire:click="openChangeRouteModal({{ $collector->id }})" icon="arrows-right-left" tooltip="Cambiar Ruta" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal para Caja -->
    <flux:modal wire:model.self="showCajaModal" class="min-w-[22rem]">
        <div class="space-y-6">
            <flux:heading size="lg">Caja del Día</flux:heading>
            <flux:input type="number" wire:model="amountToCaja" label="Cantidad para dejar en caja" />
            @error('amountToCaja') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <div class="flex justify-end mt-4 gap-2">
                <flux:modal.close><flux:button variant="ghost">Cancelar</flux:button></flux:modal.close>
                <flux:button wire:click="leaveInCaja" variant="primary">Guardar</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal para Cambiar Ruta -->
    <flux:modal wire:model.self="showChangeRouteModal" class="min-w-[22rem]">
        <div class="space-y-6">
            <flux:heading size="lg">Cambiar Ruta</flux:heading>

            <flux:select wire:model="newCollectorId" label="Seleccione Nuevo Cobrador">
                <flux:select.option value="">Seleccione</flux:select.option>
                @foreach($collectorOptions as $opt)
                    <flux:select.option value="{{ $opt->id }}">{{ $opt->name }}</flux:select.option>
                @endforeach
            </flux:select>
            @error('newCollectorId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <div class="flex justify-end mt-4 gap-2">
                <flux:modal.close><flux:button variant="ghost">Cancelar</flux:button></flux:modal.close>
                <flux:button wire:click="changeRouteTo" variant="primary">Actualizar</flux:button>
            </div>
        </div>
    </flux:modal>
</div>

