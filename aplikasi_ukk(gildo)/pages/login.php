<?php if(!empty($error)) : ?>
<div class="alert alert-danger">
   <?= $error ?>
</div>
<?php endif; ?>

<div class="container d-flex justify-content-center align-items-center" style="height:100vh">
  <div class="card p-4 shadow-lg" style="width:400px;border-top:6px solid #0d6efd">
    <h3 class="text-center fw-bold text-primary mb-4">LOGIN</h3>
    <form method="POST">
      <div class="mb-3">
        <label class="small fw-bold">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-4">
        <label class="small fw-bold">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button name="login" class="btn btn-primary w-100">MASUK</button>
    </form>
  </div>
</div>
