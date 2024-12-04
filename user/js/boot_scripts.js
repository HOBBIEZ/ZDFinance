// Function to show the forgot password form
function showForgotPassword() {
    document.getElementById("forgotForm").classList.remove("d-none");
    document.getElementById("loginForm").classList.add("d-none");
}

// Function to show the login form again
function showLogin() {
    document.getElementById("forgotForm").classList.add("d-none");
    document.getElementById("loginForm").classList.remove("d-none");
}

document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const forgotForm = document.getElementById("forgotForm");
  const loginError = document.getElementById("loginError");
  const forgotError = document.getElementById("forgotError");
  const forgotSuccess = document.getElementById("forgotSuccess");
  const emailForm = document.getElementById("emailForm");
  const signupForm = document.getElementById("signupForm");
  const signupError = document.getElementById("signupError");
  const loginButtons = document.querySelectorAll('.login-buttons');
  const accountButton = document.querySelector('.account-button');
  
  // Check if the user is already logged in
  fetch("../php/index.php", {
      method: "POST",
      body: new URLSearchParams({ action: "checkSession" })
  })
  .then((response) => response.json())
  .then((data) => {
      if (data.success && data.loggedIn) {
        loginButtons.forEach(button => button.classList.add('d-none'));
        accountButton.classList.remove('d-none');
      } else {
        loginButtons.forEach(button => button.classList.remove('d-none'));
        accountButton.classList.add('d-none');
      }
  })
  .catch((error) => console.error("Error checking session:", error));
  
  // Select all toggle-password buttons
  const togglePasswordButtons = document.querySelectorAll('.toggle-password');
  togglePasswordButtons.forEach((button) => {
    button.addEventListener('click', () => {
      // Find the sibling input element (password field)
      const input = button.parentElement.querySelector('input');
      const icon = button.querySelector('i');

      // Toggle the input type between password and text
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
  });

  // Hide error message on input on loginForm
  loginForm.querySelectorAll("input").forEach((input) => {
    input.addEventListener("input", () => {
      if (!loginError.classList.contains("d-none")) {
        loginError.classList.add("d-none");
      }
    });
  });

  // Hide error message on input on forgotForm
  forgotForm.querySelectorAll("input").forEach((input) => {
    input.addEventListener("input", () => {
      if (!forgotError.classList.contains("d-none")) {
        forgotError.classList.add("d-none");
      }
    });
  });

  // Hide error message on input on signupForm
  signupForm.querySelectorAll("input").forEach((input) => {
    input.addEventListener("input", () => {
      if (!signupError.classList.contains("d-none")) {
        signupError.classList.add("d-none");
      }
    });
  });
  
  // Login form submission
  loginForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(loginForm);
    formData.append("action", "login");

    fetch("../php/index.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          window.location.href = "../pages/account_page.php";
        } else {
          loginError.textContent = data.error;
          loginError.classList.remove("d-none");
        }
      })
      .catch((error) => console.error("Error:", error));
  });

  // Forgot password form submission
  forgotForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Get the submit button
    const submitButton = forgotForm.querySelector('button[type="submit"]');
    
    // Disable the submit button immediately to prevent further clicks
    submitButton.disabled = true;
    submitButton.textContent = "Submitting...";
    
    const formData = new FormData(forgotForm);
    formData.append("action", "forgot");

    fetch("../php/index.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          emailForm.classList.add("d-none");
          forgotError.classList.add("d-none");
          forgotSuccess.textContent = `Check your email for the password reset form.`;
          forgotSuccess.classList.remove("d-none");

          const submitButton = forgotForm.querySelector('button[type="submit"]');
          submitButton.classList.add("d-none");
        } else {
          forgotError.textContent = data.error;
          forgotError.classList.remove("d-none");
          forgotSuccess.classList.add("d-none");
        }
      })
      .catch((error) => console.error("Error:", error));
  });

  // Signup form submission
  signupForm.addEventListener('submit', function (e) {
    e.preventDefault();
  
    const formData = new FormData(signupForm);
    formData.append("action", "signup");
    
    fetch("../php/index.php", {
      method: "POST", 
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          window.location.href = "../pages/account_page.php";
        } else {
          signupError.textContent = data.error;
          signupError.classList.remove("d-none");
        }
      })
      .catch((error) => console.error("Error:", error));
  });
});
  