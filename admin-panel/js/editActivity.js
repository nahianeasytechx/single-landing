
// Use unique function names to avoid conflicts
var activitySectionCounter = 2;

function addNewItemDynamic(button) {
    var section = button.closest('.dynamic-section');
    var sectionId = section.getAttribute('data-section-id');
    var itemsList = section.querySelector('.items-list');
    
    var newItem = document.createElement('div');
    newItem.className = 'item-row mb-2';
    newItem.innerHTML = '<div class="input-group">' +
        '<span class="input-group-text"><i class="fa-solid fa-circle-check text-success"></i></span>' +
        '<input type="text" class="form-control" name="section_' + sectionId + '_items[]" placeholder="Enter item text">' +
        '<button type="button" class="btn btn-outline-danger" onclick="removeItemDynamic(this)">' +
        '<i class="fa-solid fa-trash"></i>' +
        '</button>' +
        '</div>';
    
    itemsList.appendChild(newItem);
    newItem.querySelector('input').focus();
}

function removeItemDynamic(button) {
    var itemRow = button.closest('.item-row');
    var itemsList = itemRow.closest('.items-list');
    
    if (itemsList.querySelectorAll('.item-row').length <= 1) {
        alert('Section must have at least one item');
        return;
    }
    
    if (confirm('Are you sure you want to remove this item?')) {
        itemRow.remove();
    }
}

function addNewSectionDynamic() {
    activitySectionCounter++;
    var container = document.getElementById('dynamicSectionsContainer');
    
    var newSection = document.createElement('div');
    newSection.className = 'card shadow-sm mb-4 dynamic-section';
    newSection.setAttribute('data-section-id', activitySectionCounter);
    newSection.innerHTML = '<div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">' +
        '<div class="d-flex align-items-center gap-2 flex-grow-1">' +
        '<i class="fa-solid fa-list"></i>' +
        '<input type="text" class="form-control form-control-sm section-title-input" name="section_title_' + activitySectionCounter + '" value="" placeholder="Enter section title (e.g., Requirements, Procedures)" style="max-width: 300px;">' +
        '</div>' +
        '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSectionDynamic(this)">' +
        '<i class="fa-solid fa-trash"></i> Remove Section' +
        '</button>' +
        '</div>' +
        '<div class="card-body p-4">' +
        '<div class="mb-3">' +
        '<label class="form-label">Items</label>' +
        '<div class="items-list">' +
        '<div class="item-row mb-2">' +
        '<div class="input-group">' +
        '<span class="input-group-text"><i class="fa-solid fa-circle-check text-success"></i></span>' +
        '<input type="text" class="form-control" name="section_' + activitySectionCounter + '_items[]" placeholder="Enter item text">' +
        '<button type="button" class="btn btn-outline-danger" onclick="removeItemDynamic(this)">' +
        '<i class="fa-solid fa-trash"></i>' +
        '</button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<button type="button" class="btn btn-outline-primary btn-sm" onclick="addNewItemDynamic(this)">' +
        '<i class="fa-solid fa-plus"></i> Add New Item' +
        '</button>' +
        '</div>';
    
    container.appendChild(newSection);
    newSection.querySelector('.section-title-input').focus();
}

function removeSectionDynamic(button) {
    var section = button.closest('.dynamic-section');
    var container = document.getElementById('dynamicSectionsContainer');
    
    if (container.querySelectorAll('.dynamic-section').length <= 1) {
        alert('You must have at least one section');
        return;
    }
    
    if (confirm('Are you sure you want to remove this entire section?')) {
        section.remove();
    }
}

// Remove existing image
function removeExistingImage(button) {
    if (confirm('Are you sure you want to remove this image?')) {
        var imageCol = button.closest('.col-md-4');
        imageCol.remove();
    }
}

// Remove existing file
function removeExistingFile(button) {
    if (confirm('Are you sure you want to remove this file?')) {
        var fileItem = button.closest('.file-item');
        fileItem.remove();
    }
}

// Preview new images before upload
function previewNewImages(input) {
    var previewContainer = document.getElementById('newImagesPreview');
    previewContainer.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        for (var i = 0; i < input.files.length; i++) {
            var file = input.files[i];
            
            if (file.type.match('image.*')) {
                var reader = new FileReader();
                
                reader.onload = (function(file) {
                    return function(e) {
                        var col = document.createElement('div');
                        col.className = 'col-md-4';
                        col.innerHTML = '<div class="image-item position-relative">' +
                            '<img src="' + e.target.result + '" class="img-fluid rounded" alt="New Image">' +
                            '<div class="position-absolute top-0 start-0 m-2 badge bg-success">New</div>' +
                            '</div>';
                        previewContainer.appendChild(col);
                    };
                })(file);
                
                reader.readAsDataURL(file);
            }
        }
    }
}
