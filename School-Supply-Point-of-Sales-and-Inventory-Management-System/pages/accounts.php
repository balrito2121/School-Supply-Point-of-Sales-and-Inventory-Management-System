<?php
$accounts = $store['users'];
?>
<div class="page-intro">
    <div>
        <h2>Registered staff accounts</h2>
        <p>Create the accounts administrators, cashiers, and staff use to sign in.</p>
    </div>
    <div class="user-page-actions">
        <span class="count-badge"><?php echo count($accounts); ?> accounts</span>
        <button class="button button-primary" type="button" id="openAccountModal"><?php echo icon('plus'); ?>Add account</button>
    </div>
</div>
<dialog class="account-modal" id="accountModal" aria-labelledby="accountModalTitle">
    <div class="modal-heading">
        <div><h3 id="accountModalTitle">Create staff account</h3><p>Share the login details with the new staff member.</p></div>
        <button class="modal-close" type="button" id="closeAccountModal" aria-label="Close dialog"><?php echo icon('close'); ?></button>
    </div>
    <form method="post" class="user-create-form">
        <input type="hidden" name="csrf" value="<?php echo e($_SESSION['csrf']); ?>">
        <input type="hidden" name="action" value="create_user">
        <label>Full name<input name="name" type="text" maxlength="100" required autocomplete="name" placeholder="e.g. Alex Santos"></label>
        <label>Username<input name="username" type="text" minlength="3" maxlength="30" pattern="[A-Za-z0-9._-]+" required autocomplete="off" placeholder="e.g. alex.santos"></label>
        <div class="user-password-row">
            <label>Password<div class="password-control"><input id="newStaffPassword" name="password" type="password" minlength="8" required autocomplete="new-password" placeholder="At least 8 characters"><button class="password-toggle" type="button" data-toggle-password aria-controls="newStaffPassword" aria-label="Show password" aria-pressed="false">Show</button></div></label>
            <label>Confirm password<div class="password-control"><input id="confirmStaffPassword" name="confirm_password" type="password" minlength="8" required autocomplete="new-password" placeholder="Repeat password"><button class="password-toggle" type="button" data-toggle-password aria-controls="confirmStaffPassword" aria-label="Show password" aria-pressed="false">Show</button></div></label>
        </div>
        <label>Account role<select name="role" required><option value="cashier">Cashier</option><option value="staff">Staff</option></select></label>
        <p class="form-help">Passwords are stored securely as hashes. Share the sign-in details with the new staff member.</p>
        <div class="form-actions">
            <button class="button button-quiet" type="button" id="cancelAccountModal">Cancel</button>
            <button class="button button-primary" type="submit">Create account</button>
        </div>
    </form>
</dialog>
<section class="panel account-list-panel">
        <div class="panel-heading">
            <div><h3>Registered accounts</h3><p>Existing accounts that can sign in to the system.</p></div>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>NAME</th><th>USERNAME</th><th>ROLE</th></tr></thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                        <tr>
                            <td><div class="table-product"><span class="product-mini"><?php echo e(strtoupper(substr($account['name'], 0, 1))); ?></span><div><strong><?php echo e($account['name']); ?></strong></div></div></td>
                            <td><?php echo e($account['username']); ?></td>
                            <td><span class="role-badge role-<?php echo e($account['role']); ?>"><?php echo e(ucfirst($account['role'])); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($accounts) === 0): ?><tr><td colspan="3" class="table-empty">No user accounts have been created yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
</section>
<script>
    const accountModal = document.getElementById('accountModal');
    document.getElementById('openAccountModal').addEventListener('click', function () {
        accountModal.showModal();
    });
    document.getElementById('closeAccountModal').addEventListener('click', function () {
        accountModal.close();
    });
    document.getElementById('cancelAccountModal').addEventListener('click', function () {
        accountModal.close();
    });
</script>
