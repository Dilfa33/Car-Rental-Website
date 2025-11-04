<!-- Registration View -->
<div class="auth-container d-flex align-items-center justify-content-center min-vh-100 bg-light">
  <div class="card auth-card shadow-lg" style="max-width: 500px; width: 100%;">
    <div class="card-body p-4 p-md-5">
      <h2 class="text-center mb-4">Create Account</h2>
      <form id="registerForm">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Phone Number</label>
          <input type="tel" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Driver's License Number</label>
          <input type="text" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" required>
        </div>
        <div class="mb-4">
          <label class="form-label">Confirm Password</label>
          <input type="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register Now</button>
        <p class="text-center mt-3 mb-0">
          Already have an account? <a href="#login">Login here</a>
        </p>
      </form>
    </div>
  </div>
</div>
