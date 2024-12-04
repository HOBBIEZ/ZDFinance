<?php
include('../php/db_connection.php');
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: boot.html");
    exit();
} else {
  $stmt = $conn->prepare("SELECT Status FROM Users WHERE Username = ?");
  $stmt->bind_param('s', $_SESSION['user']);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($status);
  $stmt->fetch();
  if ($status === 'deleted') {
    header("Location: ../php/logout.php");
    exit();
  }           
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account - ZDFinance</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand text-white" href="boot.html">ZDFinance</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto"></ul>
        <a href="#" class="btn btn-outline-light btn-sm login-buttons" data-bs-toggle="modal" data-bs-target="#settingsModal">Settings</a>
        <a href="../php/logout.php" class="btn btn-primary btn-sm login-buttons">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container">
      <h2 id="welcome" class="display-4 fw-bold"></h2>
      <p class="lead">Here's an overview of your accounts and recent activity.</p>
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newAccountModal">Open New Account</button>
    </div>
  </section>

  <!-- Account Summary Section -->
  <section class="py-5">
    <div class="container">
      <div class="row" id="account-summary"></div>

      <!-- Recent Transactions Section -->
      <div class="row mt-5" id="transactions-summary"></div>
    </div>
  </section>

  <!-- Settings Modal -->
  <div class="modal fade" id="settingsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Account Settings</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="settingsForm">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" name="username">
            </div>
            <div class="mb-3">
              <label class="form-label">First Name</label>
              <input type="text" class="form-control" name="first_name">
            </div>
            <div class="mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" class="form-control" name="last_name">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email">
            </div>
            <div class="mb-3">
              <label class="form-label">Phone Number</label>
              <input type="text" class="form-control" name="phone">
            </div>
            <div class="mb-3">
              <label class="form-label">Address</label>
              <input type="text" class="form-control" name="address">
            </div>
            <div id="settingsError" class="text-danger mb-3 d-none"></div>
            <button type="submit" class="btn btn-primary w-100">Save Changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- New Account Modal -->
  <div class="modal fade" id="newAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Open New Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="newAccountForm">
            <div class="mb-3">
            <label class="form-label">Account Name</label>
            <input type="text" name="account_name" class="form-control" maxlength="32" placeholder="Enter an account name" required>
            </div>
            <div id="accountError" class="text-danger mb-3 d-none"></div>
            <button type="submit" class="btn btn-primary w-100">Open Account</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Card Modal -->
  <div class="modal fade" id="addCardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCardModalLabel">Add Card</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="addCardForm">
            <div class="mb-3">
              <label for="card-pin" class="form-label">PIN</label>
              <!-- Updated input with pattern and min/max attributes -->
              <input type="text" class="form-control" id="card-pin" required 
                    pattern="^\d{4}$" maxlength="4" minlength="4" 
                    title="PIN must be a 4-digit number (0000 to 9999)" />
            </div>
            <button type="submit" class="btn btn-primary">Add Card</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Deposit Modal -->
  <div class="modal fade" id="depositModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="depositModalLabel">Deposit Funds</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <form id="depositForm">
                  <div class="mb-3">
                    <label>Amount to Deposit</label>
                    <input type="number" id="depositAmount" class="form-control" required min="10"/>
                  </div>
                  <button type="submit" class="btn btn-primary">Deposit</button>
                </form>
              </div>
          </div>
      </div>
  </div>

  <!-- Withdraw Modal -->
  <div class="modal fade" id="withdrwalModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="withdrwalModalLabel">Withdraw Funds</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <form id="withdrwalForm">
                  <div class="mb-3">
                    <label>Amount to Withdraw</label>
                    <input type="number" id="withdrwalAmount" class="form-control" required min="10"/>
                  </div>
                  <button type="submit" class="btn btn-primary">Withdraw</button>
                </form>
              </div>
          </div>
      </div>
  </div>

  <!-- New Transaction Modal -->
  <div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="transactionModallLabel">Make a Transaction</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="transactionForm">
            <div class="mb-3">
              <label class="form-label">Receiver's IBAN</label>
              <input type="text" id="receivers_iban" class="form-control" required pattern="\d{16}"/>
            </div>
            <div class="mb-3">
              <label class="form-label">Amount to sent</label>
              <input type="number" id="transaction_amount" class="form-control" required min="10">
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <input type="text" class="form-control" id="transaction_description">
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit Transaction</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Card Transaction Modal -->
  <div class="modal fade" id="nfcModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="nfcModalLabel">NFC Transaction</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <form id="cardSendForm">
                <div class="modal-body d-flex justify-content-center align-items-center" style="height: 200px;">
                    <img src="nfc.png" alt="NFC Icon" style="max-width: 100px; max-height: 100px;"> <!-- NFC Icon -->
                    <p class="text-center fw-semibold fs-5 mt-3">Get close to Receiver's NFC.</p>
                </div>
              </form>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
          </div>
      </div>
  </div>


  <!-- Footer -->
  <footer class="text-center py-3 bg-dark text-white">
    <p class="mb-0">&copy; 2024 ZDFinance. All rights reserved.</p>
  </footer>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../js/account_scripts.js"></script>
</body>
</html>
