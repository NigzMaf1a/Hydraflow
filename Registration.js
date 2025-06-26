// Import required modules
import { getCredentials } from './logzGet';
import { reRegUser } from './settings';
////////////

export class Registration {
    constructor(RegID, Name1, Name2, PhoneNo, Email, Password, Gender, RegType, Location, accStatus) {
        this.RegID = RegID;
        this.Name1 = Name1;
        this.Name2 = Name2;
        this.PhoneNo = PhoneNo;
        this.Email = Email;
        this.Password = Password;
        this.Gender = Gender;
        this.RegType = RegType;
        this.Location = Location;
        this.accStatus = accStatus;
    }

    registerUser = async () => {
        const credentials = await getCredentials(
            Name1,
            Name2,
            PhoneNo,
            Email,
            Password1,
            Password2,
            Gender,
            RegType,
            Location
        );
    
        if (credentials) {
            const registration = new Registration(...credentials);
            await registration.storeUser(); // Save the user data
            console.log('User registered successfully:', registration.getUser());
        }
    };

    // Converts the registration instance to an array
    toArray() {
        return [
            this.RegID,
            this.Name1,
            this.Name2,
            this.PhoneNo,
            this.Email,
            this.Password,
            this.Gender,
            this.RegType,
            this.Location,
            this.accStatus,
        ];
    }

    async storeUser() {
        const userData = this.toArray(); // Convert the object into an array
        const endpointUrl = '/registration.php';
    
        try {
            const response = await fetch(endpointUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    operation: 'create', // Specify the operation type
                    table: 'Registration', // Specify the table name
                    data: userData, // Pass the user data
                }),
            });
    
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
    
            const result = await response.json();
    
            if (result.success) {
                console.log('User successfully stored:', result.message);
                return result;
            } else {
                console.error('Error storing user:', result.message);
                return null;
            }
        } catch (error) {
            console.error('Error during user storage:', error);
            return null;
        }
    }
    

    // Retrieves user details
    getUser() {
        return this.toArray();
    }

    // Edits a user's information
    async editUser(updatedDetails) {
        const credentials = await reRegUser();
        // Apply updates to the class properties
        Object.assign(this, updatedDetails);
        console.log('User updated:', this.toArray());
        return this.toArray();
    }

    async deleteUser(){}
}

export class Finances extends Registration {}
