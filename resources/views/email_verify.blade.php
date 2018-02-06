Hi {{ $name }}. Thank you for registering at <?= sitename; ?>
Your password is {{ $password }}

@if($requiresauth)
    <A HREF="<?= webroot('public/auth/login') . '?action=verify&code=' . $authcode; ?>">Click here to verify your email</A>
@endif

<?= view("email_test"); ?>