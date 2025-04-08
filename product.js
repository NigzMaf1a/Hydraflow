export default class Product{
    constructor(productID, productName, productDescription, productPrice, productUnits, productImage) {
        this.productID = productID;
        this.productName = productName;
        this.productDescription = productDescription;
        this.productPrice = productPrice;
        this.productUnits = productUnits;
        this.productImage = productImage;
        this.availableProducts = [];
        this.searchedProducts = [];
        this.getProductDetails();
    }

    async getProductDetails(method = "GET", bodyData = null, productID = null) {
        try {
            const url = productID 
                ? `/Scriptz/BackEnd/getProducts.php?ProductID=${productID}` 
                : "/Scriptz/BackEnd/getProducts.php";
    
            const options = {
                method,
                headers: {
                    "Content-Type": "application/json",
                },
            };
    
            // If POST or PUT method, attach body data
            if ((method === "POST" || method === "PUT") && bodyData) {
                options.body = JSON.stringify(bodyData);
            }
    
            // API request
            const response = await fetch(url, options);
            if (!response.ok) throw new Error(`Error in ${method} request`);
    
            return await response.json();
        } catch (error) {
            console.error(`Error in getProductDetails (${method}):`, error);
            return null; // Return null if an error occurs
        }
    }
      
    
    async getAvailableProducts(){
        try{
            const response = await this.getProductDetails("GET");
            if(response.ok){
                this.availableProducts = response.filter(product => product.productUnits !== 0);
                return this.availableProducts;
            } else {
                throw new Error('Error fetching available products');
            }
        } catch {
            console.error('Error fetching available products:', error);
            return []; // Return empty array if an error occurs
        }
    }
    async searchProducts(criteria){
        try{
            const response = await this.getAvailableProducts();
            if(response.ok){
                this.searchedProducts = response.filter(product =>product.productName ===criteria);
                return this.searchedProducts;
            } else {
                throw new Error('Error searching products');
            }
        } catch (error){
            console.error('Error searching products:', error);
            return []; // Return empty array if an error occurs
        }
    }
    async addProduct(name, description, price, units, image) {
        const productData = {
            productName: name,
            productDescription: description,
            productPrice: price,
            productUnits: units,
            productImage: image
        };

        try {
            const response = await this.getProductDetails("POST", productData);
            
            if (!response) {
                throw new Error("Failed to add product. No response from server.");
            }
            
            return response; // Returning response for further handling if needed
        } catch (error) {
            console.error("Error adding product:", error);
            return null; // Returning null to indicate failure
        }
    }
    async deleteProduct(name){
        try{
            const response = await this.getProductDetails("GET");
            if(response.ok){
                const productToDelete = response.find(product => product.ProductName === name);
                if(productToDelete){
                    const response = await this.getProductDetails("DELETE", null, productToDelete.productID);
                    if(response.ok){
                        return true;
                    } else {
                        throw new Error('Error deleting product');
                    }
                } else {
                    throw new Error('Product not found');
                }
            }
        } catch(error){
            console.error('Error deleting product:', error);
            return false; // Return false to indicate failure
        }
    }
    async updateProduct(name, description, price, unit, image){
        const updatedProduct = {
            productName: name,
            productDescription: description,
            productPrice: price,
            productUnits: unit,
            productImage: image
        };
        try{
            const response = await this.getProductDetails("GET");
            if(response.ok){
                const productToUpdate = response.find(product => product.ProductName === name);
                if(productToUpdate){
                    const response = await this.getProductDetails("PUT", updatedProduct, productToUpdate.ProductID);
                    if(response.ok){
                        return true;
                    } else {
                        throw new Error('Error updating product');
                    }
                } else {
                    throw new Error('Product not found');
                }
            }
        } catch(error) {
            console.error('Error updating product:', error);
            return false; // Return false to indicate failure
        }
    }

}