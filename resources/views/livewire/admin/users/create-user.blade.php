    <div class="p-6">
        <h2 class="text-xl font-bold mb-4">Registrar Usuario</h2>

        @if(session()->has('success'))
            <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="grid gap-4 max-w-2xl" enctype="multipart/form-data">
            <!-- Nombre -->
            <flux:input wire:model="name" label="Nombre" />

            <!-- Email -->
            <flux:input wire:model="phone" label="Celular" mask="(999) 999-9999" />

            <!-- Email -->
            <flux:input wire:model="email" label="Correo Electrónico" type="email" />

            <!-- Contraseña -->
            <flux:input wire:model="password" label="Contraseña" type="password" />

            <!-- Tipo de Usuario -->
            <flux:select wire:model.live="user_type" placeholder="Seleccione Tipo de Usuario" label="Tipo de Usuario">
                <flux:select.option value="2">Cliente</flux:select.option>
                <flux:select.option value="1">Cobrador</flux:select.option>
                <flux:select.option value="0">Administrador</flux:select.option>
            </flux:select>
            <!-- Campos adicionales para user_type == 2 -->
            @if($user_type == 2)
                <flux:input wire:model="client_reference" label="Referencia" />
                <flux:input wire:model="work_address" label="Dirección de Trabajo" />
                <flux:input wire:model="payment_address" label="Dirección de Cobro" />
                <flux:input wire:model="aval" label="Aval" />

                <!-- INE -->
                <div>
                    <label class="block text-sm font-medium mb-2">INE</label>
                    <input type="file" wire:model="picture_ine" accept="image/*" class="form-input" />
                    @error('pictures_ine') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Domicilio -->
                <div>
                    <label class="block text-sm font-medium mb-2">Comprobante Domicilio</label>
                    <input type="file" wire:model="picture_domicilio" accept="image/*" class="form-input" />
                    @error('picture_domicilio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Foto (Imágenes) -->
                <div>
                    <label class="block text-sm font-medium mb-2">Extra</label>
                    <input type="file" wire:model="picture_foto" accept="image/*" class="form-input" />
                    @error('picture_foto') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Botón Guardar -->
            <div>
                <x-button type="submit" variant="primary">Guardar</x-button>
            </div>
        </form>
    </div>
