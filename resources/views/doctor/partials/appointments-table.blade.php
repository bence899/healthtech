@if($appointments->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($appointments as $appointment)
                    <tr>
                        <td class="px-6 py-4">{{ $appointment->patient->user->name }}</td>
                        <td class="px-6 py-4">{{ $appointment->appointment_date->format('M d, Y h:i A') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($appointment->status === 'cancelled')
                                <span class="text-red-600">{{ $appointment->cancellation_reason }}</span>
                            @else
                                {{ $appointment->reason_for_visit }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($appointment->status === 'pending')
                                <form method="POST" action="{{ route('doctor.appointments.respond', $appointment) }}" class="inline">
                                    @csrf
                                    <button type="submit" name="action" value="confirm" 
                                        class="text-green-600 hover:text-green-900 mr-3">Confirm</button>
                                    <button type="button" 
                                        onclick="showCancelModal('{{ $appointment->id }}')"
                                        class="text-red-600 hover:text-red-900">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-gray-500">No appointments found.</p>
@endif 