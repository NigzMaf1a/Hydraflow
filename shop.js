export class Shop{
    constructor(shopID, shopName, shopDescription, shopAddress, shopPhone, shopEmail, shopLogo, shopImage, shopStatus, shopType){
        this.shopID = shopID;
        this.shopName = shopName;
        this.shopDescription = shopDescription;
        this.shopAddress = shopAddress;
        this.shopPhone = shopPhone;
        this.shopEmail = shopEmail;
        this.shopLogo = shopLogo;
        this.shopImage = shopImage;
        this.shopStatus = shopStatus;
        this.shopType = shopType;
    }

    async getShop(){
        try{
            const response = await fetch("/Scriptz/BackEnd/getShopDetail.php");
            if(!response.ok){
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();
            return data;
        }catch(error){
            console.error("Error fetching shop detail:", error);
        }
    }
    async addProduct(productData) {
        try {
            const response = await fetch("/Scriptz/BackEnd/addProduct.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(productData) // Convert product data to JSON
            });
    
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
    
            const data = await response.json(); // Await JSON response
            return data;
        } catch (error) {
            console.error("Error adding product:", error);
            throw error; // Rethrow to handle it where the function is called
        }
    }
    async getProducts(){
        try{
            const response = await fetch("/Scriptz/BackEnd/getShopProducts.php");
            if(!response.ok){
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();
            return data;
        }catch(error){
            console.error("Error fetching shop detail:", error);
        }
    }
    async updateProduct(method = "GET", productData = null) {
        try {
            const options = {
                method,
                headers: {
                    "Content-Type": "application/json"
                }
            };
    
            // Add body only if it's a POST request
            if (method === "POST" && productData) {
                options.body = JSON.stringify(productData);
            }
    
            const response = await fetch("/Scriptz/BackEnd/shopUpdateProduct.php", options);
    
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
    
            return await response.json(); // Return parsed response data
        } catch (error) {
            console.error(`Error with ${method} request:`, error);
            throw error;
        }
    }
    
    async deleteProduct(){
        try{
            const response = await fetch("/Scriptz/BackEnd/deleteProduct.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ productId: productId })
            });
            if(!response.ok){
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();
            return data;
        }catch(error){
            console.error("Error deleting product:", error);
        }
    }
    async searchProducts(query) {
        try {
            const productList = await this.getProducts(); // Fetch products
    
            if (!productList || productList.length === 0) {
                console.log("No products found.");
                return [];
            }
    
            // Filter products based on the search query (assuming they have a `name` property)
            const filteredProducts = productList.filter(product =>
                product.name.toLowerCase().includes(query.toLowerCase())
            );
    
            return filteredProducts;
        } catch (error) {
            console.error("Error searching products:", error);
        }
    }
}