<form action="{{ route('logout') }}" method="POST">
    @csrf
    <input type="submit" value="logout" />
</form>
