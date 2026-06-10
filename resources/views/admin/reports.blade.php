@extends('layouts.app')

@section('title', 'Reports - Fitzone')

@section('content')
<h1>Reports & Analytics</h1>
<div class="grid three">
    <section class="card"><h2>Income Graph</h2><div class="chart"><svg viewBox="0 0 700 220" preserveAspectRatio="none"><polyline points="0,180 150,120 300,140 450,70 600,95 700,42" fill="none" stroke="#1263e6" stroke-width="5"/></svg></div></section>
    <section class="card"><h2>Attendance Graph</h2><div class="chart"><svg viewBox="0 0 700 220" preserveAspectRatio="none"><polyline points="0,110 140,92 280,130 420,88 560,70 700,84" fill="none" stroke="#22c55e" stroke-width="5"/></svg></div></section>
    <section class="card"><h2>Member Growth</h2><div class="chart"><svg viewBox="0 0 700 220" preserveAspectRatio="none"><polyline points="0,190 120,160 240,132 360,118 500,82 700,48" fill="none" stroke="#7c3aed" stroke-width="5"/></svg></div></section>
</div>
@endsection
