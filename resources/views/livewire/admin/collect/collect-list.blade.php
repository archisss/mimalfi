<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Préstamos para 
        @if(Auth::user()->tipo_usuario == 0)     
            <select wire:model.live="selectedDay" id="selectedDay" style="background-color: gray;">
                <option style="background-color: gray-200;" value="Lunes" {{ $selectedDay == "Lunes" ? 'Selected' : '' }}>Lunes</option>
                <option value="Martes" {{ $selectedDay == "Martes" ? 'Selected' : '' }}>Martes</option>
                <option value="Miércoles" {{ $selectedDay == "Miércoles" ? 'Selected' : '' }}>Miércoles</option>
                <option value="Jueves" {{ $selectedDay == "Jueves" ? 'Selected' : '' }}>Jueves</option>
                <option value="Viernes" {{ $selectedDay == "Viernes" ? 'Selected' : '' }}>Viernes</option>
                <option value="Sábado" {{ $selectedDay == "Sábado" ? 'Selected' : '' }}>Sábado</option>
                <option value="Domingo" {{ $selectedDay == "Domingo" ? 'Selected' : '' }}>Domingo</option>
            </select>
        @else
            {{ ucfirst($selectedDay) }}
        @endif 
    </h2>

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
                <th class="p-2">Total Prestado</th>
                <th class="p-2">Pago requerido</th>
                <th class="p-2">Deuda al Dia</th>
                <th class="p-2">Pagos realizados</th>
                <th class="p-2">Fecha de Pago</th>
                <th class="p-2">Hora de Pago</th>
                <th class="p-2">Reprogramado Para</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $loan)
                <tr class="border-b">
                    <td class="p-2">{{ $loan->user->name ?? 'N/A' }}</td>
                    <td class="p-2">$ {{ number_format($loan->total_to_pay, 2) }}</td>
                                  @php
                        $pagosRealizados = $loan->payments->count();
                        $pagosTotales = $loan->loanType->payments_total ?? 1; // evitar división por cero
                        $progreso = min(100, round(($pagosRealizados / $pagosTotales) * 100));
                        $pagomensual = $loan->total_to_pay / $pagosTotales;
                        $deudaaldia = $loan->total_to_pay - ($pagosRealizados*$pagomensual)
                    @endphp

                    <td class="p-2">$ {{ number_format($pagomensual, 2) }}</td>
                    <td class="p-2">$ {{ number_format($deudaaldia, 2) }} </td>
                    <td class="p-2"> {{ $pagosRealizados .' / '.$pagosTotales }} </td>
                    <td class="p-2">{{ $loan->payment_date }}</td>
                    <td class="p-2">{{ \Carbon\Carbon::parse($loan->payment_time)->format('H:i') }}</td>
                    <td class="p-2">{{ $loan->payment_reschedule_for ?? 'N/A' }}</td>
                    <!-- <td class="p-2 space-x-2">
                        <button wire:click="$emit('openModal', 'admin.collect.reschedule-loan', {{ json_encode(['loanId' => $loan->id]) }})" class="text-blue-500 hover:underline">Mover</button>
                        <button wire:click="$emit('openModal', 'admin.collect.pay-loan', {{ json_encode(['loanId' => $loan->id]) }})" class="text-green-500 hover:underline">Pagar</button>
                    </td> -->
                    <td class="p-2 space-x-2">
                        <flux:button wire:click="openRescheduleModal({{ $loan->id }})" icon="calendar" tooltip="Mover" />
                        <flux:button wire:click="confirmFinalize({{ $loan->id }})" icon="currency-dollar" tooltip="Pagar" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-2 text-center">No hay préstamos programados para hoy.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <flux:modal wire:model.self="showRescheduleModal" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Reprogramar Pago</flux:heading>
                </br>
                <flux:select wire:model="newPaymentDay" label="Nuevo día de pago">
                    <option value="">Seleccione un día</option>
                    <option value="lunes">Lunes</option>
                    <option value="martes">Martes</option>
                    <option value="miércoles">Miércoles</option>
                    <option value="jueves">Jueves</option>
                    <option value="viernes">Viernes</option>
                </flux:select>
                </br>
                <flux:input type="time" wire:model="newPaymentTime" label="Nueva hora de pago" />
            </div>
            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button wire:click="reschedulePayment" variant="primary">Guardar</flux:button>
            </div>
        </div>
    </flux:modal>


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

</div>

