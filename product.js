export default class Product {
    constructor() {
        this.productUrl = "/Scriptz/BackEnd/getProducts.php";
    }

    async _sendRequest(payload, successMessage) {
        try {
            const response = await fetch(this.productUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) throw new Error(`Server responded with status ${response.status}`);

            const data = await response.json();
            return { success: true, data, message: successMessage };
        } catch (error) {
            console.error(`Error in action "${payload.action}":`, error);
            return { success: false, data: [], message: error.message };
        }
    }

    async addProduct(productName, productDescription, price, productUnits, productImage, available = 'Yes') {
        const payload = {
            action: 'create',
            productName,
            productDescription,
            price,
            productUnits,
            productImage,
            available
        };
        return await this._sendRequest(payload, 'Product added successfully');
    }

    async getProducts() {
        const payload = { action: 'read' };
        return await this._sendRequest(payload, 'Products retrieved successfully');
    }

    async updateProduct(productID, productName, productDescription, price, productUnits, productImage, available = 'Yes') {
        const payload = {
            action: 'update',
            productID,
            productName,
            productDescription,
            price,
            productUnits,
            productImage,
            available
        };
        return await this._sendRequest(payload, 'Product updated successfully');
    }

    async deleteProduct(productID) {
        const payload = {
            action: 'delete',
            productID
        };
        return await this._sendRequest(payload, 'Product deleted successfully');
    }
}
