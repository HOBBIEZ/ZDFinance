document.addEventListener("DOMContentLoaded", function () {
    // Check if the user is already logged in
    fetch("../php/index.php", {
        method: "POST",
        body: new URLSearchParams({ action: "checkSession" })
    })
    .then((response) => response.json())
    .then((data) => {
        if (data.success && data.loggedIn) {
            document.getElementById("welcome").textContent = `Welcome, ${data.user}`;
            fetchUserData();
            fetchAccounts();
            fetchTransactions();
        } else {
            window.location.href = "../pages/boot.html"
        }
    })
    .catch((error) => console.error("Error checking session:", error));

    // Handle form submission to update user data
    document.getElementById("settingsForm").addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.forEach((value, key) => {
            console.log(key, value);
        });
        formData.append("action", "update_user");

        fetch('../php/index.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                settingsError.textContent = `Changes saved.`;
                settingsError.classList.remove("d-none");
                sleep(700).then(() => {window.location.href = "../pages/account_page.php"});
                
            } else {
                settingsError.textContent = data.error;
                settingsError.classList.remove("d-none");
            }
        })
        .catch(error => console.error('Error updating user data:', error));
    });

    document.getElementById("newAccountForm").addEventListener('submit', function(e) {
        e.preventDefault();
        const accountError = document.getElementById("accountError");
        const formData = new FormData(this);
        formData.append("action", "account");
    
        fetch('../php/index.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "../pages/account_page.php";
            } else {
                accountError.textContent = data.error;
                accountError.classList.remove("d-none");
            }
        })
        .catch((error) => console.error("Error:", error));
    });

    // Handle adding a card to an account
    document.getElementById('addCardForm').addEventListener('submit', function (e) {
        e.preventDefault();

        // Get the IBAN from the button that triggered the modal
        const iban = document.getElementById('addCardModal').dataset.iban;
        console.log("IBAN: ", iban); // Check if IBAN is correctly fetched
        const pin = document.getElementById('card-pin').value;

        // Send request to add card
        fetch('../php/add_card.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `iban=${iban}&pin=${pin}` // Use template literals for proper string interpolation
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal and refresh accounts
                window.location.href = "../pages/account_page.php";
            } else {
                alert(data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Handle deposit button click (check if the deposit button exists)
    document.getElementById('depositForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const depositAmount = parseFloat(document.getElementById('depositAmount').value);
        const iban = document.getElementById('depositModal').dataset.iban;
            
        if (isNaN(depositAmount) || depositAmount < 10) {
            alert('Please enter an amount of 10 or more.');
            return;
        }

        fetch('../php/external_transaction.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `iban=${iban}&amount=${depositAmount}&type=${'deposit'}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "../pages/account_page.php";
            } else {
                alert(data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Handle withdraw button click
    document.getElementById('withdrwalForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const withdrwalAmount = parseFloat(document.getElementById('withdrwalAmount').value);
        const iban = document.getElementById('withdrwalModal').dataset.iban;
            
        if (isNaN(withdrwalAmount) || withdrwalAmount < 10) {
            alert('Please enter an amount of 10 or more.');
            return;
        }

        fetch('../php/external_transaction.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `iban=${iban}&amount=${withdrwalAmount}&type=${'withdraw'}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "../pages/account_page.php";
            } else {
                alert(data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Handle Transaction
    document.getElementById('transactionForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const receivers_iban = parseInt(document.getElementById('receivers_iban').value);
        const iban = document.getElementById('transactionModal').dataset.iban;
        const transaction_amount = parseFloat(document.getElementById('transaction_amount').value);
        const transaction_description = document.getElementById('transaction_description').value;

        fetch('../php/internal_transaction.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `iban=${iban}&amount=${transaction_amount}&receivers_iban=${receivers_iban}&transaction_description=${transaction_description}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "../pages/account_page.php";
            } else {
                alert(data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Handle Card Sent Transaction
    document.getElementById('nfcModal').addEventListener('shown.bs.modal', function (e) {
        e.preventDefault();
        const iban = document.getElementById('nfcModal').dataset.iban;
        console.log(iban);
        // fetch('../php/nfc.php', {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/x-www-form-urlencoded'
        //     },
        //     body: `iban=${iban}&amount=${transaction_amount}&receivers_iban=${receivers_iban}&transaction_description=${transaction_description}`
        // })
        // .then(response => response.json())
        // .then(data => {
        //     if (data.success) {
        //         window.location.href = "../pages/account_page.php";
        //     } else {
        //         alert(data.error);
        //     }
        // })
        // .catch(error => console.error('Error:', error));
    });

    document.getElementById('transactionModal').addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var iban = button.getAttribute('data-iban'); // Extract IBAN
        this.setAttribute('data-iban', iban);
    });

    // Set the IBAN in the modal when the "Add Card" button is clicked
    document.getElementById('addCardModal').addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var iban = button.getAttribute('data-iban'); // Extract IBAN
        this.setAttribute('data-iban', iban);
    });    

    // Add modal show event to store IBAN for both deposit and withdraw
    document.getElementById('depositModal').addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var iban = button.getAttribute('data-iban'); // Extract IBAN
        this.setAttribute('data-iban', iban);
    });

    document.getElementById('withdrwalModal').addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var iban = button.getAttribute('data-iban'); // Extract IBAN
        this.setAttribute('data-iban', iban);
    });

    document.getElementById('nfcModal').addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var iban = button.getAttribute('data-iban'); // Extract IBAN
        this.setAttribute('data-iban', iban);
    });    

    // Handle deleting a card
    document.getElementById('account-summary').addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('delete-card')) {
            const cardNumber = e.target.dataset.cardNumber;
            const iban = e.target.dataset.iban;
            
            fetch('../php/delete_card.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `cardNumber=${cardNumber}&iban=${iban}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = "../pages/account_page.php";
                } else {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
});

function fetchTransactions() {
    fetch('../php/fetch_transactions.php')
    .then((response) => response.json())
    .then((data) => {
        const accountSummary = document.getElementById('transactions-summary');
        accountSummary.innerHTML = ''; // Clear current content

        if (data.error) {
            console.log('Error:', data.error);
            accountSummary.innerHTML = `<p class="text-center text-danger">Error: ${data.error}</p>`;
        } else if (data.length > 0) {
            accountSummary.innerHTML = `
                <h4 class="fw-bold">Transactions</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Source</th>
                                <th>Destination</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>`;
            
            const tableBody = accountSummary.querySelector('tbody');
            // Render transactions
            data.forEach((item) => {
                const isInternal = item.TransactionType === 'internal';
                const description = isInternal
                    ? (item.Description ? item.Description : 'No description')
                    : 'No description';
                const source = isInternal ? formatIBAN(item.Source) : (item.Type === 'withdraw' ? formatIBAN(item.Source) : 'External source');
                const destination = isInternal ? formatIBAN(item.Destination) : (item.Type === 'deposit' ? formatIBAN(item.Destination) : 'External destination');
                const badgeClass = (item.TransactionType === 'external') ? ((item.Type === 'withdraw') ? 'bg-danger' : 'bg-success') : ((item.Type === 'Sent') ? 'bg-danger' : 'bg-success');
                const amountBadge = `<span class="badge ${badgeClass}">${(badgeClass === 'bg-success') ? '+' : '-'} $${item.Amount}</span>`;
                
                tableBody.innerHTML += `
                    <tr>
                        <td>${item.Date}</td>
                        <td>${isInternal ? ((item.Type === 'Sent') ? 'Internal Sent' : 'Internal Received') : ((item.Type === 'withdraw') ? 'External Withdraw' : 'External Deposit')}</td>
                        <td>${amountBadge}</td>
                        <td>${source}</td>
                        <td>${destination}</td>
                        <td>${description}</td>
                    </tr>`;
            });
        } else {
            accountSummary.innerHTML = 
                `<h2 class="mb-4">Transactions</h2>
                <p class="ms-5 fw-semibold fs-5">No transactions yet...</p>`;
        }
    })
    .catch(error => console.error('Error fetching transactions:', error));
}

function fetchAccounts() {
    fetch('../php/fetch_accounts.php')
    .then((response) => response.json())
    .then((data) => {
        const accountSummary = document.getElementById('account-summary');
        accountSummary.innerHTML = ''; // Clear current content

        if (data.error) {
            console.log('Error:', data.error);
            accountSummary.innerHTML = `<p class="text-center text-danger">Error: ${data.error}</p>`;
        } else if (data.length > 0) {
            accountSummary.innerHTML = '<h2 class="mb-4">Accounts</h2>';

            // Render accounts and group cards under each account
            data.forEach(item => {
                // Check if account already rendered
                let account = document.querySelector(`[data-iban="${item.IBAN}"]`);

                if (!account) {
                    accountSummary.innerHTML += renderAccount(item);
                } 
            });

            // Add event listeners for delete buttons
            document.querySelectorAll('.delete-account').forEach(button => {
                button.addEventListener('click', (event) => {
                    const buttonElement = event.currentTarget;
                    const accountIBAN = buttonElement.dataset.accountIban;
                    deleteAccount(accountIBAN);
                });
            });
        } else {
            accountSummary.innerHTML = 
                `<h2 class="mb-4">Accounts</h2>
                 <p class="ms-5 fw-semibold fs-5">Open your account now!</p>`;
        }
    })
    .catch(error => console.error('Error fetching user data:', error));
}

// Helper function to render account and its associated cards
// Modify the renderAccount function to add deposit and withdraw buttons
function renderAccount(account) {
    let cardsHTML = '';
    if (account.Cards && account.Cards.length > 0) {
        cardsHTML += `<h6 class="fw-bold mt-3">Cards:</h6>`;
        account.Cards.forEach(card => {
            cardsHTML += `
                <div class="card mt-2">
                    <div class="card-body">
                        <h6 class="card-title">Card Number: ${card.Card_Number}</h6>
                        <h6 class="card-title">CVV: ${card.CVV.toString().padStart(3, '0')}</h6>
                        <h6 class="card-title">PIN: ${card.PIN.toString().padStart(4, '0')}</h6>
                        <p class="card-text">Expiration Date: ${card.Expiration_Date}</p>
                        <button class="btn btn-secondary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#nfcModal" data-iban="${account.IBAN}">Use Card</button>
                        <button class="btn btn-danger btn-sm mt-2 delete-card" data-card-number="${card.Card_Number}" data-iban="${account.IBAN}">Delete Card</button>
                    </div>
                </div>
            `;
        });
    } else {
        cardsHTML += `<p>No cards linked to this account.</p>`;
    }

    return `
        <div class="col-md-6 mb-4" data-iban="${account.IBAN}">
            <div class="card position-relative">
                <div class="card-body">
                    <h5 class="card-title fw-bold">${account.Account_Name}</h5>
                    <p class="card-text">IBAN: ${formatIBAN(account.IBAN)}</p>
                    <h6 class="fw-bold">Balance: ${account.Balance}</h6>
                    <div class="account-cards">${cardsHTML}</div>
                    <button class="btn btn-outline-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#addCardModal" data-iban="${account.IBAN}">Add Card</button>
                    <button class="btn btn-outline-success btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#depositModal" data-iban="${account.IBAN}" class="deposit-btn">Deposit</button>
                    <button class="btn btn-outline-danger btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#withdrwalModal" data-iban="${account.IBAN}" class="withdraw-btn">Withdraw</button>
                    <button class="btn btn-outline-secondary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#transactionModal" data-iban="${account.IBAN}" class="transaction-btn">Transaction</button>
                </div>
                <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 delete-account" data-account-iban="${account.IBAN}">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
}

// Function to handle account deletion
function deleteAccount(accountIBAN) {
    if (confirm('Are you sure you want to delete this account?')) {
        fetch('../php/delete_account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ accountIBAN }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "../pages/account_page.php";
            } else {
                alert(`Error: ${data.error}`);
            }
        })
        .catch(error => console.error('Error deleting account:', error));
    }
}

function fetchUserData() {
    fetch('../php/fetch_user.php')
    .then(response => response.json())
    .then(data => {
    if (data.error) {
        console.log('Error:', data.error);
    } else {
        // Populate the modal form with the current user's data
        document.querySelector('#settingsModal input[name="username"]').value = data.Username;
        document.querySelector('#settingsModal input[name="first_name"]').value = data.First_Name;
        document.querySelector('#settingsModal input[name="last_name"]').value = data.Last_Name;
        document.querySelector('#settingsModal input[name="email"]').value = data.Email;
        document.querySelector('#settingsModal input[name="phone"]').value = data.Phone_Number;
        document.querySelector('#settingsModal input[name="address"]').value = data.Address;
    }
    })
    .catch(error => console.error('Error fetching user data:', error));
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Format IBAN for better readability
function formatIBAN(iban) {
    return iban.toString().replace(/(\d{4})/g, '$1 ').trim();
}