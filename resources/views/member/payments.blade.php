@extends('layouts.app')

@section('title', 'My Payments - Fitzone')

@section('content')
<h1>Payments</h1>
<section class="card">
    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px">
        <h2>Payment History</h2>
        <button class="btn">Pay Now</button>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Date</th><th>Plan</th><th>Amount</th><th>Method</th><th>Status</th><th>Invoice</th></tr></thead>
            <tbody>
                <tr><td>12 May 2024</td><td>Gold Plan</td><td>INR 5,000</td><td>UPI</td><td><span class="badge green">Paid</span></td><td><button class="btn ghost">Download</button></td></tr>
                <tr><td>12 Apr 2024</td><td>Gold Plan</td><td>INR 5,000</td><td>Card</td><td><span class="badge green">Paid</span></td><td><button class="btn ghost">Download</button></td></tr>
                <tr><td>12 Mar 2024</td><td>Gold Plan</td><td>INR 5,000</td><td>UPI</td><td><span class="badge green">Paid</span></td><td><button class="btn ghost">Download</button></td></tr>
            </tbody>
        </table>
    </div>
</section>
@endsection
