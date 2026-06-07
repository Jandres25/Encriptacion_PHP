document.addEventListener('click', function (event) {
    const deleteButton = event.target.closest('.js-delete-user');
    if (!deleteButton) {
        return;
    }

    const id = deleteButton.getAttribute('data-id');
    const csrf = deleteButton.getAttribute('data-csrf');
    const name = deleteButton.getAttribute('data-name') || '';
    const username = deleteButton.getAttribute('data-username') || '';

    if (!id) {
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
        if (!result.isConfirmed) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'post';
        form.action = `${APP_URL}/users/delete`;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_csrf';
        csrfInput.value = csrf;

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;

        form.appendChild(csrfInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    });
});
