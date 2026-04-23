document.addEventListener('click', function (event) {
    const deleteButton = event.target.closest('.js-delete-user');
    if (!deleteButton) {
        return;
    }

    const deleteUrl = deleteButton.getAttribute('data-delete-url');
    const name = deleteButton.getAttribute('data-name') || '';
    const username = deleteButton.getAttribute('data-username') || '';

    if (!deleteUrl) {
        return;
    }

    Swal.fire({
        title: 'Delete user?',
        text: `Name: ${name} | Username: ${username}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = deleteUrl;
        }
    });
});
