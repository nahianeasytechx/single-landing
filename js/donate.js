
        // Wait for the page to load completely
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, initializing donation form...');

            // Get all the elements
            const amountButtons = document.querySelectorAll('.amount-btn:not(#otherBtn)');
            const otherBtn = document.getElementById('otherBtn');
            const donationInput = document.getElementById('donationAmount');
            const form = document.getElementById('donationForm');
            const successMessage = document.getElementById('successMessage');
            const successText = document.getElementById('successText');

            console.log('Found buttons:', amountButtons.length);
            console.log('Found input:', donationInput ? 'Yes' : 'No');
            console.log('Found Other button:', otherBtn ? 'Yes' : 'No');

            // Add click event to each amount button
            amountButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    console.log('Button clicked:', this.getAttribute('data-amount'));

                    // Remove active class from ALL buttons
                    document.querySelectorAll('.amount-btn').forEach(function(btn) {
                        btn.classList.remove('active');
                    });

                    // Add active class to the clicked button
                    this.classList.add('active');

                    // Get the amount from data-amount attribute
                    const amount = this.getAttribute('data-amount');
                    
                    // Set the value in the input field
                    donationInput.value = amount;
                    
                    console.log('Amount set to:', amount);
                });
            });

            // Add click event to "Other" button
            if (otherBtn) {
                otherBtn.addEventListener('click', function() {
                    console.log('Other button clicked');

                    // Remove active from all buttons
                    document.querySelectorAll('.amount-btn').forEach(function(btn) {
                        btn.classList.remove('active');
                    });

                    // Make Other button active
                    this.classList.add('active');

                    // Clear the input and focus it
                    donationInput.value = '';
                    donationInput.focus();
                });
            }

            // When user types in the amount field
            donationInput.addEventListener('input', function() {
                const currentValue = this.value;
                console.log('Input changed to:', currentValue);
                
                let matchFound = false;

                // Check if the typed value matches any preset amount
                amountButtons.forEach(function(button) {
                    if (button.getAttribute('data-amount') === currentValue) {
                        // Remove active from all
                        document.querySelectorAll('.amount-btn').forEach(function(btn) {
                            btn.classList.remove('active');
                        });
                        // Make matching button active
                        button.classList.add('active');
                        matchFound = true;
                    }
                });

                // If no match and there's a value, activate "Other"
                if (!matchFound && currentValue !== '') {
                    document.querySelectorAll('.amount-btn').forEach(function(btn) {
                        btn.classList.remove('active');
                    });
                    if (otherBtn) {
                        otherBtn.classList.add('active');
                    }
                }
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const amount = donationInput.value;
                
                if (!amount || amount < 10) {
                    alert('Please enter a valid donation amount (minimum ৳10)');
                    donationInput.focus();
                    return;
                }

                // Show success message
                successText.textContent = `Thank you for your donation of ৳${amount}!`;
                successMessage.style.display = 'block';

                // Scroll to success message
                successMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                console.log('Form submitted with amount:', amount);
            });

            console.log('All event listeners attached successfully!');
        });
