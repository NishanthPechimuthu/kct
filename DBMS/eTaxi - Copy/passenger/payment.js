
document.addEventListener('DOMContentLoaded', () => {
    const paymentForm = document.getElementById('payment-form');
    const paymentStatus = document.getElementById('payment-status');
    const urlParams = new URLSearchParams(window.location.search);
    const paymentId = urlParams.get('payment_id');
    const amount = urlParams.get('amount');
    const description = urlParams.get('description');

    if (!paymentId || !amount) {
        paymentStatus.innerHTML = '<p class="text-red-500">Invalid payment link.</p>';
        return;
    }

    document.getElementById('payment-amount').textContent = `₹${amount}`;
    document.getElementById('payment-description').textContent = `For: ${description || 'Your Ride'}`;

    paymentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const loadingSpinner = document.getElementById('loading-spinner');

        try {
            paymentStatus.innerHTML = '<p class="text-blue-500">Processing payment...</p>';
            loadingSpinner.classList.remove('hidden');

            const response = await axios.post('/api/payments.php', {
                payment_id: paymentId,
                method: paymentMethod,
                amount: amount,
                description: description
            });

            if (response.data.success) {
                loadingSpinner.classList.add('hidden');
                paymentStatus.innerHTML = '<p class="text-green-500">Payment successful!</p>';
                setTimeout(() => {
                    window.location.href = '/passenger/dashboard.php';
                }, 2000);
            } else {
                throw new Error(response.data.message);
            }
        } catch (error) {
            loadingSpinner.classList.add('hidden');
            paymentStatus.innerHTML = `<p class="text-red-500">Payment failed: ${error.message}</p>`;
        }
    });
});
