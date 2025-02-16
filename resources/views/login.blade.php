<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Login - Kasir.IDon</title>
  <!-- Custom fonts and styles -->
  <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
  <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
  <style>
    .is-invalid {
      border-color: #e3342f;
    }
  </style>
</head>
<body class="bg-gradient-primary">
  <div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">
      <div class="col-xl-10 col-lg-12 col-md-9 mt-5">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row justify-content-center">
              <div class="col-lg-6 d-none d-lg-block bg-login-image">
                <img src="{{ asset('img/Login.jpg') }}" alt="Login Image" style="width:100%; margin-top:3em;">
              </div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Login</h1>
                  </div>
                  @if(session('msg'))
                    <div class="alert alert-info">
                      {{ session('msg') }}
                    </div>
                  @endif
                  <!-- Alert umum untuk validasi -->
                  <div id="alert" class="alert alert-danger d-none"></div>
                  <form id="loginForm" method="POST" action="{{ url('/login') }}" class="user">
                    @csrf
                    <div class="form-group">
                      <input type="text" id="email" name="email" class="form-control form-control-user" placeholder="Masukkan Email...">
                      <small id="emailError" class="text-danger d-none"></small>
                    </div>
                    <div class="form-group">
                      <input type="password" id="password" name="password" class="form-control form-control-user" placeholder="Masukkan Password...">
                      <small id="passwordError" class="text-danger d-none"></small>
                    </div>
                    <button type="submit" class="btn btn-primary py-3 w-100 mb-4">Sign In</button>
                  </form>
                </div>
              </div>
            </div>
            <!-- End Row -->
          </div>
        </div>
      </div>
    </div>
    <!-- End Outer Row -->
  </div>

  <!-- Custom scripts -->
  <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
  <script>
    // Validasi real-time untuk input email
    document.getElementById('email').addEventListener('input', function() {
      const email = this.value.trim();
      const emailError = document.getElementById('emailError');
      if (!email) {
        emailError.innerHTML = 'Email tidak boleh kosong.';
        emailError.classList.remove('d-none');
        this.classList.add('is-invalid');
      } else if (!email.includes('@')) {
        emailError.innerHTML = 'Format email yang Anda masukkan tidak valid. Silakan periksa kembali.';
        emailError.classList.remove('d-none');
        this.classList.add('is-invalid');
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        emailError.innerHTML = 'Format email yang Anda masukkan tidak valid. Silakan periksa kembali.';
        emailError.classList.remove('d-none');
        this.classList.add('is-invalid');
      } else {
        emailError.classList.add('d-none');
        this.classList.remove('is-invalid');
      }
    });

    // Validasi real-time untuk input password
    document.getElementById('password').addEventListener('input', function() {
      const password = this.value;
      const passwordError = document.getElementById('passwordError');
      if (!password) {
        passwordError.innerHTML = 'Password tidak boleh kosong.';
        passwordError.classList.remove('d-none');
        this.classList.add('is-invalid');
      } else {
        passwordError.classList.add('d-none');
        this.classList.remove('is-invalid');
      }
    });

    // Validasi saat submit form
    document.getElementById('loginForm').addEventListener('submit', function(event) {
      event.preventDefault();
      const emailField = document.getElementById('email');
      const passwordField = document.getElementById('password');
      const email = emailField.value.trim();
      const password = passwordField.value;
      const alertBox = document.getElementById('alert');
      let hasError = false;

      // Reset pesan error dan tampilan validasi
      document.getElementById('emailError').classList.add('d-none');
      document.getElementById('passwordError').classList.add('d-none');
      emailField.classList.remove('is-invalid');
      passwordField.classList.remove('is-invalid');
      alertBox.classList.add('d-none');
      alertBox.innerHTML = '';

      // Skenario 1: Kedua field kosong
      if (!email && !password) {
        alertBox.innerHTML = 'Mohon lengkapi email dan password untuk melanjutkan.';
        alertBox.classList.remove('d-none');
        emailField.classList.add('is-invalid');
        passwordField.classList.add('is-invalid');
        hasError = true;
      }

      // Skenario 2: Salah satu field kosong
      if (!email || !password) {
        alertBox.innerHTML = 'Mohon lengkapi kedua kolom email dan password.';
        alertBox.classList.remove('d-none');
        if (!email) emailField.classList.add('is-invalid');
        if (!password) passwordField.classList.add('is-invalid');
        hasError = true;
      }

      // Skenario 3: Format email tidak valid
      if (email && (!email.includes('@') || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))) {
        alertBox.innerHTML = 'Format email yang Anda masukkan tidak valid. Silakan periksa kembali.';
        alertBox.classList.remove('d-none');
        emailField.classList.add('is-invalid');
        hasError = true;
      }

      if (hasError) return;

      // Jika validasi client-side lolos, submit form ke server
      this.submit();
    });
  </script>
</body>
</html>
