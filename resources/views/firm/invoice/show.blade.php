<x-firm.app>
    <div>
        <a href="{{ route('firm.invoice.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h6>Invoice Details</h6>
        </div>
        <div class="card-body">

            <!-- invoice Info -->
            <div class="table-responsive mb-5">
                <table class="table table-hover" style="table-layout: fixed">
                    <tbody>
                        <tr>
                            <th scope="row">Name</th>
                            <td>{{ $invoice->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Email</th>
                            <td>{{ $invoice->email }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Phone</th>
                            <td>{{ $invoice->phone }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Address</th>
                            <td>{{ $invoice->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">City</th>
                            <td>{{ $invoice->city ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Country</th>
                            <td>{{ $invoice->country ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Under case type</th>
                            <td>{{ $invoice->case_type ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Type</th>
                            <td>{{ $invoice->type }}</td>
                        </tr>
                        
                        @if ($invoice->type === 'MILESTONE')
                            <th scope="row">Due dates</th>
                            <td>
                                <table class="table">
                                    <tr>
                                        <th scope="row">Description</th>
                                        <th scope="row">Due date</th>
                                        <th scope="row">status</th>
                                    </tr>
                                    @foreach ($invoice->milestone as $milestone)
                                        <tr>
                                            <td>{{$milestone['description']}}</td>
                                            <td>{{$milestone['due_date']}}</td>
                                            <td><x-firm.payment-badge :status="$milestone['status']"/></td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        @else   
                            <tr>
                                <th scope="row">Status</th>
                                <td><x-firm.payment-badge :status="$invoice->status"/></td>
                            </tr>
                            <tr>
                                <th scope="row">Due date</th>
                                <td>{{$invoice->due_date}}</td>
                            </tr>
                        @endif
                        <tr>
                            <th scope="row">Payment method</th>
                            <td>{{$invoice->payment_method}}</td>
                        </tr>
                        <tr>
                            <th scope="row">Total</th>
                            <td>{{$invoice->total}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-responsive mb-5">
                {{-- Receipt --}}
                <h4 class="mb-3">Receipt</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Particulars</th>
                            <th>Qty</th>
                            <th>Amount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->receipt as $receipt)
                            <tr>
                                <td>{{$receipt['particular']}}</td>
                                <td>{{$receipt['qty']}}</td>
                                <td>{{$receipt['amount']}}</td>
                                <td>{{$receipt['total']}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex">
        <a href="{{ route('firm.invoice.edit', $invoice->id) }}" class="btn btn-primary btn-icon-text me-3">
            <i data-feather="edit-2"></i> Edit invoice
        </a>
        <form action="{{ route('firm.invoice.destroy', $invoice->id) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this invoice?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-icon-text">
                <i data-feather="trash"></i> Delete invoice
            </button>
        </form>
    </div>
</x-firm.app>
