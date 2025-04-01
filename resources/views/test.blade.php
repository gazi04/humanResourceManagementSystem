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
<form method="POST" action="{{ route('hr.upload-contract') }}" enctype="multipart/form-data">
    @csrf
    <input type='hidden' name='employeeID' value="132" />
    <input type='file' name='contract_file' />
    <input type='submit' />
</form>
<br>
<form method="POST" action="{{ route('hr.get-contracts') }}">
    @csrf
    <input type='hidden' name='employeeID' value="1" />
    <input type='submit' value='get contracts' />
</form>
