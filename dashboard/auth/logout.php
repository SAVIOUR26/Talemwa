<!DOCTYPE html>
<html>
<head><title>Logging out…</title></head>
<body>
<script>
  localStorage.removeItem('talemwa_token');
  localStorage.removeItem('talemwa_name');
  localStorage.removeItem('talemwa_role');
  window.location.href = '/auth/login.php';
</script>
</body>
</html>
