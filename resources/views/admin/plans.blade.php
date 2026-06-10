@extends('layouts.app')

@section('title', 'Membership Plans - Fitzone')

@section('content')
<h1>Membership Plans</h1>
<section class="card">
    <div class="plans">
        <article class="plan"><h2>Basic Plan</h2><div class="price">INR 1,000 <small>/ month</small></div><p class="muted">30 days access</p><p>Gym access<br>Basic equipment<br>1 day pass</p><button class="btn">Activate / Edit</button></article>
        <article class="plan"><h2>Standard Plan</h2><div class="price">INR 2,000 <small>/ month</small></div><p class="muted">30 days access</p><p>Cardio access<br>Trainer check-in<br>3 day pass</p><button class="btn" style="background:var(--green)">Activate / Edit</button></article>
        <article class="plan featured"><h2>Premium Plan</h2><div class="price">INR 3,500 <small>/ month</small></div><p class="muted">30 days access</p><p>All equipment<br>Personal training<br>Nutrition guide</p><button class="btn" style="background:var(--amber)">Activate / Edit</button></article>
    </div>
</section>
@endsection
