@extends('layouts.app')

@section('title', 'Workout Plans - Fitzone')

@section('content')
<h1>Workout Plans</h1>
<div class="grid two">
    <section class="card">
        <h2>Create Plan</h2>
        <form>
            <div class="form-grid">
                <label>Select Member<select><option>Amit Verma</option><option>Neha Gupta</option><option>Rahul Sharma</option></select></label>
                <label>Exercise Name<input placeholder="Bench Press"></label>
                <label>Sets<input placeholder="4"></label>
                <label>Reps<input placeholder="12"></label>
            </div>
            <label style="margin-top:12px">Notes<textarea placeholder="Keep shoulders locked and control the movement."></textarea></label>
            <div class="actions"><button class="btn ghost" type="button">Cancel</button><button class="btn" type="button">Save Plan</button></div>
        </form>
    </section>
    <section class="card">
        <h2>Current Plan Preview</h2>
        <table><tbody>
            <tr><td>Bench Press</td><td>4 sets</td><td>12 reps</td></tr>
            <tr><td>Incline Dumbbell Press</td><td>4 sets</td><td>12 reps</td></tr>
            <tr><td>Push Ups</td><td>3 sets</td><td>15 reps</td></tr>
        </tbody></table>
    </section>
</div>
@endsection
