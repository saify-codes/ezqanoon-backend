<x-team.app>
    <div>
        <a href="{{ route('team.invoice.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>

    <!-- Validation errors -->
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
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="card-title m-0">Create invoice</h4>
                <select class="form-control w-auto" id="type" name="type" form="invoice-form" required>
                    <option value="ONE TIME">One time</option>
                    <option value="MILESTONE">Milestone</option>
                </select>
            </div>

            <form action="{{ route('team.invoice.store') }}" method="POST" id="invoice-form">
                @csrf

                <fieldset>
                    <legend>Recipient</legend>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                placeholder="e.g. Musaafa" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input 
                                    type="tel" 
                                    class="form-control" 
                                    id="phone" 
                                    value="{{ old('phone') }}" 
                                    required
                                >
                            </div>
                            <input type="hidden" name="phone">
                            <input type="hidden" name="country_code">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                placeholder="test@gmail.com" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="123 Main St…" required>
                        </div>
                    </div>
                </fieldset>
                
                
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country') }}" placeholder="e.g. Pakistan">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="e.g. Karachi">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Payment method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-control" required>
                            <option value="CASH" {{old('payment_method') === 'CASH'? 'selected' : ''}}>Cash</option>
                            <option value="BANK" {{old('payment_method') === 'BANK'? 'selected' : ''}}>Bank</option>
                            <option value="ONLINE" {{old('payment_method') === 'ONLINE'? 'selected' : ''}}>Online transfer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="type" class="form-label">Case type <span class="text-danger">*</span></label>
                        <select class="form-select" id="case-type" name="case_type" required>
                            <option value="CRIMINAL" {{ old('case_type') === 'CRIMINAL' ? 'selected' : '' }}>CRIMINAL</option>
                            <option value="CIVIL"    {{ old('case_type') === 'CIVIL' ? 'selected' : '' }}>CIVIL</option>
                            <option value="OTHERS"   {{ old('case_type') === 'OTHERS' ? 'selected' : '' }}>OTHERS</option>
                        </select>
                    </div>
                </div>



                <!-- ONE-TIME section -->
                <template id="one_time_template">
                    <div id="one_time" class="mb-3">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="PENDING" {{old('status') === 'PENDING'? 'selected' : ''}}>Pending</option>
                                    <option value="PAID"    {{old('status') === 'PAID'? 'selected' : ''}}>Paid</option>
                                    <option value="OVERDUE" {{old('status') === 'OVERDUE'? 'selected' : ''}}>Overdue</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- MILESTONE section -->
                <template id="milestone_template">
                    <div id="milestone" class="mb-3">
                        <div class="row mb-3 milestone-row" data-index="0">
                            <div class="col-md-4">
                                <label class="form-label">Milestone Description <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="milestone[0][description]" class="form-control description"
                                    placeholder="e.g. first draft" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="milestone[0][due_date]" class="form-control due_date"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <select name="milestone[0][status]" class="form-control status" required>
                                        <option value="PENDING">Pending</option>
                                        <option value="PAID">Paid</option>
                                        <option value="OVERDUE">Overdue</option>
                                    </select>
                                    <button type="button" class="btn btn-icon btn-success add-milestone">
                                        <i data-feather="plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- RECEIPT table -->
                <div class="row mb-3">
                    <div class="col-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Particulars</th>
                                    <th>Qty</th>
                                    <th>Amount</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" name="receipt[0][particular]" class="form-control"
                                            placeholder="Service 1" required></td>
                                    <td><input type="number" name="receipt[0][qty]" class="form-control"
                                            value="1" min="1" required></td>
                                    <td><input type="number" name="receipt[0][amount]" class="form-control"
                                            value="0" min="0" required></td>
                                    <td><input type="number" name="receipt[0][total]" class="form-control"
                                            value="0.00" readonly required></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-icon btn-danger delete-row">
                                            <i data-feather="trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">Grand Total</td>
                                    <td colspan="2">
                                        <input id="grand_total" name="grand_total" class="form-control" type="number" value="0" readonly >
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <button type="button" class="btn btn-icon btn-success add-row w-100">
                                            <i data-feather="plus"></i> Add row
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="row mb-3 justify-content-end">
                    <div class="col-md-3">
                        <label class="form-label">Amount paid</label>
                        <input type="number" min="0" name="paid" class="form-control" value="{{ old('paid', 0) }}" placeholder="Enter amount paid">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">Create Invoice</button>
            </form>
        </div>
    </div>

    @push('plugin-styles')
        <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    @endpush


    @push('custom-scripts')
        <script>

            const iti = intlTelInput(document.querySelector("#phone"), {
                            separateDialCode: true,
                            initialCountry: "pk",
                            loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js"),
                            strictMode: true    
            });

            
            
            function calculateGrandTotal() {
                let sum = 0;
                $('tbody tr').each(function() {
                    sum += parseFloat($(this).find('input[name*="[total]"]').val()) || 0
                });
                $('#grand_total').val(sum.toFixed(2));
            }
            // calculate total on receipt table
            function calculateTotal(row) {
                const qty        = parseFloat(row.find('input[name*="[qty]"]').val()) || 0;
                const amount     = parseFloat(row.find('input[name*="[amount]"]').val()) || 0;
                row.find('input[name*="[total]"]').val((qty * amount).toFixed(2));
                calculateGrandTotal()
            }

            $('form').on('submit', (eve) => {
                
                const paid       = parseFloat($('input[name="paid"]').val()) || 0;
                const grandTotal = parseFloat($('#grand_total').val()) || 0;

                if (paid > grandTotal) {
                    eve.preventDefault();
                    Swal.fire('Error', 'Amount paid cannot be greater than the bill total', 'error');
                    return false;
                }

                if (!iti.isValidNumber()) {
                    Swal.fire('Error', 'Please enter a valid phone number', 'error');
                    eve.preventDefault()
                }

                // If valid, set the phone number and country code
                if ($('#phone').val()) {
                    $('[name="phone"]').val(iti.getNumber());
                    $('[name="country_code"]').val(iti.getSelectedCountryData().iso2);
                }
            });

            // toggle sections
            $('#type').change(function() {

                switch (this.value) {
                    case 'ONE TIME':
                        
                        // 1) Show the one‑time form
                        var $oneTmpl     = $('#one_time_template');
                        var $oneFragment = $($oneTmpl.prop('content').cloneNode(true));
                        $oneTmpl.replaceWith($oneFragment);

                        // 2) Wrap the existing #milestone back into a new template
                        var $milestone = $('#milestone');
                        if ($milestone.length) {
                            var $msTmpl = $('<template>', { id: 'milestone_template' });
                            $milestone.replaceWith($msTmpl);
                            // DOM append into the template’s content
                            $msTmpl[0].content.appendChild($milestone[0]);
                        }
                    break;
                    
                    case 'MILESTONE':
                        
                        // 1) Show the milestone form
                        var $msTmpl      = $('#milestone_template');
                        var $msFragment  = $($msTmpl.prop('content').cloneNode(true));
                        $msTmpl.replaceWith($msFragment);

                        // 2) Wrap the existing #one_time back into a new template
                        var $oneTime = $('#one_time');
                        if ($oneTime.length) {
                            var $otTmpl = $('<template>', { id: 'one_time_template' });
                            $oneTime.replaceWith($otTmpl);
                            $otTmpl[0].content.appendChild($oneTime[0]);
                        }
                    
                    break;
                }

                feather.replace()
            });

            $('#type').trigger('change')

            $('table').on('input', 'input[name*="[qty]"], input[name*="[amount]"]', function() {
                calculateTotal($(this).closest('tr'));
            });

            // add receipt row
            $('table').on('click', '.add-row', function() {
                const index = $('tbody tr').length;
                $('tbody').append(`
                <tr>
                    <td><input type="text" name="receipt[${index}][particular]" class="form-control" placeholder="Service 1" required></td>
                    <td><input type="number" name="receipt[${index}][qty]" class="form-control" value="1" min="1" required></td>
                    <td><input type="number" name="receipt[${index}][amount]" class="form-control" value="0" min="0" required></td>
                    <td><input type="number" name="receipt[${index}][total]" class="form-control" value="0.00" readonly></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-icon btn-danger delete-row"><i data-feather="trash"></i></button>
                    </td>
                </tr>
            `);
                feather.replace();
            });

            // delete receipt row
            $('table').on('click', '.delete-row', function() {
                $(this).closest('tr').remove();
            });

            // add milestone
            $(document).on('click', '.add-milestone', function() {
                const index = $('#milestone .milestone-row').length;

                $(this)
                    .removeClass('btn-success add-milestone')
                    .addClass('btn-danger remove-milestone')
                    .html('<i data-feather="trash"></i>');

                $('#milestone').append(`
                    <div class="row mb-3 milestone-row" data-index="0">
                        <div class="col-md-4">
                            <label class="form-label">Milestone Description <span class="text-danger">*</span></label>
                            <input type="text" name="milestone[${index}][description]" class="form-control description" placeholder="e.g. first draft" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="milestone[${index}][due_date]" class="form-control due_date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <select name="milestone[${index}][status]" class="form-control status" required>
                                    <option value="PENDING">Pending</option>
                                    <option value="PAID">Paid</option>
                                    <option value="OVERDUE">Overdue</option>
                                </select>
                                <button type="button" class="btn btn-icon btn-success add-milestone">
                                    <i data-feather="plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);
                feather.replace();
            });

            // remove milestone
            $(document).on('click', '.remove-milestone', function() {
                $(this).closest('.milestone-row').remove();
                calculateGrandTotal()
            });

            $('#case-type').change(function() {

                const parent = $(this).parent();
                const value  = this.value;

                if (value === 'OTHERS') {
                    parent.prop('class', 'col-md-1').after(`
                        <div class="col-md-2">
                            <label for="otherType" class="form-label">Specify case type</label>
                            <input type="text" class="form-control" id="otherType" name="case_type" placeholder="Enter case type"/>
                        </div>
                    `);
                } else {
                    parent.prop('class', 'col-md-3').next().remove();
                }
            });
        </script>
    @endpush
</x-team.app>
