<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Donation Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-bold">Admin Dashboard</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.donations.index') }}" class="border-indigo-500 text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Donations
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Donation List</h2>
                <div class="flex space-x-4">
                    <form action="{{ route('admin.donations.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center">
                        @csrf
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" class="mr-2" required>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            Import Excel
                        </button>
                    </form>
                    <button onclick="document.getElementById('addDonationModal').classList.remove('hidden')" 
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Add New Donation
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
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
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $donation->name }}</td>
                                <td class="px-6 py-4">{{ Str::limit($donation->description, 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($donation->donation_amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $donation->amount_in_text }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $donation->donate_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-1">
                                        @if($donation->certificate_url)
                                            <a href="{{ $donation->certificate_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900" title="View Certificate">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                            <!-- <button type="button" data-donation-id="{{ $donation->short_id }}" class="verify-certificate-btn text-blue-600 hover:text-blue-900" title="Verify Certificate">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <span id="certificate-status-{{ $donation->short_id }}" class="text-sm"></span> -->
                                        @else
                                            <a href="#" target="_blank" class="text-gray-500 hover:text-indigo-900" title="No Certificate">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.donations.toggle-verification', $donation) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="toggle-verification-btn {{ $donation->verified ? 'text-green-600 hover:text-green-900' : 'text-gray-600 hover:text-gray-900' }}" 
                                                data-verified="{{ $donation->verified ? 'true' : 'false' }}"
                                                data-donation-id="{{ $donation->short_id }}"
                                                title="{{ $donation->verified ? 'Verified' : 'Not Verified' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                @if($donation->verified)
                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                @else
                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812z" clip-rule="evenodd" />
                                                @endif
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" data-donation-id="{{ $donation->short_id }}" class="edit-donation-btn text-indigo-600 hover:text-indigo-900 mr-3" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.donations.destroy', $donation) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this donation?')" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $donations->links() }}
            </div>
        </div>
    </main>

    <!-- Add Donation Modal -->
    <div id="addDonationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Donation</h3>
                <form action="{{ route('admin.donations.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Donor Name</label>
                        <input type="text" name="name" id="name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
                        <textarea name="description" id="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="donation_amount">Donation Amount</label>
                        <input type="number" step="0.01" name="donation_amount" id="donation_amount" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="amount_in_text">Amount in Text</label>
                        <input type="text" name="amount_in_text" id="amount_in_text" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="donate_date">Donate Date</label>
                        <input type="date" name="donate_date" id="donate_date" value="{{ date('Y-m-d') }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <!-- <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="certificate_url">Certificate URL</label>
                        <input type="url" name="certificate_url" id="certificate_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="https://example.com/certificate.pdf">
                    </div> -->
                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Add Donation</button>
                        <button type="button" onclick="document.getElementById('addDonationModal').classList.add('hidden')" class="text-gray-600 hover:text-gray-900">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-2 text-center">
                    <h3 class="text-lg font-medium text-gray-900" id="confirmationTitle">Confirm Action</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" id="confirmationMessage"></p>
                    </div>
                </div>
                <div class="mt-4 flex justify-center space-x-4">
                    <button id="confirmButton" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Confirm
                    </button>
                    <button onclick="closeConfirmationModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Donation Modal -->
    <div id="editDonationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Donation</h3>
                <form id="editDonationForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_name">Donor Name</label>
                        <input type="text" name="name" id="edit_name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_description">Description</label>
                        <textarea name="description" id="edit_description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_donation_amount">Donation Amount</label>
                        <input type="number" step="0.01" name="donation_amount" id="edit_donation_amount" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_amount_in_text">Amount in Text</label>
                        <input type="text" name="amount_in_text" id="edit_amount_in_text" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_donate_date">Donate Date</label>
                        <input type="date" name="donate_date" id="edit_donate_date" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_certificate_url">Certificate URL</label>
                        <input type="url" name="certificate_url" id="edit_certificate_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="" disabled>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Update Donation</button>
                        <button type="button" onclick="closeEditModal()" class="text-gray-600 hover:text-gray-900">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentDonationId = null;
        let currentAction = null;

        // Add event listeners when the document is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Handle certificate verification
            document.querySelectorAll('.verify-certificate-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const donationId = this.dataset.donationId;
                    verifyCertificate(donationId);
                });
            });

            // Handle donation verification toggle
            document.querySelectorAll('.toggle-verification-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const donationId = this.dataset.donationId;
                    const isVerified = this.dataset.verified === 'true';
                    showConfirmationModal(donationId, !isVerified);
                });
            });

            // Handle edit donation
            document.querySelectorAll('.edit-donation-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const donationId = this.dataset.donationId;
                    editDonation(donationId);
                });
            });

            // Handle delete donation
            document.querySelectorAll('.delete-donation-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const donationId = this.dataset.donationId;
                    deleteDonation(donationId);
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

        function closeEditModal() {
            document.getElementById('editDonationModal').classList.add('hidden');
            currentDonationId = null;
        }

        function showConfirmationModal(donationId, newStatus) {
            currentDonationId = donationId;
            currentAction = newStatus;
            
            const modal = document.getElementById('confirmationModal');
            const title = document.getElementById('confirmationTitle');
            const message = document.getElementById('confirmationMessage');
            const confirmButton = document.getElementById('confirmButton');
            
            title.textContent = newStatus ? 'Verify Donation' : 'Unverify Donation';
            message.textContent = `Are you sure you want to ${newStatus ? 'verify' : 'unverify'} this donation?`;
            confirmButton.className = `px-4 py-2 ${newStatus ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'} text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500`;
            confirmButton.textContent = newStatus ? 'Verify' : 'Unverify';
            
            modal.classList.remove('hidden');
        }

        function closeConfirmationModal() {
            document.getElementById('confirmationModal').classList.add('hidden');
            currentDonationId = null;
            currentAction = null;
        }

        function confirmVerification(donationId, newStatus) {
            showConfirmationModal(donationId, newStatus);
        }

        // Add event listener for the confirm button
        document.getElementById('confirmButton').addEventListener('click', function() {
            if (currentDonationId !== null && currentAction !== null) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/donations/${currentDonationId}/toggle-verification`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Close modal when clicking outside
        document.getElementById('confirmationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirmationModal();
            }
        });
    </script>
</body>
</html> 