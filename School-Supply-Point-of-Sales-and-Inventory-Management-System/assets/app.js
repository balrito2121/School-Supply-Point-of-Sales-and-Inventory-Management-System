document.addEventListener('click', function (event) {
    const toggle = event.target.closest('[data-toggle-password]');
    if (!toggle) {
        return;
    }

    const input = document.getElementById(toggle.getAttribute('aria-controls'));
    if (!input) {
        return;
    }

    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    toggle.textContent = isHidden ? 'Hide' : 'Show';
    toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    toggle.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
});

const logoutDialog = document.getElementById('logoutDialog');
let logoutForm = null;

document.addEventListener('submit', function (event) {
    const form = event.target.closest('form[data-logout-form]');
    if (!form) {
        return;
    }

    if (form.dataset.confirmed === 'true') {
        delete form.dataset.confirmed;
        return;
    }

    event.preventDefault();
    if (!logoutDialog || typeof logoutDialog.showModal !== 'function') {
        if (window.confirm('Are you sure you want to sign out?')) {
            form.submit();
        }
        return;
    }

    logoutForm = form;
    logoutDialog.showModal();
});

const cancelLogout = document.getElementById('cancelLogout');
if (cancelLogout) {
    cancelLogout.addEventListener('click', function () {
        logoutDialog.close();
        logoutForm = null;
    });
}

const confirmLogout = document.getElementById('confirmLogout');
if (confirmLogout) {
    confirmLogout.addEventListener('click', function () {
        if (!logoutForm) {
            return;
        }

        logoutForm.dataset.confirmed = 'true';
        logoutForm.requestSubmit();
    });
}
