@extends('layouts.admin')

@section('content')
<div class="mx-auto px-4">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">Donation List</h2>
            <div class="flex gap-3">
                <form action="{{ route('admin.donations.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center">
                    @csrf
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="mr-2 border rounded px-3 py-2" required>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center">
                        <i class="fas fa-file-import mr-2"></i>Import Excel
                    </button>
                </form>
                <button onclick="document.getElementById('addDonationModal').classList.remove('hidden')" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center">
                    <i class="fas fa-plus mr-2"></i>Add New Donation
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-bs-dismiss="alert" aria-label="Close">
                <span class="sr-only">Close</span>
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-bs-dismiss="alert" aria-label="Close">
                <span class="sr-only">Close</span>
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount in Text</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donate Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verified</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($donations as $donation)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $donation->name }}</td>
                                <td class="px-6 py-4">{{ Str::limit($donation->description, 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($donation->donation_amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $donation->amount_in_text }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $donation->donate_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($donation->certificate_url)
                                        <a href="{{ $donation->certificate_url }}" target="_blank" class="text-blue-600 hover:text-blue-800" title="View Certificate">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-400" title="No Certificate">
                                            <i class="fas fa-file-alt"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.donations.toggle-verification', $donation) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 rounded {{ $donation->verified ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}" 
                                                data-verified="{{ $donation->verified ? 'true' : 'false' }}"
                                                data-donation-id="{{ $donation->short_id }}"
                                                title="{{ $donation->verified ? 'Verified' : 'Not Verified' }}">
                                            <i class="fas {{ $donation->verified ? 'fa-check-circle' : 'fa-circle' }}"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <button type="button" data-donation-id="{{ $donation->short_id }}" 
                                                class="text-blue-600 hover:text-blue-800 edit-donation-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.donations.destroy', $donation) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" 
                                                    onclick="return confirm('Are you sure you want to delete this donation?')" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $donations->links() }}
            </div>
        </div>
    </div>

    <!-- Add Donation Modal -->
    <div id="addDonationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" tabindex="-1" aria-hidden="true">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-lg font-medium">Add New Donation</h5>
                <button type="button" class="text-gray-400 hover:text-gray-500" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.donations.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="name">Donor Name</label>
                        <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="description">Description</label>
                        <textarea name="description" id="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="donation_amount">Donation Amount</label>
                        <input type="number" step="0.01" name="donation_amount" id="donation_amount" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="amount_in_text">Amount in Text</label>
                        <input type="text" name="amount_in_text" id="amount_in_text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="donate_date">Donate Date</label>
                        <input type="date" name="donate_date" id="donate_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add Donation</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Donation Modal -->
    <div id="editDonationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" tabindex="-1" aria-hidden="true">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-lg font-medium">Edit Donation</h5>
                <button type="button" class="text-gray-400 hover:text-gray-500" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editDonationForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="edit_name">Donor Name</label>
                        <input type="text" name="name" id="edit_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="edit_description">Description</label>
                        <textarea name="description" id="edit_description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="edit_donation_amount">Donation Amount</label>
                        <input type="number" step="0.01" name="donation_amount" id="edit_donation_amount" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="edit_amount_in_text">Amount in Text</label>
                        <input type="text" name="amount_in_text" id="edit_amount_in_text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="edit_donate_date">Donate Date</label>
                        <input type="date" name="donate_date" id="edit_donate_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="edit_certificate_url">Certificate URL</label>
                        <input type="url" name="certificate_url" id="edit_certificate_url" disabled class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update Donation</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentDonationId = null;

        // Initialize modals
        document.addEventListener('DOMContentLoaded', function() {
            // Handle edit donation
            document.querySelectorAll('.edit-donation-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const donationId = this.dataset.donationId;
                    editDonation(donationId);
                });
            });
        });

        function editDonation(id) {
            currentDonationId = id;
            const modal = document.getElementById('editDonationModal');
            const form = document.getElementById('editDonationForm');
            
            // Fetch donation data
            fetch(`/admin/donations/${id}/edit`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(donation => {
                // Set form action with the donation ID
                form.action = `/admin/donations/${id}`;
                
                // Populate form fields
                document.getElementById('edit_name').value = donation.name;
                document.getElementById('edit_description').value = donation.description || '';
                document.getElementById('edit_donation_amount').value = donation.donation_amount;
                document.getElementById('edit_amount_in_text').value = donation.amount_in_text;
                document.getElementById('edit_certificate_url').value = donation.certificate_url || '';
                
                // Format the date properly for date input (YYYY-MM-DD)
                const donateDate = new Date(donation.donate_date);
                const formattedDate = donateDate.toISOString().split('T')[0];
                document.getElementById('edit_donate_date').value = formattedDate;
                
                // Show the modal
                modal.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching donation data:', error);
                alert('Error loading donation data. Please try again.');
            });
        }
    </script>
@endsection 