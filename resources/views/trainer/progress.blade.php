@extends('layouts.app')

@section('title', 'Progress Tracking - Fitzone')

@section('content')
<h1>Progress Tracking</h1>
<div class="grid two">
    <section class="card">
        <h2>Member Progress Notes</h2>
        <div class="table-scroll">
            <table><thead><tr><th>Member</th><th>Weight</th><th>Progress Notes</th></tr></thead><tbody>
                <tr><td>Amit Verma</td><td>78 kg</td><td>Improved shoulder stability.</td></tr>
                <tr><td>Neha Gupta</td><td>62 kg</td><td>Needs consistency on leg days.</td></tr>
            </tbody></table>
        </div>
    </section>
    <section class="card">
        <h2>Weight Tracking Chart</h2>
        <div class="chart"><svg viewBox="0 0 700 220" preserveAspectRatio="none"><polyline points="0,75 130,92 260,118 390,132 520,150 700,170" fill="none" stroke="#22c55e" stroke-width="5"/></svg></div>
    </section>
</div>
@endsection
