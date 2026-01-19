
document.addEventListener('DOMContentLoaded', function() {
    // Amount button functionality
    const amountButtons = document.querySelectorAll('.amount-btn:not(#otherBtn)');
    const otherBtn = document.getElementById('otherBtn');
    const donationInput = document.getElementById('donationAmount');

    if (amountButtons.length > 0 && donationInput) {
        amountButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('active'));

                // Add active class to clicked button
                this.classList.add('active');

                // Set the amount
                const amount = this.getAttribute('data-amount');
                donationInput.value = amount;
            });
        });
    }

    if (otherBtn && donationInput) {
        otherBtn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('active'));

            // Add active to Other button
            this.classList.add('active');

            // Clear and focus input
            donationInput.value = '';
            donationInput.focus();
        });
    }

    if (donationInput) {
        donationInput.addEventListener('input', function() {
            const currentValue = this.value;
            let matchFound = false;

            amountButtons.forEach(button => {
                if (button.getAttribute('data-amount') === currentValue) {
                    document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    matchFound = true;
                }
            });

            if (!matchFound && currentValue !== '') {
                document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('active'));
                if (otherBtn) {
                    otherBtn.classList.add('active');
                }
            }
        });
    }
});
