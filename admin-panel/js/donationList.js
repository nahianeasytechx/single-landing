function viewDonation(id) {
    window.location.href = 'view-donation-fund.php?id=' + id;
}

function editDonation(id) {
    window.location.href = 'edit-donation-category?id=' + id;
}

function deleteDonation(id) {
    if (confirm('Are you sure you want to delete this donation record?')) {
        alert('Donation #' + id + ' deleted successfully!');
    }
}
