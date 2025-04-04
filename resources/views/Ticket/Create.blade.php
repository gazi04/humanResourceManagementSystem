<form method="POST" action="{{ route('hr.ticket.create') }}">
    @csrf
    <label>Subject</label><br>
    <input type='text' name="subject" /><br>
    <label>description</label><br>
    <input type='text' name='description' /><br>
    <input type='submit' value='Send' />
</form>
