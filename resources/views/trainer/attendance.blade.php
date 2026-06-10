@extends('layouts.app')

@section('title', 'Trainer Attendance - Fitzone')

@section('content')
<h1>Mark Attendance</h1>
<section class="card">
    <div class="table-scroll">
        <table>
            <thead><tr><th>Member</th><th>Plan</th><th>Today</th><th>Action</th></tr></thead>
            <tbody>
                <tr><td>Amit Verma</td><td>Gold Plan</td><td><span class="badge green">Present</span></td><td><button class="btn">Present</button> <button class="btn ghost">Absent</button></td></tr>
                <tr><td>Neha Gupta</td><td>Silver Plan</td><td><span class="badge amber">Unmarked</span></td><td><button class="btn">Present</button> <button class="btn ghost">Absent</button></td></tr>
                <tr><td>Rahul Sharma</td><td>Gold Plan</td><td><span class="badge red">Absent</span></td><td><button class="btn">Present</button> <button class="btn ghost">Absent</button></td></tr>
            </tbody>
        </table>
    </div>
</section>
@endsection
