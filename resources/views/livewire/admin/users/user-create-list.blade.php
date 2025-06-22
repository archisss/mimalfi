<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Listado de Usuarios</h2>

    <!-- Mensaje de éxito -->
    @if(session()->has('success'))
        <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="flex items-center gap-4 mb-4">
            @if($search!='')
                <flux:button wire:click="cleansearch" icon="x-mark" variant="danger" />
            @endif
            <input
                type="text"
                wire:model.live="search"
                placeholder="Buscar por nombre..."
                class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <flux:select wire:model.live="userTypeFilter">
                <flux:select.option value="">Todos</flux:select.option>
                <flux:select.option value="1">Cobradores</flux:select.option>
                <flux:select.option value="2">Clientes</flux:select.option>
            </flux:select>
    </div>

    <!-- Tabla de usuarios -->
    <table class="w-full text-sm border text-center">
        <thead>
            <tr>
                <th class="p-2">Nombre</th>
                <th class="p-2">Email</th>
                <th class="p-2">Tipo</th>
                <th class="p-2">Teléfono</th>
                <th class="p-2">Dirección</th>
                <th class="p-2">Dirección de Cobro</th>
                <th class="p-2">Imagenes</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr class="border-b">
                    <td class="p-2">{{ $user->name }}</td>
                    <td class="p-2">{{ $user->email }}</td>
                    <td class="p-2">
                        @if($user->user_type == 1)
                            Cobrador
                        @elseif($user->user_type == 2)
                            Cliente
                        @else
                            N/A
                        @endif
                    </td>
                        <td class="p-2">{{ $user->cellphone ?? 'N/A' }}</td>
                        <td class="p-2">{{ $user->userDetail->work_address ?? 'N/A' }}</td>
                        <td class="p-2">{{ $user->userDetail->payment_address ?? 'N/A' }}</td>
                        <td class="p-2">
  @if($user->userDetail?->picture_ine)
    <a href="{{ Storage::url($user->userDetail->picture_ine) }}" target="_blank">
      <img src="{{ Storage::url($user->userDetail->picture_ine) }}"
           alt="INE"
           class="h-10 w-10 object-cover inline-block mr-1">
    </a>
  @endif
  @if($user->userDetail?->picture_domicilio)
    <a href="{{ Storage::url($user->userDetail->picture_domicilio) }}" target="_blank">
      <img src="{{ Storage::url($user->userDetail->picture_domicilio) }}"
           alt="Domicilio"
           class="h-10 w-10 object-cover inline-block mr-1">
    </a>
  @endif
  @if($user->userDetail?->picture_foto)
    <a href="{{ Storage::url($user->userDetail->picture_foto) }}" target="_blank">
      <img src="{{ Storage::url($user->userDetail->picture_foto) }}"
           alt="Foto"
           class="h-10 w-10 object-cover">
    </a>
  @endif
</td>

                        <td class="p-2 space-x-2">
                        <flux:button icon="currency-dollar" tooltip="Crear Préstamo" href="{{ route('admin.loans.create', ['user_id' => $user->id]) }}" color="primary" />
                        <flux:button icon="pencil-square" tooltip="Editar" href="{{ route('admin.users.edit', ['user_id' => $user->id]) }}" color="primary" />
                            <!-- <a href="{{ route('admin.loans.create', ['user_id' => $user->id]) }}" class="text-blue-500 hover:underline">Crear Préstamo</a>
                        <a href="{{ route('admin.users.user.create.list', ['user' => $user->id]) }}" class="text-green-500 hover:underline">Editar</a> -->
                        </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-2 text-center">No se encontraron usuarios.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

