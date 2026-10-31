<script>
// Sends an admin action (delete, status toggle, ...) as a POST with the CSRF
// token. These routes no longer accept GET, so a link or image elsewhere on the
// web cannot trigger them for a logged-in admin.
function adminPost(url) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.style.display = 'none';
    var token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = @json(csrf_token());
    form.appendChild(token);
    document.body.appendChild(form);
    form.submit();
}
</script>
