<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Listado de Préstamos</h2>

    @if(session()->has('success'))
        <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    <!-- search -->
    <div class="mb-4">
        <div class="flex items-center gap-2">
        @if($search!='')
            <flux:button wire:click="cleansearch" icon="x-mark" variant="danger" />
        @endif
        <input
            type="text"
            wire:model.live="search"
            placeholder="Buscar por nombre del cliente..."
            class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        /></div>
    </div>

    <table class="w-full text-sm border text-center">
        <thead>
            <tr>
                <th class="p-2">Cliente</th>
                <th class="p-2">Monto</th>
                <th class="p-2">Interes</th>
                <th class="p-2">Total a Pagar</th>
                <th class="p-2">Pago requerido</th>
                <th class="p-2">Deuda al Dia</th>
                <th class="p-2">Tipo</th>
                <th class="p-2">Pagos</th>
                <th class="p-2">Pagos Indicador</th>
                <th class="p-2">Estado</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $loan)
                <tr class="border-b">
                    <td class="p-2">{{ $loan->user->name ?? 'N/A' }}</td>
                    <td class="p-2">$ {{ number_format($loan->amount, 2) }}</td>
                    <td class="p-2">$ {{ number_format($loan->interest, 2) }}</td>
                    <td class="p-2">$ {{ number_format($loan->total_to_pay, 2) }}</td>
                    @php
                        $pagosRealizados = $loan->payments->count();
                        $pagosTotales = $loan->loanType->payments_total ?? 1; // evitar división por cero
                        $progreso = min(100, round(($pagosRealizados / $pagosTotales) * 100));
                        $pagomensual = $loan->total_to_pay / $pagosTotales;
                        $deudaaldia = $loan->total_to_pay - ($pagosRealizados*$pagomensual)
                    @endphp

                    <td class="p-2">$ {{ number_format($pagomensual, 2) }}</td>
                    <td class="p-2">$ {{ number_format($deudaaldia, 2) }}</td>
                    <td class="p-2">{{ $loan->loanType->name ?? 'N/A' }}</td>
                    <td class="p-2">{{ $loan->payments->count() }} / {{ $loan->loanType->payments_total ?? 'N/A' }}</td>
                    <!-- Icono de pagos realizados  -->
                    

                    <td class="p-2 w-40 align-top">
                        <div class="text-sm font-medium mb-1 text-center">
                            {{ $pagosRealizados }} / {{ $pagosTotales }}
                            @if($progreso === 100)
                                <span class="ml-1 text-green-600">✅</span>
                            @endif
                        </div>

                        @if($progreso < 100)
                            <div class="w-full h-3 bg-gray-200 rounded">
                                <div
                                    class="h-3 rounded {{ 
                                        $progreso < 50 ? 'bg-yellow-400' : 'bg-blue-500'
                                    }}"
                                    style="width: {{ $progreso }}%"
                                ></div>
                            </div>
                        @endif
                    </td>
                     <!-- fin pagos realizados  -->
                    <td class="p-2 capitalize">{{ $loan->status }}</td>
                    <td class="p-2 space-x-2">
                    @if($loan->status === 'pendiente')
                        <flux:button wire:click="updateStatus({{ $loan->id }}, 'autorizado')" icon="check" tooltip="Autorizar"  />
                        <flux:button wire:click="updateStatus({{ $loan->id }}, 'cancelado')" icon="x-circle" tooltip="Cancelar" />
                        <flux:button variant="danger" wire:click="delete({{ $loan->id }}, 'eliminado')" icon="trash" tooltip="Eliminar" />
                    @endif   

                        <flux:button wire:click="confirmFinalize({{ $loan->id }})" icon="currency-dollar" tooltip="Pagar" />

                        <flux:button wire:click="showPayments({{ $loan->id }})" icon="queue-list" tooltip="Ver pagos" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <flux:modal wire:model.self="showFinalizeModal" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Abonar al Préstamo</flux:heading>           
                    <!-- <flux:text class="mt-2"><p>¿Desea registrar un pago total para este préstamo?</p></flux:text> -->               
                    <flux:text class="text-sm">
                        Pago mensual estimado: ${{ number_format($pagomensual, 2) }} | 
                        Deuda total: ${{ number_format($deudaaldia, 2) }}
                    </flux:text> 
            </div>

            <div class="flex items-center space-x-2">
                <flux:checkbox wire:model.live="payFull" />
                <flux:label>¿Pago total?</flux:label>
            </div>
        
            <div>
                <flux:input type="number" wire:model="amount" label="Cantidad abonada" />
                @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button wire:click="finalizeLoan" variant="primary">Aceptar</flux:button>
            </div>
        </div>
    </flux:modal>

@if($showPaymentsModal)
    <flux:modal wire:model.self="showPaymentsModal" class="p-8 min-w-[30rem]">
        <div class="space-y-4">
            <flux:heading size="lg" class="mb-4">Historial de Pagos</flux:heading>

            @if(count($selectedPayments) === 0)
                <flux:text>No hay pagos registrados para este préstamo.</flux:text>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">Fecha</th>
                                <th class="p-2 text-left">Monto</th>
                                <th class="p-2 text-left">Pago total</th>
                                <th class="p-2 text-left">Cobrador</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedPayments as $payment)
                                <tr class="border-t">
                                    <td class="p-2">{{ \Carbon\Carbon::parse($payment->payment_due)->format('d/m/Y') }}</td>
                                    <td class="p-2">${{ number_format($payment->amount, 2) }}</td>
                                    <td class="p-2">
                                        @if($payment->pay_full)
                                            ✅
                                        @else
                                            ❌
                                        @endif
                                    </td>
                                    <td class="p-2">
                                        {{ \App\Models\User::find($payment->collector)?->name ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="flex justify-end mt-4">
                <flux:modal.close>
                    <flux:button variant="ghost">Cerrar</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
@endif


</div>