<div class="p-6">
    <!-- Contenedor único, todo está dentro de este div -->
    <div>
        <h2 class="text-xl font-bold mb-4">Crear Préstamo</h2>

        @if(session()->has('success'))
            <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="grid gap-4 max-w-2xl">
            <!-- Alias -->
            <flux:input wire:model="user_id" label="User Id" disabled />

            <!-- Cliente -->
            <flux:select wire:model="user_id" placeholder="Seleccione" label="Nombre Cliente">
                @foreach($clients as $client)
                    <flux:select.option value="{{ $client->id }}">
                        {{ $client->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>
                
            <!-- Alias -->
            <flux:input wire:model="alias" label="Alias" />

            <!-- Tipo de Préstamo -->
            <flux:select wire:model.live="loan_type_id" placeholder="Seleccione Tipo de Prestamo" label="Tipo de Préstamo">
                @foreach($loanTypes as $type)
                    <flux:select.option wire:key="loan_type-{{ $type->id }}" value="{{ $type->id }}">
                        {{ $type->name }} ({{ $type->porcentage }}%)
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex gap-4 items-end">
                <!-- Monto -->
                <flux:input wire:model.live="amount" label="Monto" mask="9999999"/>

                <!-- Interés Calculado -->
                <flux:input
                    label="Interés"
                    :value="number_format(($amount * optional($loanTypes->firstWhere('id', $loan_type_id))->porcentage ?? 0) / 100, 2)"
                    readonly
                />

                <!-- Total a Pagar (Monto + Interés) -->
                <flux:input
                    label="Total a Pagar"
                    :value="number_format($amount + (($amount * optional($loanTypes->firstWhere('id', $loan_type_id))->porcentage ?? 0) / 100), 2)"
                    readonly
                />
            </div>


            <!-- Día de pago -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Día de pago</label>
                <div class="flex space-x-4">
                    @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'] as $dia)
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="payment_date" value="{{ $dia }}" class="form-radio h-5 w-5 text-blue-600">
                            <span class="ml-4" style="margin-left:10px; margin-right: 20px;">{{ $dia }}</span>
                        </label>
                    @endforeach
                </div>
                @error('payment_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Día de pago -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Tipo de Pago</label>
                <div class="flex space-x-4">
                    @foreach(['Efectivo', 'Digital'] as $tipodepago)
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="payment_type" value="{{ $tipodepago }}" class="form-radio h-5 w-5 text-blue-600">
                            <span class="ml-4" style="margin-left:10px; margin-right: 20px;">{{ $tipodepago }}</span>
                        </label>
                    @endforeach
                </div>
                @error('payment_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Fecha de vencimiento -->
            <x-input label="Fecha de vencimiento" wire:model="term" type="date" />

            <!-- Cobrador -->
            <flux:select wire:model.live="collector" placeholder="Asigne al Cobrador" label="Cobrador">
                @foreach($collectors as $collector)
                    <flux:select.option wire:key="collector-{{ $collector->id }}" value="{{ $collector->id }}">
                        {{ $collector->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <!-- Usa banco -->
            <flux:field variant="inline">
                <flux:checkbox wire:model.live="use_bank" :disabled="$disable_bank"/>
                <flux:label>¿Usa efectivo de Cobradores?</flux:label>
                <flux:error name="use_bank" />
            </flux:field>
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div type="danger" message="{{ session('error') }}" ></div>

            <!-- Prestamista -->
            <x-input label="ID del Prestamista (opcional)" wire:model="use_lender" type="number" />

            <!-- Botón Guardar -->
            <div>
                <x-button type="submit" variant="primary">Guardar</x-button>
            </div>
        </form>
        @if ($errors->any())
    <div class="bg-red-100 text-red-800 p-2 mb-4 rounded">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    </div>
</div>