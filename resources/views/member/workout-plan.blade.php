@extends('layouts.app')

@section('title', 'My Workout Plan - Fitzone')

@section('content')
<h1>Workout Plan</h1>
<section class="card">
    <h2>Daily Exercises</h2>
    <div class="table-scroll">
        <table><thead><tr><th>Exercise</th><th>Sets</th><th>Reps</th><th>Instructions</th><th>Trainer Notes</th></tr></thead><tbody>
            <tr><td>Bench Press</td><td>4</td><td>12</td><td>Controlled movement</td><td>Do not lock elbows.</td></tr>
            <tr><td>Incline Press</td><td>4</td><td>12</td><td>Medium incline</td><td>Keep shoulders back.</td></tr>
            <tr><td>Push Ups</td><td>3</td><td>15</td><td>Full range</td><td>Slow tempo.</td></tr>
        </tbody></table>
    </div>
</section>
@endsection
