@foreach ($sessions as $session)
    <option value={{ $session->id }}>{{ $session->date_range }}</option>
@endforeach