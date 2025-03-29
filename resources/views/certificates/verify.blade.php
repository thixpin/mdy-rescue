<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Donation Certificate</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow-lg">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Verify Donation Certificate
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Enter the certificate ID to verify the certificate
                </p>
            </div>

            <div class="mt-8 space-y-6">
                <div>
                    <label for="short_id" class="sr-only">Certificate ID</label>
                    <input id="short_id" name="short_id" type="text" required 
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm uppercase" 
                        placeholder="Enter Certificate ID (e.g., ABC123XYZ9)"
                        maxlength="10"
                        oninput="this.value = this.value.toUpperCase()">
                </div>

                <div>
                    <button onclick="verifyCertificate()" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Verify Certificate
                    </button>
                </div>
            </div>

            <!-- Result Section -->
            <div id="result" class="hidden mt-6">
                <div class="rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg id="success-icon" class="hidden h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <svg id="error-icon" class="hidden h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 id="result-title" class="text-sm font-medium"></h3>
                            <div id="result-message" class="mt-2 text-sm"></div>
                        </div>
                    </div>
                </div>

                <!-- Donation Details -->
                <div id="donation-details" class="hidden mt-4 p-4 bg-gray-50 rounded-md">
                    <h4 class="text-sm font-medium text-gray-900">Donation Details</h4>
                    <dl class="mt-2 text-sm text-gray-600">
                        <div class="grid grid-cols-3 gap-4 py-3">
                            <dt class="font-medium">Name:</dt>
                            <dd id="donation-name" class="col-span-2"></dd>
                        </div>
                        <div id="donation-description-container" class="grid grid-cols-3 gap-4 py-3">
                            <dt class="font-medium"></dt>
                            <dd id="donation-description" class="col-span-2"></dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4 py-3">
                            <dt class="font-medium">Amount:</dt>
                            <dd id="donation-amount" class="col-span-2"></dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4 py-3">
                            <dt class="font-medium">Date:</dt>
                            <dd id="donation-date" class="col-span-2"></dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4 py-3">
                            <dt class="font-medium">Certificate:</dt>
                            <dd id="donation-certificate" class="col-span-2"></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <script>
        function verifyCertificate() {
            const shortId = document.getElementById('short_id').value;
            const resultDiv = document.getElementById('result');
            const donationDetails = document.getElementById('donation-details');
            const successIcon = document.getElementById('success-icon');
            const errorIcon = document.getElementById('error-icon');
            const resultTitle = document.getElementById('result-title');
            const resultMessage = document.getElementById('result-message');

            // Reset UI
            resultDiv.classList.remove('hidden');
            donationDetails.classList.add('hidden');
            successIcon.classList.add('hidden');
            errorIcon.classList.add('hidden');

            fetch(`/verify-certificate/check`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: `short_id=${shortId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resultTitle.textContent = 'Certificate Verification';
                        resultTitle.className = 'text-sm font-medium text-green-800';
                        resultMessage.textContent = data.message;
                        resultMessage.className = 'mt-2 text-sm text-green-700';
                        successIcon.classList.remove('hidden');

                        if (data.is_valid) {
                            // Show donation details
                            donationDetails.classList.remove('hidden');
                            document.getElementById('donation-name').textContent = data.donation.name;
                            document.getElementById('donation-description').textContent = data.donation.description;
                            document.getElementById('donation-amount').textContent = `MMK ${parseFloat(data.donation.donation_amount).toLocaleString()}`;
                            document.getElementById('donation-date').textContent = data.donation.donate_date;
                            document.getElementById('donation-certificate').innerHTML = 
                                `<a href="${data.donation.certificate_url}" target="_blank" class="text-indigo-600 hover:text-indigo-900">View Certificate</a>`;

                            if(data.donation.description == null || data.donation.description == ''){
                                document.getElementById('donation-description-container').style.display = 'none';
                            }
                        }
                    } else {
                        resultTitle.textContent = 'Verification Failed';
                        resultTitle.className = 'text-sm font-medium text-red-800';
                        resultMessage.textContent = data.message;
                        resultMessage.className = 'mt-2 text-sm text-red-700';
                        errorIcon.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    resultTitle.textContent = 'Error';
                    resultTitle.className = 'text-sm font-medium text-red-800';
                    resultMessage.textContent = 'An error occurred while verifying the certificate.';
                    resultMessage.className = 'mt-2 text-sm text-red-700';
                    errorIcon.classList.remove('hidden');
                });
        }
    </script>
    @if($shortId)
        <script>
            document.getElementById('short_id').value = '{{ $shortId }}';
            verifyCertificate();
        </script>
    @endif
</body>
</html> 