export class Login {
    constructor(email, password) {
        this.email = email;
        this.password = password;
        this.logInThen();
        console.log("Iko Nini!");
    }

    async logInThen() {
        try {
            const response = await fetch('/HydraFlow/Scriptz/BackEnd/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ Email: this.email, Password: this.password })
            });
            
            const data = await response.json();
            console.log("Imefika Hapa!");
            
            if (data.success) {
                switch (data.RegType) {
                    case 'Admin':
                        window.location.href = 'Pages\managerDashboard.php';
                        break;
                    case 'Manager':
                        window.location.href = 'manager_dashboard.php';
                        break;
                    case 'Client':
                        window.location.href = 'client_dashboard.php';
                        break;
                    case 'Plumber':
                        window.location.href = 'plumber_dashboard.php';
                        break;
                    case 'Mason':
                        window.location.href = 'mason_dashboard.php';
                        break;
                    default:
                        alert('Unknown user role.');
                }
            } else {
                alert(data.error || 'Login failed.');
            }
        } catch (error) {
            console.error('Login error:', error);
            alert('An error occurred. Please try again.');
        }
    }
}
