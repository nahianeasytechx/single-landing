


    // Show/hide button text field and activity preview based on selection
    $('#linked_activity').on('change', function() {
        const selectedValue = $(this).val();
        const selectedText = $(this).find('option:selected').text();
        
        if (selectedValue) {
            $('#buttonTextSection').slideDown();
            $('#activityPreview').slideDown();
            $('#selectedActivityName').text(selectedText);
        } else {
            $('#buttonTextSection').slideUp();
            $('#activityPreview').slideUp();
            $('#button_text').val('');
        }
    });

    // Character counter for description
    $('#slider_description').on('input', function() {
        const length = $(this).val().length;
        $('#charCount').text(length);
        
        if (length > 230) {
            $('#charCount').addClass('text-danger').removeClass('text-muted');
        } else {
            $('#charCount').addClass('text-muted').removeClass('text-danger');
        }
    });

    // Image preview
    $('#file').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').slideDown();
            };
            reader.readAsDataURL(file);
        }
    });


function clearActivity() {
    $('#linked_activity').val('').trigger('change');
    $('#buttonTextSection').slideUp();
    $('#activityPreview').slideUp();
}

function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this slider image? This action cannot be undone.')) {
        alert('Slider image deleted successfully!');
        // Uncomment when backend is ready:
        // window.location.href = 'delete-slider.php?id=' + id;
    }
}

function editSlider(id) {
    window.location.href = 'edit-slider.php?id=' + id;
}
