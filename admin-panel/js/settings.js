
    
// Profile photo preview
document.getElementById('profilePhotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePhotoPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + 'Icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password strength checker
document.getElementById('newPassword').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    const colors = ['#e74c3c', '#e67e22', '#f39c12', '#2ecc71', '#27ae60'];
    const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const widths = ['20%', '40%', '60%', '80%', '100%'];
    
    if (password.length > 0) {
        strengthBar.style.width = widths[strength - 1];
        strengthBar.style.background = colors[strength - 1];
        strengthText.textContent = 'Password strength: ' + texts[strength - 1];
        strengthText.style.color = colors[strength - 1];
    } else {
        strengthBar.style.width = '0%';
        strengthText.textContent = 'Password strength: None';
        strengthText.style.color = '#6c757d';
    }
});

// Password match checker
document.getElementById('confirmPassword').addEventListener('input', function() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = this.value;
    const matchText = document.getElementById('passwordMatch');
    
    if (confirmPassword.length > 0) {
        if (newPassword !== confirmPassword) {
            matchText.style.display = 'block';
            this.classList.add('border-danger');
        } else {
            matchText.style.display = 'none';
            this.classList.remove('border-danger');
            this.classList.add('border-success');
        }
    } else {
        matchText.style.display = 'none';
        this.classList.remove('border-danger', 'border-success');
    }
});

// Profile form submission
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Add your AJAX call here
    alert('Profile updated successfully!');
    
    // Example: Uncomment when backend is ready
    // const formData = new FormData(this);
    // fetch('update-profile.php', {
    //     method: 'POST',
    //     body: formData
    // })
    // .then(response => response.json())
    // .then(data => {
    //     alert(data.message);
    // });
});

// Password form submission
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }
    
    // Add your AJAX call here
    alert('Password changed successfully!');
    this.reset();
    document.getElementById('strengthBar').style.width = '0%';
    
    // Example: Uncomment when backend is ready
    // const formData = new FormData(this);
    // fetch('change-password.php', {
    //     method: 'POST',
    //     body: formData
    // })
    // .then(response => response.json())
    // .then(data => {
    //     alert(data.message);
    //     if(data.success) this.reset();
    // });
});

function resetForm() {
    document.getElementById('profileForm').reset();
}
