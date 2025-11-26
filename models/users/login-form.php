<form action="/api/users/login.php" method="POST" class="user__login-form">
    <label>
        <b>Email:</b>
        <input type="email" name="email" required>
    </label>
    <label>
        <b>Password:</b>
        <input type="password" name="pass" required>
    </label>
    <div class="user__login-btns">
        <button type="submit" class="btn-primary">Enter</button>
        <a href="/models/users/reg-form.php" class="btn-secondary">Registration</a>
    </div>
</form>
