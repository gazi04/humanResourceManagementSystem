@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form method="POST" action="{{ route('hr.ticket.create') }}">
    @csrf
    <label>Subject</label><br>
    <input type='text' name="subject" /><br>
    <label>description</label><br>
    <input type='text' name='description' /><br>
    <input type='submit' value='Send' />
</form>
