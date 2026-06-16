</div><!-- end main -->
</div><!-- end layout -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="/assets/js/app.js"></script>
<script>
  // Auth guard — redirect if no token
  if (!localStorage.getItem('talemwa_token')) {
    window.location.href = '/auth/login.php';
  }
</script>
</body>
</html>
