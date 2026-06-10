<!DOCTYPE html>
<html>
<head>
    <title>Pay with M-PESA</title>
</head>
<body>
<h2>Make Payment</h2>
<input type="text" id="phone" placeholder="2547XXXXXXXX">
<input type="number" id="amount" placeholder="Amount">
<button onclick="pay()">Pay Now</button>
<script>
function pay() {
    fetch('/stkpush', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            phone: document.getElementById('phone').value,
            amount: document.getElementById('amount').value
        })
    });
    alert("Check your phone for M-PESA prompt");
}
</script>
</body>
</html>
