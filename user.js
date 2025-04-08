export default class User{
    constructor(userID){
        this.userID = userID;
        this.usersVariables = [];
        this.userUrl = "/Scriptz/BackEnd/registration.php"
    }
    async getUser(userID){
        try{
                const users = await fetch(this.userUrl);
                if(!users){
                    throw new Error(`Failed to fetch user data: ${users.status}`);
                }
                return this.user = users.some(user => user.RegID === userID);
        } catch(error){
            console.error("Error fetching user:", error);
            return [];
        }
    }
    async userVariables(userID){
        const user = await this.getUser(userID);
        if(!user){
            throw new Error("User not found");
        }
        return this.usersVariables = {
            userID: user.RegID,
            name1: user.Name1,
            name2: user.Name2,
            regtype: user.RegType,
            gender: user.Gender,
            location: user.Location
        };
    }
}