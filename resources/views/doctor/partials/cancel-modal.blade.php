<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900">Decline Appointment</h3>
            <form id="cancelForm" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="action" value="decline">
                <div class="mt-2">
                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700">Reason for declining</label>
                    <textarea
                        name="cancellation_reason"
                        id="cancellation_reason"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    ></textarea>
                </div>
                <div class="mt-4 flex justify-end space-x-3">
                    <button type="button" onclick="hideCancelModal()" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                        Decline Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCancelModal(appointmentId) {
    const modal = document.getElementById('cancelModal');
    const form = document.getElementById('cancelForm');
    form.action = `/doctor/appointments/${appointmentId}/respond`;
    modal.classList.remove('hidden');
}

function hideCancelModal() {
    const modal = document.getElementById('cancelModal');
    modal.classList.add('hidden');
    document.getElementById('cancellation_reason').value = '';
}

document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideCancelModal();
    }
});
</script> 