<div class="p-6">

<flux:main> <!-- center the content -->

    <h3 class="mb-2">Listado de Préstamos</h3>

    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="p-2">Cliente</th>
                <th class="p-2">Monto</th>
                <th class="p-2">Fecha</th>
                <th class="p-2">Cobrador</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $loan)
                <tr class="border-t">
                    <td class="p-2">{{ $loan->user->name ?? 'N/A' }}</td>
                    <td class="p-2">${{ number_format($loan->amount, 2) }}</td>
                    <td class="p-2">{{ $loan->term }}</td>
                    <td class="p-2">{{ $loan->collector ? \App\Models\User::find($loan->collector)->name : 'No asignado' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</flux:main>
</div>

