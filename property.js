import PropertyUnits from '/Scriptz/FrontEnd/unit.js';

export default class Property {
    constructor() {
        this.unit = new PropertyUnits();
        this.allProperties = [];
        this.clientsProperties = [];
        this.propertyUnits = [];
        this.url = "/Scriptz/BackEnd/getProperties.php";
    }

    async fetchData(url = this.url, method = "GET", body = null) {
        try {
            const options = {
                method,
                headers: { "Content-Type": "application/json" }
            };

            if (body) options.body = JSON.stringify(body);

            const response = await fetch(url, options);
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            return { success: true, data: await response.json() };
        } catch (error) {
            console.error(`Error with ${method} request to ${url}:`, error);
            return { success: false, error: error.message };
        }
    }

    async getAllProperties() {
        const result = await this.fetchData();
        if (result.success) {
            this.allProperties = result.data;
        }
        return this.allProperties;
    }

    async clientProperties(clientID) {
        const properties = await this.getAllProperties();
        this.clientsProperties = properties.filter(property => property.clientID === clientID);
        return this.clientsProperties;
    }

    async clientUnits(propertyID) {
        const units = await this.unit.getUnits(propertyID);
        this.propertyUnits = units.filter(unit => unit.PropertyID === propertyID);
        return this.propertyUnits;
    }

    async addProperty(clientID, propertyName, propertyType, units) {
        try {
            const result = await this.fetchData(this.url, "POST", {
                clientID,
                propertyName,
                propertyType,
                units
            });
            if (result.success) {
                console.log("Property added successfully!");
            } else {
                console.error("Error adding property:", result.error);
            }
        } catch (error) {
            throw new Error('Failed to add property');
        }
    }

    async addUnit(propertyID, unitName) {
        try {
            const unitData = {
                PropertyID: propertyID,
                UnitName: unitName
            };
            const result = await this.unit.createUnit(unitData);
            if (result.success) {
                console.log("Unit added successfully!");
            } else {
                console.error("Error adding unit:", result.error);
            }
        } catch (error) {
            console.error("Failed to add unit", error);
        }
    }

    async updateProperty(propertyID, clientID, propertyName, propertyType, units) {
        try {
            const properties = await this.clientProperties(clientID);
            const propertyExists = properties.some(property => property.PropertyID === propertyID);
            
            if (!propertyExists) {
                console.error("Property not found for update");
                return;
            }
            
            const result = await this.fetchData(this.url, "PUT", {
                propertyID,
                clientID,
                propertyName,
                propertyType,
                units
            });
            if (result.success) {
                console.log("Property updated successfully!");
            } else {
                console.error("Error updating property:", result.error);
            }
        } catch (error) {
            console.error("Failed to update property", error);
        }
    }
    async updateUnit(unitID, propertyID, unitName) {
        try {
            // Fetch all units for the given property
            const units = await this.unit.getUnits(propertyID);
    
            // Check if the unit exists
            const unitExists = units.some(unit => unit.UnitID === unitID && unit.PropertyID === propertyID);
            if (!unitExists) {
                console.error("Unit not found for update");
                return;
            }
    
            // Await the update request
            const result = await this.unit.updateUnit({
                unitID,
                propertyID,
                unitName
            });
    
            // Check if update was successful
            if (result.success) {
                console.log("Unit updated successfully!");
            } else {
                throw new Error("Error updating unit");
            }
        } catch (error) {
            console.error("Failed to update unit", error);
        }
    }
    async deleteProperty(clientID, propertyID) {
        try {
            const properties = await this.clientProperties(clientID);
            const propertyExists = properties.some(property => property.PropertyID === propertyID);
            
            if (!propertyExists) {
                console.error("Property not found for deletion");
                return;
            }
            
            const result = await this.fetchData(`${this.url}/${propertyID}`, "DELETE");
            if (result.success) {
                console.log("Property deleted successfully!");
            } else {
                console.error("Error deleting property:", result.error);
            }
        } catch (error) {
            console.error("Failed to delete property", error);
        }
    }
    async deleteUnit(unitID, propertyID) {
        try{
            // Fetch all units for the given property
            const units = await this.unit.getUnits(propertyID);
            const unitExists = units.some(unit => unit.UnitID === unitID && unit.PropertyID === propertyID);
            if (!unitExists) {
                console.error("Unit not found for deletion");
                return;
            }
            const result = await this.unit.deleteUnit(unitID);
            if (result.success) {
                console.log("Unit deleted successfully!");
            } else {
                throw new Error("Error deleting unit");
            }
        } catch (error){
            console.error("Failed to delete unit", error);
        }
    }
    
}
