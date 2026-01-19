<div class="mb-5 px-3">
    <div
        data-aos="fade-up"
        data-aos-delay="200"
        class="container ">


        <div class="row join-bg">

        <div class="row mx-auto py-2">
            <div class="section_title text-center">
                <h2 class="form-title-text text-uppercase"><b>Make Your Donation</b></h2>
                <div class="title-divider mx-auto mt-2"></div>
            </div>
        </div>

        <style>
            .title-divider {
                width: 60px; 
                height: 4px;               
                background-color:#ffffff; 
            }
            
            /* Payment method icon styling */
            .payment-method-card label {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .payment-icon {
                height: 30px;
                width: auto;
                max-width: 60px;
                object-fit: contain;
            }
        </style>

            
            <div class="row mx-auto pt-2 form-fields-row mt-5">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="course-lable">Donation Fund<span class="required-asterisk">*</span></label>
                                <select class="w-100 px-4  rounded-2 course_input  category_options" id="donationCategory" name="category">
                                    <option value="">-- Select a category --</option>
                                    <option value="mosque-fund">Mosque Project</option>
                                    <option value="madrasha-fund">Madrasha Project</option>
                                    <option value="school">School Project</option>
                                    <option value="hospital">Hospital Project</option>
                                    <option value="zakat">Zakatul Sadaka</option>
                                    <option value="disaster-relief">Disaster Relief</option>
                                    <option value="education-support">Education Support</option>
                                    <option value="orphan-homeless">Food & Financial Aid Support  For Orphan & Homeless</option>
                                    <option value="disaster-relief">Disaster Relief</option>
                                    <option value="tree-plantation">Plant A Tree</option>
                                    <option value="other">Other</option>
                                </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="course-lable">Mobile No. <span class="required-asterisk">*</span></label>
                        <input type="text" class="course_input" id="donorPhoneNumber" placeholder="Phone" required="required">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="course-lable">Amount <span class="required-asterisk">*</span></label>
                        <input type="text" class="course_input" id="donationAmountField" placeholder="Donation Amount" required="required">
                    </div>
                </div>
                <div class="col-md-3 button-col d-flex align-items-end">
                    <div class="form-group w-100">
                        <label for="name" class="donate-label-placeholder">Donate</label>
                        <button type="button" class="button w-100 donate-main-button border-0 text-white" id="openDonationModalBtn" style="border-radius: 10px; outline:none; cursor: pointer;">
                            <span>Donate Now</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class=" alert alert-success text-center mt-5 mx-auto bg-transparent text-white" role="alert">
                <ul>
                    <li>Your contribution can change lives — please select a category you wish to donate to.</li>
                </ul>
            </div>

        </div>
    </div>
</div>

<!-- Donation Modal -->
<div class="modal fade" id="completeDonationModal" tabindex="-1" aria-labelledby="completeDonationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content donation-modal-content">
            <div class="modal-header donation-modal-header">
                <h5 class="modal-title" id="completeDonationModalLabel">Complete Your Donation</h5>
                <button type="button" class="btn-close donation-modal-close" data-bs-dismiss="modal" aria-label="Close" id="closeDonationModalBtn"></button>
            </div>
            <div class="modal-body donation-modal-body">
                <form id="completeDonationForm">
                    <!-- Name Field -->
                    <div class="mb-3">
                        <label class="course-lable">Full Name <span class="required-asterisk">*</span></label>
                        <input type="text" class="course_input" id="donorFullName" placeholder="Enter your full name" required>
                    </div>

                    <!-- Email Field -->
                    <div class="mb-3">
                        <label class="course-lable">Email Address <span class="required-asterisk">*</span></label>
                        <input type="email" class="course_input" id="donorEmailAddress" placeholder="Enter your email" required>
                    </div>

                    <!-- Address Field -->
                    <div class="mb-3">
                        <label class="course-lable">Address <span class="required-asterisk">*</span></label>
                        <textarea class="course_input" id="donorFullAddress" rows="2" placeholder="Enter your address" required style="resize: none;"></textarea>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label class="course-lable">Payment Method <span class="required-asterisk">*</span></label>
                        
                        <div class="payment-method-card" onclick="selectPaymentMethod('sslcommerz')">
                            <input type="radio" name="donationPaymentMethod" id="sslcommerz" value="sslcommerz" required>
                            <label for="sslcommerz" style="cursor: pointer; margin: 0; font-weight: 600;">
                                <img src="images/ssl logo.png" alt="SSL Commerz" class="payment-icon">
                                <span>SSL Commerz</span>
                            </label>
                        </div>

                        <div class="payment-method-card" onclick="selectPaymentMethod('bkashPayment')">
                            <input type="radio" name="donationPaymentMethod" id="bkashPayment" value="bkash">
                            <label for="bkashPayment" style="cursor: pointer; margin: 0; font-weight: 600;">
                                <img src="images/bkash.png" alt="bKash" class="payment-icon">
                                <span>bKash</span>
                            </label>
                        </div>

                        <div class="payment-method-card" onclick="selectPaymentMethod('nagadPayment')">
                            <input type="radio" name="donationPaymentMethod" id="nagadPayment" value="nagad">
                            <label for="nagadPayment" style="cursor: pointer; margin: 0; font-weight: 600;">
                                <img src="images/nogod.png" alt="Nagad" class="payment-icon">
                                <span>Nagad</span>
                            </label>
                        </div>



                        <div class="payment-method-card" onclick="selectPaymentMethod('paypalPayment')">
                            <input type="radio" name="donationPaymentMethod" id="paypalPayment" value="paypal">
                            <label for="paypalPayment" style="cursor: pointer; margin: 0; font-weight: 600;">
                                <img src="images/paypal.png" alt="PayPal" class="payment-icon">
                                <span>PayPal</span>
                            </label>
                        </div>

                    </div>

                    <!-- Proceed Button -->
                    <button type="submit" class="cursor-pointer button w-100 donate-main-button border-0 text-white" style="border-radius: 10px; margin-top: 0.5rem;">
                        <span>Proceed</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* --- 1. GLOBAL VARIABLES & UTILITIES --- */
:root {
    --primary-color:#05A657;
    --primary-hover:#038d47;
    --text-color: #333;
    --label-color: #555;
    --border-color: #ced4da;
    --input-focus-shadow: 0 0 0 0.2rem rgba(0, 255, 47, 0.25);
    --shadow-light: 0 6px 20px rgba(0, 0, 0, 0.08); 
    --radius: 10px;
}

.join-bg {
    /* Mimics a modern "card" container */
    border-radius: var(--radius);
    box-shadow: var(--shadow-light);
    padding: 30px;
    margin: 20px 0;
}

.form-title-text {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--primary-color);
}

.required-asterisk {
    color: #dc3545;
}

/* --- 2. INPUT & LABEL STYLING (course-lable, course_input) --- */

/* Modern Label */
.course-lable {
    display: block;
    font-weight: 600;
    color: var(--label-color);
    margin-bottom: 0.3rem;
    font-size: 0.9rem;
}

/* Modern Input/Select (Your existing .course_input class) */
.course_input {
    width: 100%;
    padding: 0.6rem 0.85rem;
    font-size: 0.95rem;
    line-height: 1.4;
    color: var(--text-color);
    background-color: #fff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    transition: all 0.2s ease-in-out;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    min-height: 40px;
}

/* Input/Select Focus State */
.course_input:focus {
    border-color: var(--primary-color);
    outline: 0;
    box-shadow: var(--input-focus-shadow);
}
.cursor-pointer{
    cursor: pointer;
}


/* --- 3. BUTTON STYLING (button) --- */

/* Hide placeholder label for button column */
.donate-label-placeholder {
    visibility: hidden;
    height: 1.25rem; /* Reserve space for alignment */
    display: block;
}

/* The primary Donate Button */
.donate-main-button {
    /* Uses your existing .button class, just adding visual polish */
    height: calc(2.25rem + 18px); /* Match input height */
    background-color: var(--primary-color) !important;
    border-radius: var(--radius) !important;
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: background-color 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
}

.donate-main-button:hover {
    background-color: var(--primary-hover) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.donate-main-button:active {
    transform: translateY(0);
    box-shadow: none;
}

/* Remove old style overrides for button span */
.btn-margin .button span:first-child {
    padding: 0 !important;
    line-height: normal !important;
    white-space: normal !important;
}
.modal-dialog {
    max-width: 550px;
    margin: 1.75rem auto;
}
.modal-open .modal {
    overflow-x: hidden;
    overflow-y: auto;
}
.modal {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1050;
    display: none;
    overflow: hidden;
    outline: 0;
    background: #000000b3;
    opacity: 0;
}

/* --- 4. MODAL STYLING --- */
.donation-modal-content {
    border-radius: var(--radius);
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
}

.donation-modal-header {
    background-color: var(--primary-color);
    color: #fff;
    border-radius: var(--radius) var(--radius) 0 0;
    padding: 0.9rem 1.25rem;
    border-bottom: none;
    flex-shrink: 0;
}

.donation-modal-header .modal-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #fff;
}

.donation-modal-close {
    background-color: transparent;
    border: none;
    color: #fff;
    opacity: 1;
    font-size: 1.3rem;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    padding: 0;
}

.donation-modal-close:hover {
    opacity: 0.8;
}

.donation-modal-close::before {
    content: "×";
    font-size: 1.8rem;
    line-height: 1;
    color: #fff;
}

.donation-modal-body {
    padding: 1.25rem 1.5rem;
    overflow-y: auto;
    max-height: calc(85vh - 60px);
    flex: 1;
}

/* Custom scrollbar for modal body */
.donation-modal-body::-webkit-scrollbar {
    width: 8px;
}

.donation-modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.donation-modal-body::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 10px;
}

.donation-modal-body::-webkit-scrollbar-thumb:hover {
    background: var(--primary-hover);
}

/* Firefox scrollbar */
.donation-modal-body {
    scrollbar-width: thin;
    scrollbar-color: var(--primary-color) #f1f1f1;
}

/* Reduce margin between form fields */
#completeDonationForm .mb-3 {
    margin-bottom: 0.8rem !important;
}

/* --- 5. MODAL PAYMENT METHOD CARDS --- */
.payment-method-card {
    border: 2px solid var(--border-color);
    border-radius: 8px;
    padding: 0.7rem 0.9rem;
    margin-bottom: 0.7rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
}

.payment-method-card:hover {
    border-color: var(--primary-color);
    background-color: #f8f9fa;
}

.payment-method-card input[type="radio"] {
    margin-right: 10px;
    cursor: pointer;
    width: 16px;
    height: 16px;
}

.payment-method-card label {
    font-size: 0.95rem;
}

.payment-method-card.selected {
    border-color: var(--primary-color);
    background-color: #e8f5e9;
}

/* --- 6. RESPONSIVENESS (Mobile Focus) --- */

@media (max-width: 767px) {
    .join-bg {
        padding: 20px;
    }
    
    .form-title-text {
        font-size: 1.8rem;
    }
    
    /* Ensure proper spacing on mobile for the stacked columns */
    .form-fields-row > [class*="col-"] {
        margin-bottom: 1rem;
    }
    
    .button-col {
        margin-top: 0; /* Clear the alignment trick when stacked */
    }

    /* Adjust the button label visibility for mobile stacking */
    .donate-label-placeholder {
        visibility: hidden;
        height: 0;
        margin-bottom: 0;
    }
    
    .donate-main-button {
        height: 50px; /* Slightly easier to tap on mobile */
    }

    .donation-modal-body {
        padding: 1rem 1.25rem;
        max-height: calc(85vh - 55px);
    }
    
    .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .donation-modal-content {
        max-height: 90vh;
    }
}
</style>

<script>
// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    const openModalBtn = document.getElementById('openDonationModalBtn');
    const closeModalBtn = document.getElementById('closeDonationModalBtn');
    const donationForm = document.getElementById('completeDonationForm');
    const modalElement = document.getElementById('completeDonationModal');
    
    let donationModal;
    
    // Initialize Bootstrap modal if available
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        donationModal = new bootstrap.Modal(modalElement);
    }

    // Open modal when "Donate Now" is clicked
    if (openModalBtn) {
        openModalBtn.addEventListener('click', function() {
            const category = document.getElementById('donationCategory').value;
            const phone = document.getElementById('donorPhoneNumber').value;
            const amount = document.getElementById('donationAmountField').value;

            // Validation
            if (!category) {
                alert('Please select a donation category');
                return;
            }

            if (!phone) {
                alert('Please enter your mobile number');
                return;
            }

            if (!amount) {
                alert('Please enter donation amount');
                return;
            }

            // Open modal
            if (donationModal) {
                donationModal.show();
            } else {
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                document.body.classList.add('modal-open');
            }
        });
    }

    // Close modal manually
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            if (donationModal) {
                donationModal.hide();
            } else {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.body.classList.remove('modal-open');
            }
        });
    }

    // Handle form submission
    if (donationForm) {
        donationForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('donorFullName').value;
            const email = document.getElementById('donorEmailAddress').value;
            const address = document.getElementById('donorFullAddress').value;
            const paymentMethod = document.querySelector('input[name="donationPaymentMethod"]:checked');

            // Validation
            if (!name || !email || !address) {
                alert('Please fill in all fields');
                return;
            }

            if (!paymentMethod) {
                alert('Please select a payment method');
                return;
            }

            // Collect all data
            const donationData = {
                category: document.getElementById('donationCategory').value,
                phone: document.getElementById('donorPhoneNumber').value,
                amount: document.getElementById('donationAmountField').value,
                name: name,
                email: email,
                address: address,
                paymentMethod: paymentMethod.value
            };

            console.log('Donation Data:', donationData);

            // Here you would send data to backend
            alert('Proceeding to payment with ' + paymentMethod.value + '...');
            
            // Close modal
            if (donationModal) {
                donationModal.hide();
            } else {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.body.classList.remove('modal-open');
            }

            // Reset forms
            document.getElementById('donationCategory').value = '';
            document.getElementById('donorPhoneNumber').value = '';
            document.getElementById('donationAmountField').value = '';
            donationForm.reset();
        });
    }
});

// Handle payment method selection
function selectPaymentMethod(methodId) {
    // Remove selected class from all cards
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Add selected class to clicked card
    const selectedCard = document.getElementById(methodId).closest('.payment-method-card');
    if (selectedCard) {
        selectedCard.classList.add('selected');
    }

    // Check the radio button
    const radioBtn = document.getElementById(methodId);
    if (radioBtn) {
        radioBtn.checked = true;
    }
}
</script>