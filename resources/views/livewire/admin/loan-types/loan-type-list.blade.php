<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Listado de Tipos de Préstamo</h2>

    @if(session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full text-sm border text-center">
        <thead>
            <tr>
                <th class="p-2">Nombre</th>
                <th class="p-2">Días</th>
                <th class="p-2">Pagos</th>
                <th class="p-2">%</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loanTypes as $type)
                <tr class="border-t">
                    <td class="p-2">{{ $type->name }}</td>
                    <td class="p-2">{{ $type->calendar_days }}</td>
                    <td class="p-2">{{ $type->payments_total }}</td>
                    <td class="p-2">{{ $type->porcentage }}%</td>
                    <td class="p-2">
                        <flux:button wire:click="edit({{ $type->id }})" icon="pencil" tooltip="Editar" />
                        <flux:button wire:click="delete({{ $type->id }})" icon="trash" tooltip="Eliminar" variant="danger" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($showEditModal)
    <flux:modal wire:model.self="showEditModal" class="min-w-[30rem]">
        <div class="space-y-4">
            <flux:heading size="lg">Editar Tipo de Préstamo</flux:heading>

            <div class="space-y-3">
                <flux:input wire:model="editName" label="Nombre" />
                <flux:input wire:model="editCalendarDays" label="Días entre pagos" type="number" />
                <flux:input wire:model="editPaymentsTotal" label="Total de pagos" type="number" />
                <flux:input wire:model="editPorcentage" label="Porcentaje de interés" type="number" step="0.01" />
            </div>

            <div class="flex justify-end space-x-2 mt-4">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button wire:click="update" variant="primary">Guardar</flux:button>
            </div>
        </div>
    </flux:modal>
    @endif




</div>

