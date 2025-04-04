<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">Ticket Management</h4>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Subject</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Requester</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->subject }}</td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;"
                                                               title="{{ $ticket->description }}">
                                                               {{ $ticket->description }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $ticket->status === 'closed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </td>
                                <td>{{ $ticket->firstName }} {{ $ticket->lastName }}</td>
                                <td>{{ $ticket->email }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No tickets found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tickets->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
