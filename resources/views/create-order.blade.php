<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Laundry Order</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans p-8">

    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg border border-gray-200 shadow-sm">
        <h1 class="text-2xl font-semibold mb-6">Create New Laundry Order</h1>

        <!-- Display Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('orders.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Customer Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black" 
                       placeholder="John Doe" required>
            </div>

            <!-- Contact Number -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                <input type="text" name="contact_number" value="{{ old('contact_number') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black" 
                       placeholder="09123456789" required>
            </div>

            <!-- Pickup Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Date</label>
                <input type="date" name="pickup_date" value="{{ old('pickup_date') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black" 
                       required>
            </div>

            <!-- Items Section -->
            <div class="border-t pt-4 mt-4">
                <h2 class="text-lg font-medium mb-3">Order Items</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                        <select name="items[0][service_type_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black" required>
                            <option value="">Select a service...</option>
                            <!-- Make sure you have these in your database! -->
                            <option value="1">Wash & Fold</option>
                            <option value="2">Dry Clean</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                        <input type="number" step="0.1" name="items[0][weight_kg]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black" 
                               placeholder="3.5" required>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" class="w-full bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 font-medium">
                    Submit Order
                </button>
            </div>
        </form>
    </div>

</body>
</html>