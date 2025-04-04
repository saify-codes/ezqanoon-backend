<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.team.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>

    <!-- Display validation errors (if any) -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Create invoice</h4>

            <!-- The form -->
            <form action="{{ route('lawyer.invoice.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <fieldset>
                    <legend>Issuer</legend>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="issuer_name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="issuer_name" name="issuer_name" value="{{ old('issuer_name') }}" placeholder="e.g. Musaafa" required>
                        </div>
                        <div class="col-md-3">
                            <label for="issuer_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="issuer_phone" name="issuer_phone" value="{{ old('issuer_phone') }}" placeholder="e.g. +923487161543" required>
                        </div>
                        <div class="col-md-6">
                            <label for="issuer_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="issuer_email" name="issuer_email" value="{{ old('issuer_email') }}" placeholder="e.g. test@gmail.com" required>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Recipient</legend>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="recipient_name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" placeholder="e.g. Musaafa" required>  
                        </div>
                        <div class="col-md-3">
                            <label for="recipient_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="recipient_phone" name="recipient_phone" value="{{ old('recipient_phone') }}" placeholder="e.g. +923487161543" required>
                        </div>
                        <div class="col-md-3">
                                <label for="recipient_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="recipient_email" name="recipient_email" value="{{ old('recipient_email') }}" placeholder="e.g. test@gmail.com" required>
                            </div>
                            <div class="col-md-3">
                                <label for="recipient_address" class="form-label">Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="recipient_address" name="recipient_address" value="{{ old('recipient_address') }}" placeholder="e.g. 123 Main St, Anytown, USA" required>
                        </div>
                    </div>
                </fieldset> 

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="PENDING">Pending</option>
                            <option value="PAID">Paid</option>
                            <option value="OVERDUE">Overdue</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Particulars</th>
                                    <th>Qty</th>
                                    <th>Amount</th>
                                    <th>Tax (%)</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" name="particulars[0]" placeholder="e.g. Service 1" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="qty[0]" placeholder="e.g. 1" value="1" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="amount[0]" placeholder="e.g. 100" value="0" min="0" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="tax[0]" placeholder="e.g. 10" value="0" min="0" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="total[0]" value="0.00" readonly required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-icon btn-danger delete-row">
                                            <i data-feather="trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6">
                                        <button type="button" class="btn btn-icon btn-success add-row w-100">
                                            <i data-feather="plus"></i> 
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Submit button (with an ID for enabling/disabling) -->
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Create Invoice
                </button>
            </form>
        </div>
    </div>


    @push('custom-scripts')
        <script>
            $(document).ready(function() {
                // Function to calculate total
                function calculateTotal(row) {
                    const qty       = parseFloat(row.find('input[name^="qty"]').val())      || 0
                    const amount    = parseFloat(row.find('input[name^="amount"]').val())   || 0
                    const tax       = parseFloat(row.find('input[name^="tax"]').val())      || 0
                    const subtotal  = qty * amount
                    const taxAmount = (subtotal * tax) / 100
                    const total     = subtotal + taxAmount
                    
                    row.find('input[name^="total"]').val(total.toFixed(2));
                }

                // Add row handler
                $('table').on('click', '.add-row', function() {
                    var rowIndex = $('tbody tr').length;
                    $('tbody').append(`
                        <tr>
                            <td>
                                <input type="text" class="form-control" name="particulars[${rowIndex}]" placeholder="e.g. Service 1" required>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="qty[${rowIndex}]" placeholder="e.g. 1" required>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="amount[${rowIndex}]" placeholder="e.g. 100" required>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="tax[${rowIndex}]" placeholder="e.g. 10" required>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="total[${rowIndex}]" placeholder="e.g. 100" readonly>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger delete-row">
                                    <i data-feather="trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);

                    feather.replace();
                });

                // Input change handler for automatic total calculation
                $('table').on('input', 'input[name^="qty"], input[name^="amount"], input[name^="tax"]', function() {
                    calculateTotal($(this).closest('tr'));
                });

                // Delete row handler
                $('table').on('click', '.delete-row', function() {
                    $(this).closest('tr').remove();
                });
            });
        </script>
    @endpush
</x-lawyer.app>
