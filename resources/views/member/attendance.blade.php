@extends('layouts.app')

@section('title', 'My Attendance - Fitzone')

@section('content')
<h1>Attendance</h1>
<section class="card">
    <h2>Calendar View</h2>
    <div class="calendar">
        @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat','26','27','28','29','30','1','2','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30'] as $day)
            <div class="day {{ in_array($day, ['1','15','19','22','26','29']) ? 'present' : (in_array($day, ['10','16']) ? 'absent' : '') }}">{{ $day }}</div>
        @endforeach
    </div>
    <p class="muted"><strong style="color:#0f172a">75%</strong> attendance history, 18 present and 4 absent.</p>
</section>
@endsection
