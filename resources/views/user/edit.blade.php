@extends('layouts.main')

@section('title', 'Edit User')

@section('content')
<div class="row g-4">
    <div class="col-sm-12 col-xl-6">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Edit User</h6>
            <form action="/edit-user/{{ $user->id }}" method="POST" id="editUserForm">
                @method('put')
                @csrf

                <!-- Nama -->
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" id="name">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div id="nameFeedback" class="valid-feedback d-none">Nama tersedia!</div>
                    <div id="nameErrorFeedback" class="invalid-feedback d-none">Nama sudah digunakan!</div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" id="email">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div id="emailFeedback" class="valid-feedback d-none">Email tersedia!</div>
                    <div id="emailErrorFeedback" class="invalid-feedback d-none">Email sudah digunakan!</div>
                    <div id="emailFormatError" class="invalid-feedback d-none">Format email salah (Missing @).</div>
                </div>

                <!-- Password Baru -->
<div class="mb-3">
    <label for="password" class="form-label">Password Baru</label>
    <div class="input-group">
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="editPassword">
        <button class="btn btn-outline-secondary" type="button" id="toggleEditPassword">
            <i class="fa fa-eye"></i>
        </button>
    </div>
    <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    <div id="editPasswordFeedback" class="valid-feedback d-none">Password memenuhi syarat!</div>
</div>

<!-- Konfirmasi Password -->
<div class="mb-3">
    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
    <div class="input-group">
        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="editPassword_confirmation">
        <button class="btn btn-outline-secondary" type="button" id="toggleEditConfirmPassword">
            <i class="fa fa-eye"></i>
        </button>
    </div>
    @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
    <div id="editConfirmPasswordFeedback" class="valid-feedback d-none">Password cocok!</div>
    <div id="editConfirmPasswordError" class="invalid-feedback d-none">Password tidak cocok!</div>
</div>
  <!-- Role (Select) -->
                <div class="form-floating mb-4">
                    <select name="role" class="form-select @error('role') is-invalid @enderror" id="role">
                        <option value="" disabled>Pilih Role</option>
                        <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>Petugas</option>
                        <option value="kasir" {{ old('role', $user->role) == 'kasir' ? 'selected' : '' }}>Kasir</option>
                    </select>
                    <label for="role"></label>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
            </form>
        </div>
    </div>
</div>
<script>
    function togglePassword(inputId, buttonId) {
        document.getElementById(buttonId).addEventListener('click', function () {
            let passwordField = document.getElementById(inputId);
            let icon = this.querySelector('i');

            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }

    togglePassword('password', 'togglePassword');
    togglePassword('password_confirmation', 'toggleConfirmPassword');
    togglePassword('editPassword', 'toggleEditPassword');
    togglePassword('editPassword_confirmation', 'toggleEditConfirmPassword');

    function validatePassword(inputId, feedbackId) {
        document.getElementById(inputId).addEventListener('input', function () {
            let password = this.value;
            let feedback = document.getElementById(feedbackId);
            let regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

            if (regex.test(password)) {
                this.classList.remove("is-invalid");
                this.classList.add("is-valid");
                feedback.classList.remove("d-none");
            } else {
                this.classList.remove("is-valid");
                this.classList.add("is-invalid");
                feedback.classList.add("d-none");
            }
        });
    }

    validatePassword('password', 'passwordFeedback');
    validatePassword('editPassword', 'editPasswordFeedback');

    function validateConfirmPassword(passwordId, confirmId, feedbackId, errorId) {
        document.getElementById(confirmId).addEventListener('input', function () {
            let password = document.getElementById(passwordId).value;
            let confirmPassword = this.value;
            let feedback = document.getElementById(feedbackId);
            let error = document.getElementById(errorId);

            if (password === confirmPassword && confirmPassword.length > 0) {
                this.classList.remove("is-invalid");
                this.classList.add("is-valid");
                feedback.classList.remove("d-none");
                error.classList.add("d-none");
            } else {
                this.classList.remove("is-valid");
                this.classList.add("is-invalid");
                feedback.classList.add("d-none");
                error.classList.remove("d-none");
            }
        });
    }

    validateConfirmPassword('password', 'password_confirmation', 'confirmPasswordFeedback', 'confirmPasswordError');
    validateConfirmPassword('editPassword', 'editPassword_confirmation', 'editConfirmPasswordFeedback', 'editConfirmPasswordError');
</script>

<!-- JavaScript Validation -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("password_confirmation");

    // Feedback divs
    const nameFeedback = document.getElementById("nameFeedback");
    const nameErrorFeedback = document.getElementById("nameErrorFeedback");
    const emailFeedback = document.getElementById("emailFeedback");
    const emailErrorFeedback = document.getElementById("emailErrorFeedback");
    const emailFormatError = document.getElementById("emailFormatError");
    const passwordFeedback = document.getElementById("passwordFeedback");
    const confirmPasswordFeedback = document.getElementById("confirmPasswordFeedback");
    const confirmPasswordError = document.getElementById("confirmPasswordError");

    // Validasi Nama (cek unik di database)
    nameInput.addEventListener("input", function () {
        fetch(`/check-username?name=${nameInput.value}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    nameInput.classList.add("is-invalid");
                    nameInput.classList.remove("is-valid");
                    nameFeedback.classList.add("d-none");
                    nameErrorFeedback.classList.remove("d-none");
                } else {
                    nameInput.classList.remove("is-invalid");
                    nameInput.classList.add("is-valid");
                    nameFeedback.classList.remove("d-none");
                    nameErrorFeedback.classList.add("d-none");
                }
            });
    });

    // Validasi Email (format + unik di database)
    emailInput.addEventListener("input", function () {
        const emailValue = emailInput.value;
        if (!emailValue.includes("@")) {
            emailInput.classList.add("is-invalid");
            emailFormatError.classList.remove("d-none");
            return;
        } else {
            emailFormatError.classList.add("d-none");
        }

        fetch(`/check-email?email=${emailValue}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    emailInput.classList.add("is-invalid");
                    emailInput.classList.remove("is-valid");
                    emailFeedback.classList.add("d-none");
                    emailErrorFeedback.classList.remove("d-none");
                } else {
                    emailInput.classList.remove("is-invalid");
                    emailInput.classList.add("is-valid");
                    emailFeedback.classList.remove("d-none");
                    emailErrorFeedback.classList.add("d-none");
                }
            });
    });

    // Validasi Password
    passwordInput.addEventListener("input", function () {
        const password = passwordInput.value;
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

        if (passwordPattern.test(password)) {
            passwordInput.classList.remove("is-invalid");
            passwordInput.classList.add("is-valid");
            passwordFeedback.classList.remove("d-none");
        } else {
            passwordInput.classList.remove("is-valid");
            passwordInput.classList.add("is-invalid");
            passwordFeedback.classList.add("d-none");
        }
    });

    // Validasi Konfirmasi Password
    confirmPasswordInput.addEventListener("input", function () {
        if (confirmPasswordInput.value === passwordInput.value && confirmPasswordInput.value !== "") {
            confirmPasswordInput.classList.remove("is-invalid");
            confirmPasswordInput.classList.add("is-valid");
            confirmPasswordFeedback.classList.remove("d-none");
            confirmPasswordError.classList.add("d-none");
        } else {
            confirmPasswordInput.classList.remove("is-valid");
            confirmPasswordInput.classList.add("is-invalid");
            confirmPasswordFeedback.classList.add("d-none");
            confirmPasswordError.classList.remove("d-none");
        }
    });
});
</script>
<!-- Custom CSS -->
<style>
    .form-select {
        padding: 0.6rem;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 1rem;
    }

    .form-select:focus {
        border-color: #007bff;
        box-shadow: 0px 0px 8px rgba(0, 123, 255, 0.5);
    }

    .form-floating select {
        background-color: #fff;
        font-size: 1rem;
    }
</style>
@endsection
